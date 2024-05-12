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
 
jimport('joomla.application.component.view');

/**
 * Item View
 */
class ConvertFormsViewForm extends JViewLegacy
{
    /**
     * display method of Item view
     * @return void
     */
    public function display($tpl = null) 
    {
        $app = JFactory::getApplication();

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            $app->enqueueMessage(implode('\n', $errors), 'error');
            return false;
        }

        $layout = $app->input->get('layout', 'default');

        if ($layout == 'preview')
        {
            //$data = json_decode($app->input->get('jform', null, 'RAW'));

            $input = json_decode(file_get_contents('php://input'));
            $data = json_decode($input);

            $xx = new JRegistry();

            foreach ($data as $value)
            {
                $key = str_replace(['jform[', ']', '['], ['', '', '.'], $value->name);
                $xx->set($key, $value->value);
            }

            $xx = $xx->toArray();

            $this->data = $this->getModel('Form')->validate('jform', $xx);
            $this->data['params'] = json_decode($this->data['params'], true);
            $this->data['fields'] = $this->data['params']['fields'];

            unset($this->data['params']['fields']);

            $this->form = ConvertForms\Helper::renderForm($this->data);     
        }
        
        if ($layout == 'field')
        {
            $formControl = urldecode($app->input->get('formcontrol', null, 'RAW'));
            $loadData    = $app->input->get('field', array(), 'ARRAY');

            $this->field = ConvertForms\FieldsHelper::getFieldClass($loadData['type'])->getOptionsForm($formControl, $loadData);
        }

        // Display the template
        parent::display($tpl);
    }
}