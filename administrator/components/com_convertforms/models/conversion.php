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

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Filter\InputFilter;
use ConvertForms\Form;
use ConvertForms\Helper;
use ConvertForms\FieldsHelper;

defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * Conversion Model Class
 */
class ConvertFormsModelConversion extends JModelAdmin
{
    /**
     *  The database object
     *
     *  @var  object
     */
    private $db;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JModelLegacy
     * @since   1.6
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->db = JFactory::getDbo();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Conversion', $prefix = 'ConvertFormsTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.6.1
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
        if (!isset($data->params))
        {
            return parent::preprocessForm($form, $data, $group);
        }

        // Ensure the form binds the data regardless the letter case of the field's name.
        // We should not rely on the field's name. Instead we need to switch over to field's IDs instead.
        $data->params = array_change_key_case($data->params);

        // @todo - What we do here is somehow a joke. We should consider moving the logic of preparing each field to the respective field class in the namespace.
        $data_ = clone $data;

        $this->prepare($data_);

        // Add form custom fields to form
        $fields = [];

        foreach ($data_->prepared_fields as $key => $field)
        {
            $type = $field->options->get('type');

            // Map of fields types that need to be transformed in order to be recognized by the XML parser.
            $transformFields = [
                'hidden'     => 'text',
                'currency'   => 'NR_Currencies',
                'country'    => 'NR_Geo',
                'checkbox'   => 'checkboxes',
                'dropdown'   => 'list',
                'fileupload' => 'textlist',
                'confirm'    => $field->options->get('confirm_type')
            ];

            if ($type == 'fileupload')
            {
                $limit_files = $field->options->get('limit_files');

                if (!isset($limit_files) || (isset($limit_files) && $limit_files == '1'))
                {
                    $transformFields['fileupload'] = 'text';

                    // In case the previous multiple field is turn into a single field, we need to transform the value from array to string too.
                    if (is_array($data->params[$key]))
                    {
                        $data->params[$key] = implode(',', $data->params[$key]);
                    }
                }
            }            
            
            if (array_key_exists($type, $transformFields))
            {
                $type = $transformFields[$type];
            }

            // Radio fields doesn't accept Array as a value and we need to transform it into a string.
            if (in_array($type, ['radio']))
            {
                if (isset($data->params[$key]))
                {
                    $data->params[$key] = implode('', (array) $data->params[$key]);
                }
            }

            // Create the field
            $label = $field->class->getLabel();

            $fld = new SimpleXMLElement('<field/>');
            $fld->addAttribute('name', $key);
            $fld->addAttribute('type', $type);
            $fld->addAttribute('label', $label);
            $fld->addAttribute('hint', $field->options->get('placeholder', $label));
            $fld->addAttribute('description', $fld->attributes()->hint);
            $fld->addAttribute('class', 'input-xlarge');
            $fld->addAttribute('rows', 10); // Used for textarea inputs
            $fld->addAttribute('filter', $field->options->get('filter', 'safehtml'));

            if ($type == 'editor')
            {
                $fld->addAttribute('editor', $field->options->get('editor'));
            }

            // Define options to list-based fields
            if (in_array($type, ['list', 'radio', 'checkboxes']) && $choices = $field->class->getOptions())
            {
                if ($type == 'list' && $hint = $field->options->get('placeholder'))
                {
                    array_unshift($choices, [
                        'label'    => trim($hint),
                        'value'    => '',
                        'selected' => true
                    ]);
                }

                foreach ($choices as $choice)
                {
                    $option = $fld->addChild('option', htmlspecialchars($choice['label']));
                    $option->addAttribute('value', strip_tags($choice['value']));
                }
            }

            // Get field's XML
            $fields[] = str_replace('<?xml version="1.0"?>', '', $fld->asXml());
        }
        
