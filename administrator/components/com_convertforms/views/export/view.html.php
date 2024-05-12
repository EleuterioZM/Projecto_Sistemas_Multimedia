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
defined('_JEXEC') or die('Restricted access');
 
use ConvertForms\Export;

/**
 * Templates View
 */
class ConvertFormsViewExport extends JViewLegacy
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    public function display($tpl = null) 
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $viewLayout = $input->get('layout', 'default');

        $this->tmpl = $input->get('tmpl');
        $this->baseURL = 'index.php?option=com_convertforms&view=export';
        $this->start_over_link = $this->baseURL . ($this->tmpl == 'component' ? '&tmpl=component' : '');

        switch ($viewLayout)
        {
            case 'completed':
                $file = $input->get('filename');

                if (!Export::exportFileExists($file))
                {
                    Export::error(JText::_('COM_CONVERTFORMS_EXPORT_ERROR_CANT_FIND_FILE'));
                }

                $this->download_link = 'index.php?option=com_convertforms&task=export.download&filename=' . $file;
                $this->total_submissions_exported = $input->get('total');
                $this->export_type = $input->get('export_type');
                break;

            case 'progress':
                JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

                try
                {
                    $data = Export::export($input->getArray());

                    $pagination = $data['pagination'];
                    $options = $data['options'];
                    
                    $totalProcessedSoFar = $pagination->pagesCurrent * $pagination->limit;
                    $totalProcessedSoFar = $pagination->total > $totalProcessedSoFar ? $totalProcessedSoFar : $pagination->total;
                    
                    $this->processed = $totalProcessedSoFar;
                    $this->total = $pagination->total;

                    if ($pagination->pagesCurrent < $pagination->pagesTotal)
                    {
                        $new_url = \JURI::getInstance();
                    
                        $new_url->setVar('offset', $options['offset'] + $options['limit']);
                        $new_url->setVar('processed', $this->processed);
                        $new_url->setVar('total', $this->total);
                    
                        header('Refresh:0; url=' . $new_url->toString());
                    } else 
                    {
                        // Export completed
                        $optionsQuery = http_build_query(array_filter([
                            'total' =>  $this->total,
                            'filename' => $options['filename'],
                            'export_type' => $options['export_type'],
                            'tmpl' => $this->tmpl
                        ]));
                    
                        $app->redirect($this->baseURL . '&layout=completed&' . $optionsQuery);
                    }

                } catch (\Throwable $th)
                {
                    Export::error($th->getMessage());
                }

                break;

            case 'error':
                $this->error = $input->get('error', '', 'RAW');
                break;
            
            default:
                $form = new JForm('export');
                $form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/export_submissions.xml');
                $form->bind($app->input->getArray());

                $this->form = $form;

                break;
        }

        if ($this->tmpl == 'component')
        {
            JFactory::getDocument()->addStyleDeclaration('
                body {
                    background:none !important;
                }
            ');
        } else 
        {
            $this->addToolBar();
        }

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            $app->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Display the template
        parent::display($tpl);
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        JToolBarHelper::title(JText::_('COM_CONVERTFORMS') . ": " . JText::_('COM_CONVERTFORMS_LEADS_EXPORT'));
    }
}
