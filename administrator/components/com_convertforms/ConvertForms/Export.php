<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

use Joomla\Registry\Registry;
use ConvertForms\Helper;
use NRFramework\File;

defined('_JEXEC') or die('Restricted access');

/**
 * Export submissions to CSV and JSON
 */
class Export
{
    /**
     * Export submissions to CSV or JSON file
     *
     * @param  array $options   The export options
     *
     * @return array
     */
    public static function export($options)
    {
        // Increase memory size and execution time to prevent PHP errors on datasets > 20K
        set_time_limit(300); // 5 Minutes
        ini_set('memory_limit', '-1');

        $options = new Registry($options);
        
        // Load submissions model
        \JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/models');
        $model = \JModelLegacy::getInstance('Conversions', 'ConvertFormsModel', ['ignore_request' => true]);

        // When we're exporting certain IDs, there's no need to check the state.
        if (strpos($options->get('filter_search', ''), 'id:') !== false || $options->get('filter_state') == '*')
        {
            $filter_state = 'skip';
        } else 
        {
            $filter_state = $options->get('filter_state');
        }

        $model->setState('filter.state', $filter_state);
        $model->setState('filter.join_campaigns', 'skip');
        $model->setState('filter.join_forms', 'skip');
        $model->setState('list.limit', $options->get('limit', 0));
        $model->setState('list.start', $options->get('offset', 0));
        $model->setState('list.direction', 'asc');
        $model->setState('filter.search', $options->get('filter_search'));
        $model->setState('filter.form_id', $options->get('filter_form_id'));
        $model->setState('filter.period', $options->get('filter_period', ''));
        $model->setState('filter.created_from', $options->get('filter_created_from', ''));
        $model->setState('filter.created_to', $options->get('created_to', ''));

        // Proceed only if we have submissions
        if (!$submissions = $model->getItems())
        {
            $error = \JText::sprintf('COM_CONVERTFORMS_NO_RESULTS_FOUND', strtolower(\JText::_('COM_CONVERTFORMS_SUBMISSIONS')));
            throw new \Exception($error);
        }

        foreach ($submissions as $key => &$submission)
        {
            self::prepareSubmission($submission);
        }

        $export_type = $options->get('export_type', 'csv');

        $pagination = $model->getPagination();
        $firstRun = $pagination->pagesCurrent == 1;
        $filename = File::getTempFolder() . $options->get('filename', 'convertforms_submissions.' . $export_type);

        switch ($export_type)
        {
            case 'json':
                self::toJSON($submissions, $filename, !$firstRun);
                break;

            default:
                $excel_security = (bool) Helper::getComponentParams()->get('excel_security', true);
                self::toCSV($submissions, $filename, !$firstRun, $excel_security);
                break;
        }

        return [
            'options'    => $options,
            'pagination' => $pagination
        ];
    }

    /**
     *  Get a key value array with submission's submitted data
     *
     *  @param   object  $submission  The submission object
     *
     *  @return  array
     */
    private static function prepareSubmission(&$submission)
    {
        $result = [
            'id' => $submission->id,
            'created' => $submission->created,
            'state' => $submission->state
        ];

        foreach ($submission->prepared_fields as $field_name => $field)
        {
            // Always return the raw value and let the export type decide how the value should be displayed.
            $result[$field_name] = $field->value_raw;
        }

        $submission = $result;
    }

    /**
     * Create a JSON file with given data
     *
     * @param   array     $data           The data to populate the file   
     * @param   string    $destination    The path where the store the JSON file
     * @param   bool      $append         If true, given data will be appended to the end of the file.
     *
     * @return  void
     */
    private static function toJSON($data, $destination, $append = false)
    {
        $content = \JFile::exists($destination) ? (array) json_decode(file_get_contents($destination), true) : [];
        $content = $append ? array_merge($content, $data) : $data;
        $content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Save the file
        \JFile::write($destination, $content);
    }

    /**
     *  Create a CSV file with given data
     *
     *  @param   array     $data            The data to populate the file   
     *  @param   string    $destination     The path where the store the CSV file
     *  @param   bool      $append          If true, given data will be appended to the end of the file.
     *  @param   boolean   $excel_security  If enabled, certain row values will be prefixed by a tab to avoid any CSV injection.
     *
     *  @return  void
     */
    private static function toCSV($data, $destination, $append = false, $excel_security = true)
    {
        $output = fopen($destination, $append ? 'a' : 'w');

        if (!$append)
        {
            // Support UTF-8 on Microsoft Excel
            fputs($output, "\xEF\xBB\xBF");
            
            // Add column names in the first line
            fputcsv($output, array_keys($data[0]));
        }

        foreach ($data as $key => $row)
        {
            // Prevent CSV Injection: https://vel.joomla.org/articles/2140-introducing-csv-injection
            if ($excel_security)
            {
                foreach ($row as &$value)
                {
                    $value = is_array($value) ? implode(', ', $value) : $value;

                    $firstChar = substr($value, 0, 1);

                    // Prefixe values starting with a =, +, - or @ by a tab character
                    if (in_array($firstChar, array('=', '+', '-', '@')))
                    {
                        $value = '    ' . $value;
                    }
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
    }

    /**
     * Redirects to the error layout and displays the given error message
     *
     * @param  string $error_message
     *
     * @return void
     */
    public static function error($error_message)
    {
        $app = \JFactory::getApplication();

        $optionsQuery = http_build_query(array_filter([
            'option' => 'com_convertforms',
            'view'   => 'export',
            'layout' => 'error',
            'error'  => $error_message,
            'tmpl'   => $app->input->get('tmpl')
        ]));
    
        $app->redirect('index.php?' . $optionsQuery);
    }

    /**
     * Verifies the export file does exist
     *
     * @param string $filename
     *
     * @return bool 
     */
    public static function exportFileExists($filename)
    {
        return \JFile::exists(File::getTempFolder() . $filename);
    }

    /**
     * Adds the Export popup to the page which can be triggered by toolbar buttons.
     *
     * @return void
     */
    public static function renderModal()
    {
        \JHtml::script('com_convertforms/export.js', ['relative' => true, 'version' => 'auto']);

        \JFactory::getDocument()->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function() {
                new exportModal("'.\JFactory::getApplication()->input->get('view') . '");
            });
        ');

        $html =  \JHtml::_('bootstrap.renderModal', 'cfExportSubmissions', [
            'backdrop' => 'static'
        ]);

        // This is the only way to add a custom CSS class to the popup container
        echo str_replace('modal hide', 'modal hide transparent', $html);
    }
}