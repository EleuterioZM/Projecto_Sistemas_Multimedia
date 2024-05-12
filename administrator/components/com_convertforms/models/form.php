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

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * Item Model
 */
class ConvertFormsModelForm extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Form', $prefix = 'ConvertFormsTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true) 
    {
        // Get the form.
        $form = $this->loadForm('com_convertforms.form', 'form', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) 
        {
            return false;
        }

        return $form;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $files = array(
            "form_design"
        );

        foreach ($files as $key => $value)
        {
            $form->loadFile($value, false);
        }

        // Call all ConvertForms plugins
        JPluginHelper::importPlugin('convertforms');

        // load translation strings
        $this->loadTranslations();

        parent::preprocessForm($form, $data, $group);
    }
    
    /**
     * Enqueues translations for the back-end
     * 
     * @return  void
     */
    private function loadTranslations()
    {
        JText::script('COM_CONVERTFORMS_SUBMISSION_ID');
        JText::script('COM_CONVERTFORMS_SUBMISSION_DATE');
        JText::script('COM_CONVERTFORMS_SUBMISSION_USER_ID');
        JText::script('COM_CONVERTFORMS_SUBMISSION_CAMPAIGN_ID');
        JText::script('COM_CONVERTFORMS_SUBMISSION_FORM_ID');
        JText::script('COM_CONVERTFORMS_SUBMISSION_VISITOR_ID');
        JText::script('COM_CONVERTFORMS_SUBMISSIONS_COUNT');
        JText::script('COM_CONVERTFORMS_ALL_FIELDS');
        JText::script('COM_CONVERTFORMS_ALL_FIELDS_NO_LABELS');
        JText::script('COM_CONVERTFORMS_ALL_FILLED_ONLY_FIELDS');
    }

    /**
     *  Prepare form fieldsets by tab into an array
     *
     *  @return  array
     */
    public function getTabs()
    {
        $form = $this->getForm();

        // Tabs
        $tabs = array(
           "fields" => array(
                "label" => "COM_CONVERTFORMS_FIELDS",
                "icon"  => "icon-list-2"
            ),
            "design" => array(
                "label" => "COM_CONVERTFORMS_DESIGN",
                "icon"  => "icon-picture"
            ),
            "behavior"   => array(
                "label" => "COM_CONVERTFORMS_BEHAVIOR",
                "icon"  => "icon-options"
            ),
            "conversion" => array(
                "label" => "COM_CONVERTFORMS_SUBMISSION",
                "icon"  => "icon-generic"
            )
        );

        foreach ($tabs as $key => $tab)
        {
            $tabs[$key]["fields"] = $this->getFieldsetbyTab($form, $key);
        }

        return $tabs;
    }

    /**
     *  Return form fieldsets by tab attribute
     *
     *  @param   JForm   $form  The form
     *  @param   string  $tab   The tab name
     *
     *  @return  array          Found fieldsets
     */
    private function getFieldsetbyTab($form, $tab)
    {
        $fieldsets = $form->getXML()->fieldset;

        $found = array();

        foreach ($fieldsets as $key => $fieldset)
        {
            if ($tab == (string) $fieldset["tab"])
            {
                $found[] = $fieldset;
            }
        }

        return $found;
    }

    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {

            $params = $item->params;

            if (is_array($params) && count($params)) {

                foreach ($params as $key => $value) {
                    if (!isset($item->$key) && !is_object($value)) {
                        $item->$key = $value;
                    }
                }

                unset($item->params);
            }
        }

        return $item;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_convertforms.edit.form.data', array());

        if (!empty($data))
        {
            $params = json_decode($data['params'], true);
            $data   = array_merge($data, $params);
            unset($data['params']);

            return $data;
        }

        // Load existing form template
        if ($template = JFactory::getApplication()->input->get('template', null))
        {
            return ConvertForms\Helper::loadFormTemplate($template);
        }

        if (empty($data))
        {
            $data = $this->getItem();

            if (is_null($data->name))
            {
                $data->name = JText::_('COM_CONVERTFORMS_UNTITLED_BOX');
            }
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  The form data.
     *
     * @return  boolean  True on success.
     * @since   1.6
     */
    public function save($data)
    {
        $params = json_decode($data['params'], true);

        if (is_null($params))
        {
            $params = array();
        }

        // Validate field options
        foreach ($params['fields'] as $key => &$field)
        {
            if (!isset($field['type']))
            {
                continue;
            }

            $class = ConvertForms\FieldsHelper::getFieldClass($field['type']);

            if (!method_exists($class, 'onBeforeFormSave'))
            {
                continue;
            }

            if (!$class->onBeforeFormSave($this, $params, $field))
            {
                return false;
            }
        }

        $data['params'] = json_encode($params);

        return parent::save($data);
    }

    /**
     * Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        // Prevent saving form with an empty name
        if (empty($data["name"]))
        {
            $data["name"] = JText::_("COM_CONVERTFORMS_UNTITLED_BOX");
        }

        $newdata = array();
        
        $params = array();
        $this->_db->setQuery('SHOW COLUMNS FROM #__convertforms');
        $dbkeys = $this->_db->loadObjectList('Field');
        $dbkeys = array_keys($dbkeys);

        foreach ($data as $key => $val)
        {
            if (in_array($key, $dbkeys))
            {
                $newdata[$key] = $val;
            }
            else
            {
                $params[$key] = $val;
            }
        }

        $newdata['params'] = json_encode($params);
        return $newdata;
    }

    /**
     * Method to copy an item
     *
     * @access    public
     * @return    boolean    True on success
     */
    public function copy($id)
    {
        $item = $this->getItem($id);

        unset($item->_errors);
        $item->id = 0;
        $item->published = 0;
        $item->name = JText::sprintf('NR_COPY_OF', $item->name);

        $item = $this->validate(null, (array) $item);

        return ($this->save($item));
    }
}

