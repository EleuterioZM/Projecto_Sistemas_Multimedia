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

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;
use NRFramework\Cache;
use Joomla\Registry\Registry;

class Form
{
    /**
     * Returns a form object from database
     *
     * @param  integer     $id             The ID of the form
     * @param  bool        $only_inputs    If true, fields that doesn't have an input element such as HTML and reCAPTCHA, won't be returned.
     *
     * @return mixed       Null on failure, array on success
     */
    public static function load($id, $only_inputs = false, $ignore_state = false)
    {
        if (!$id)
        {
            return;
        }
        
        $hash = 'convertforms_' . $id . '_' . (string) $only_inputs;
        if (Cache::has($hash))
        {
            return Cache::get($hash);
        }

        // Get a db connection.
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
         
        $query->select('*')
            ->from($db->quoteName('#__convertforms'))
            ->where($db->quoteName('id') . ' = '. (int) $id);
        
        if (!$ignore_state)
        {
            $query->where($db->quoteName('state') . ' = 1');
        }

        $db->setQuery($query);

        if (!$form = $db->loadAssoc())
        {
            return;
        }

        $form['params'] = json_decode($form['params'], true);

        // Make 3rd party developer's life easier by setting field's name as the array key for faster code manipulation through PHP scripts.
        foreach ($form['params']['fields'] as $key => $field)
        {
            // This is useful when we would like to skip non-interactive fields such as HTML, reCAPTCHA e.t.c.
            if ($only_inputs && !isset($field['name']))
            {
                unset($form['params']['fields'][$key]);
                continue;
            }

            $name = isset($field['name']) ? $field['name'] : $key;

            unset($form['params']['fields'][$key]);
            $form['params']['fields'][$name] = $field;
        }

        $form['fields'] = $form['params']['fields'];
        unset($form['params']['fields']);

        return Cache::set($hash, $form);
    }

    /**
     * Get settings of a form field
     *
     * @param   integer     $form_id      The id of the form
     * @param   integer     $field_key    The id of the field
     *
     * @return  mixed       Null on failure, Registry object on success
     */
    public static function getFieldSettingsByKey($form_id, $field_key)
    {
        if (!$form = self::load($form_id))
        {
            return;
        }

		$found = array_filter($form['fields'], function($field) use ($field_key)
		{
            return ($field['key'] == $field_key);
        });

        return new Registry(array_pop($found));;
    }

    /**
     * Run user-defined PHP scripts on certain form events.
     * 
     * The available events are:
     * 
     * formprepare:         Called on form data prepare.
     * formdisplay:         Called on form display.
     * formsubmission:      Called on form process.
     * afterformprocess:    Called after the form has been processed and the submission is saved into the database.
     *
     * @param   Integer $form_id        The form's ID
     * @param   String  $script_name    The script name to run
     * @param   Array   $payload        The data passed as argument to the PHP script. By reference.
     *
     * @return  void
     */
    public static function runPHPScript($form_id, $script_name, &$payload)
    {
        // Only on the front-end
        if (\JFactory::getApplication()->isClient('administrator'))
        {
            return;
        }

        if (!$form = self::load($form_id))
        {
            return;
        }

        // Abort, if the script is not found
        if (!isset($form['params']['phpscripts'][$script_name]))
        {
            return;
        }

        // Abort, if the script is empty
        if (!$php_script = $form['params']['phpscripts'][$script_name])
        {
            return;
        }

        if (!isset($payload['form']))
        {
            $payload['form'] = $form;
        }

        $payload['script_name'] = $script_name;

        try
        {
            $executer = new \NRFramework\Executer($php_script, $payload);
            $executer->run();
        } catch (\Throwable $th)
        {
            $error = $th->getMessage() . ' - ' . $th->getFile() . ' on line ' . $th->getLine();
           
            // Log error
            Helper::triggerError($error, 'PHP Script', $form_id);

            // Re throw exception
            throw new \Exception($th->getMessage());
        }
    }

    /**
     * Return the total number of form submissions
     *
     * @param   integer $form_id    The form's ID
     *
     * @return  integer
     */
    public static function getSubmissionsTotal($form_id)
    {
        if (!$form_id)
        {
            return;
        }

        $hash = md5('cf_count_' . $form_id);

		if (Cache::has($hash))
		{
			return Cache::get($hash);
        }
        
        \JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/models');

		$model = \JModelLegacy::getInstance('Conversions', 'ConvertFormsModel', ['ignore_request' => true]);

		$model->setState('filter.form_id', (int) $form_id);
		$model->setState('filter.state', [1, 2]);
		$model->setState('filter.join_campaigns', 'skip');
		$model->setState('filter.join_forms', 'skip');

        $query = $model->getListQuery();

		$query->clear('select');
        $query->select('count(a.id)');

		$db = \JFactory::getDbo();
        $db->setQuery($query);

		$count = $db->loadResult();

	    return Cache::set($hash, $count);
    }
}