        $form->setField(new SimpleXMLElement('
            <fieldset name="params">
                <fields name="params">
                    ' . implode('', $fields) . '
                </fields>
            </fieldset>
        '));

		parent::preprocessForm($form, $data, $group);
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
        $form = $this->loadForm('com_convertforms.conversion', 'conversion', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) 
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_convertforms.edit.conversion.data', array());

        if (empty($data))
        {
            $data = $this->getItem(null, false);
        }

        return $data;
    }

    /**
     *  Validate data before saving
     *
     *  @param   object  $form   The form to validate
     *  @param   object  $data   The data to validate
     *  @param   string  $group  
     *
     *  @return  array           The validated data
     */
    public function validate($form, $data, $group = null)
    {
        // Validate conversion edited via the backend
        if (JFactory::getApplication()->isClient('administrator'))
        {
            return parent::validate($form, $data, $group);
        }

        // Make sure we have a valid Form data
        if (!isset($data['cf']) || empty($data['cf']))
        {
            throw new Exception('No submission data found');
        }

        // Make sure we have a valid Form ID passed
        if (!isset($data['cf']['form_id']) || !$formid = (int) $data['cf']['form_id'])
        {
            throw new Exception('Form ID is either missing or invalid');
        }

        // Let the user manipulate the post data before saved into the database.
        // @todo - Move PHP Scripts logic to a separate plugin.
        $payload = ['post' => &$data['cf']];
        Form::runPHPScript($formid, 'formprocess', $payload);

        // Get form from payload or load a new instance
        $form = isset($payload['form']) ? $payload['form'] : Form::load($formid);

        $error_message = null;
        $result = JFactory::getApplication()->triggerEvent('onConvertFormsSubmissionValidate', [&$data['cf'], &$error_message, $form]);

        if (in_array(false, $result, true))
        {
            throw new \Exception(is_null($error_message) ? 'Error' : $error_message);
        }

        // Honeypot check
        if (isset($data['cf']['hnpt']) && !empty($data['cf']['hnpt']))
        {
            throw new Exception('Honeypot field triggered');
            die();
        }

        // Make sure the right form is loaded
        if (is_null($form['id']))
        {
            throw new Exception('Unknown Form');
        }

        // Initialize the object that is going to be saved in the database
        $newData = [
            'form_id'     => $formid,
            'campaign_id' => (int) $form['params']['campaign'],
            'state'       => isset($form['params']['submission_state']) ? $form['params']['submission_state'] : 1
        ];

        $overrides = isset($data['overrides']) ? json_decode($data['overrides']) : [];

        // Let's validate submitted data
        foreach ($form['fields'] as $key => $form_field)
        {
            $field_name  = isset($form_field['name']) ? $form_field['name'] : null;
            $field_class = FieldsHelper::getFieldClass($form_field['type'], $form_field, $data);
            $user_value  = (!is_null($field_name) && isset($data['cf'][$field_name])) ? $data['cf'][$field_name] : null;

            // Check if the field must be ignored and not be validated. Eg: hidden by conditional logic.
            if (isset($overrides->ignore) && is_array($overrides->ignore) && in_array($form_field['key'], $overrides->ignore))
            {
                continue;
            }

            // Validate and Filter user value. If an error occurs the submission aborts with an exception shown in the form
            $field_class->validate($user_value);

            // Skip unknown fields or fields with an empty value
            if (!$field_name || $user_value == '')
            {
                continue;
            }

            $newData['params'][$field_name] = $user_value;
        }

        return $newData;
    }

    /**
     *  Create a new conversion based on the post data.
     *
     *  @return  object     The new conversion row object
     */ 
    public function createConversion($data)
    {
        JPluginHelper::importPlugin('convertforms');
        JPluginHelper::importPlugin('convertformstools');
        JPluginHelper::importPlugin('system');

        // Validate data
        $data = $this->validate(null, $data);

        /** 
         * This event is rather useful for the following reasons:
         * 
         * 1. It allows us to make modifications to the submission after it passes the validation checks.
         * 2. It allows us to access and modify submission properties such as 'state' and 'form_id'. Eg: Store all submissions unpublished by default.
         * 
         * We may support this event in the PHP Scripts section too.
         */
        JFactory::getApplication()->triggerEvent('onConvertFormsSubmissionBeforeSave', [&$data]);

        // JSON_UNESCAPED_UNICODE encodes multibyte unicode characters literally. 
        // Without: Τάσος => \u03a4\u03ac\u03c3\u03bf\u03c2
        // With:    Τάσος => Τάσος
        $data['params'] = json_encode($data['params'], JSON_UNESCAPED_UNICODE);

        // Everything seems fine. Let's save data to the database.
        if (!$this->save($data))
        {
            throw new Exception($this->getError());
        }

        $submission = $this->getItem();

        // Run user's PHP script after the form has been processed, stored into the database and all addons have run.
        // @todo - Move PHP Scripts logic into a separate plugin.
        $payload = ['submission' => &$submission];
        Form::runPHPScript($data['form_id'], 'afterformsubmission', $payload);

        /**
         * Why this event was created:
         * 
         * - Due to the fact that we cannot hook into "onConvertFormsSubmissionAfterSave" and manipulate the $submission object.
         *   If we try to do so, the updated $submission wont find its way to the other listeners hooked to this event.
         * 
         * When does this event run:
         * 
         * - Right after the submission has been saved to the database.
         * - Before other plugins hook to run their own code after the submission has been processed.
         * 
         * When it should be used:
         * 
         * - When we want to modify the submission object just before other listeners hook into "onConvertFormsSubmissionAfterSave"
         *   such as Email Notifications so they will get the updated $submission object.
         * 
         * Example Use Case:
         * 
         * - If we want to rename the uploaded files and use add the submission id to the filename, we must listen to this event,
         *   update the $submission object, rename the file, update the database and emails will use the updated file data.
         */
        JFactory::getApplication()->triggerEvent('onConvertFormsSubmissionAfterSavePrepare', [&$submission]);

        /**
         * When does this event run:
         * 
         * - At the end of the submission process, after the $submission object has been finalized and its ready to be given
         *   out to plugins/code that hook to this event.
         * 
         * When it should be used:
         * 
         * - We dont want to modify the $submission data and plan to execute our code after the form has been submitted
         *   successfully.
         * 
         * Example Use Case:
         * - Send Email Notifications
         * - Submit data to Campaigns
         */
        JFactory::getApplication()->triggerEvent('onConvertFormsSubmissionAfterSave', [&$submission]);
        
        return $submission;
    }

    /**
     *  Get a conversion item
     *
     *  @param   interger  $pk  The conversion row primary key
     *
     *  @return  object         The conversion object
     */
    public function getItem($pk = null, $prepare = true)
    {
        if (!$item = parent::getItem($pk))
        {
            return;
        }

        JPluginHelper::importPlugin('convertformstools');

        // There's no need to change the timezone on the backend as the date properties are mainly used in the Calendar fields
        // which are already modifying the timezone offset with the filter="user_utc" property.
        if (JFactory::getApplication()->isClient('site'))
        {
            $item->created  = Helper::formatDate($item->created);
            $item->modified = Helper::formatDate($item->modified);
        }

        if ($item->user_id)
        {
            $item->user_name = JFactory::getUser($item->user_id)->name;
        }

        // Load Form & Campaign Model
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/models', 'ConvertFormsModel');

        $modelForm = JModelLegacy::getInstance('Form', 'ConvertFormsModel', ['ignore_request' => true]);
        $modelCampaign = JModelLegacy::getInstance('Campaign', 'ConvertFormsModel', ['ignore_request' => true]);

        $item->form = $modelForm->getItem($item->form_id);
        $item->campaign = $modelCampaign->getItem($item->campaign_id);

        // On J4 we get infinite loop if we inject the prepared fields to $data.
        // Thus, we don't prepare the submission on this method in order to not pollute the $data object. 
        // Instead we use the preprocessForm() method with a cloned object.
        if ($prepare)
        {
            $this->prepare($item);
        }

        //var_dump($item->prepared_fields['country_16']);

        return $item;
    }

    public function prepare(&$submission)
    {
        if (!$submission || is_null($submission->id))
        {
            return;
        }

        // Note: Form may be already available in the $submission object.
        if (!$form = Form::load($submission->form_id, false, true))
        {
            return;
        }

        $fields = [];

        // Make sure we're manipualating an array.
        $submission_params = array_change_key_case((array) $submission->params);

        foreach ($form['fields'] as $field)
        {
            // Skip fields with no name like reCAPTCHA, HTML e.t.c
            if (!isset($field['name']))
            {
                continue;
            }

            $field_name = strtolower($field['name']);
            $submitted_value = isset($submission_params[$field_name]) ? $submission_params[$field_name] : '';

            // Make sure the type of field is valid
            if (!$class = FieldsHelper::getFieldClass($field['type'], $field))
            {
                continue;
            }

            $prepared_field = (object) [
                'options'    => new Registry($field),
                'class'      => $class,
                'label'      => isset($field['label']) && !empty($field['label']) ? JText::_($field['label']) : $field['name'],
                'value'      => $class->prepareValue($submitted_value),
                'value_html' => $class->prepareValueHTML($submitted_value),
                'value_raw'  => $submitted_value
            ];

            $fields[$field_name] = $prepared_field;
        }

        // @todo - Stop using 'prepare_fields' property and switch over to the standard 'fields' property.
        $submission->prepared_fields = $fields;
    }
}