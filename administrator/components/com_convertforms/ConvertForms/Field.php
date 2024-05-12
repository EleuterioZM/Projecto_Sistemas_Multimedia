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

use Joomla\CMS\Filter\InputFilter;
use Joomla\Registry\Registry;
use ConvertForms\Helper;

defined('_JEXEC') or die('Restricted access');

/**
 *  Convert Forms Field Main Class
 */
class Field
{
	/**
	 *  Field Object
	 *
	 *  @var  object
	 */
	protected $field;

	/**
	 *  The prefix name used for input names
	 *
	 *  @var  string
	 */
	private $namePrefix = 'cf';

	/**
	 *  Filter user value before saving into the database. By default converts the input to a string; strips all HTML tags / attributes.
	 *
	 *  @var  string
	 */
	protected $filterInput = 'HTML';

	/**
	 *  Exclude common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields;

	/**
	 *  Data passed to layout rendering
	 *
	 *  @var  object
	 */
	private $layoutData;
	
	/**
	 * Indicates whether it accepts multiple values
	 *
	 * @var bool
	 */
	protected $multiple = false;

	/**
	 * Indicates the default required behavior on the form
	 *
	 * @var bool
	 */
	protected $required = true; 

	/**
	 * The data submitted by the user in the form
	 *
	 * @var array
	 */
	protected $data;

	/**
	 *  Class constructor
	 *
	 *  @param   mixed  $field_options   Object or Array Field options
	 *
	 *  @return  void
	 */
	public function __construct($field_options = null, $form_data = null)
	{
		$this->data = $form_data;

		if ($field_options)
		{
			$field_options['required'] = isset($field_options['required']) ? $field_options['required'] : $this->required;

			// We should better call $this->setField() here
			$this->field = new Registry($field_options);
		}

		$this->app = \JFactory::getApplication();
	}

	/**
	 *  Set field object
	 * 
	 *  Rename to prepareField
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		$field = is_array($field) ? (object) $field : $field;

		if (!isset($field->name) || empty($field->name))
		{
			$field->name = $this->getName() . '_' . $field->key;
		}

		$field->input_id   = 'form' . $field->namespace . '_' . $field->name;
		$field->input_name = $this->namePrefix . '[' . $field->name . ']';

		$field->htmlattributes = [];

		$this->field = $field;

		return $this;
	}

	/**
	 * Event fired during form saving in the backend to help us validate user options.
	 *
	 * @param  object	$model			The Form Model
	 * @param  array	$form_data		The form data to be saved
	 * @param  array	$field_options	The field data
	 *
	 * @return bool
	 */
	public function onBeforeFormSave($model, $form_data, &$field_options)
	{
		if (isset($field_options['name']) && $field_options['name'] == '')
		{
			$field_options['name'] = $field_options['type'] . '_' . $field_options['key'];
		}

		return true;
	}

	/**
	 *  Discovers the actual field name from the called class
	 *
	 *  @return  string
	 */
	protected function getName()
	{
		$class_parts = explode('\\', get_called_class());
		return strtolower(end($class_parts));
	}

	/**
	 *  Renders the field's input element
	 *
	 *  @return  string  	HTML output
	 */
	protected function getInput()
	{
		$layoutsPath = JPATH_ADMINISTRATOR . '/components/com_convertforms/layouts/fields/';

		// Override layout path if it's available
		$layoutName = isset($this->inheritInputLayout) ? $this->inheritInputLayout : $this->getName();
	
		// Check if an admininistrator layout is available
		if ($this->app->isClient('administrator') && \JFile::exists($layoutsPath . $layoutName . '_admin.php'))
		{
			$layoutName .= '_admin';
		}

		// @todo - Pass only this class to payload as $field and change all layouts accordingly. Mind the backwards compatibility.
		return Helper::layoutRender('fields.' . $layoutName, $this->getInputData());
	}

	/**
	 *  Prepares the field's input layout data
	 *
	 *  @return  array
	 */
	protected function getInputData()
	{
		return array(
			'class' => $this,
			'field' => $this->field,
			'form'  => $this->field->form
		);
	}

	/**
	 *  Renders the Field's control group that will contain both input and label parts.
	 *
	 *  @return  string 	HTML Output
	 */
	public function getControlGroup()
	{
		\JPluginHelper::importPlugin('convertformstools');
		$this->app->triggerEvent('onConvertFormsFieldBeforeRender', [&$this->field, $this->field->form]);

		$this->field->input = $this->getInput();

		$layoutData = [
			'field' => $this->field,
			'form'  => $this->field->form
		];

    	$html = Helper::layoutRender('controlgroup', $layoutData);

		$this->app->triggerEvent('onConvertFormsFieldAfterRender', [&$html, $this->field, $this->field->form]);

		return $html;
	}

	/**
	 *  Renders the Field's Options Form used in the backend
	 *
	 *  @param   string  $formControl  From control prefix
	 *  @param   array   $loadData     Form data to bind
	 *
	 *  @return  string                The final HTML output
	 */
	public function getOptionsForm($formControl = 'jform', $loadData = null)
	{
		// Setup the common form first
		$form = new \JForm('cf', array('control' => $formControl));

		$form->addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/fields');
		$form->addFieldPath(JPATH_PLUGINS . '/system/nrframework/fields');
		$form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/ConvertForms/xml/field.xml');

		// Exclude Fields
		if (is_array($this->excludeFields))
		{
			$reservedFields = array(
				'key',
				'type'
			);

			foreach ($form->getFieldSets() as $key => $fieldSetName)
			{	
				$fields = $form->getFieldset($fieldSetName->name);

				foreach ($fields as $key => $field)
				{
					// We can't exclude reserved fields
					if (in_array($field->fieldname, $reservedFields))
					{
						continue;
					}

					if (!in_array($field->fieldname, $this->excludeFields) && 
						!in_array('*', $this->excludeFields))
					{
						continue;
					}

					$form->removeField($field->fieldname);
				}
			}
		}

		// Load field based options
		$form->loadFile(JPATH_COMPONENT_ADMINISTRATOR . '/ConvertForms/xml/field/' . $this->getName() . '.xml');

        \JPluginHelper::importPlugin('convertformstools');
		$this->app->triggerEvent('onConvertFormsBackendRenderOptionsForm', [&$form, $this->getName()]);

		// Bind Data
		$form->bind($loadData);

		// Give individual field classes to manipulate the form before the render
		if (method_exists($this, 'onBeforeRenderOptionsForm'))
		{
			$this->onBeforeRenderOptionsForm($form);
		}

		// Render Layout
		$data = array(
			'form' 			=> $form,
			'header'		=> $this->getOptionsFormHeader(),
			'fieldTypeName' => $this->getName(),
			'loadData' 	 	=> $loadData
		);
		
		$html = Helper::layoutRender('optionsform', $data);

		// Give individual field classes to manipulate the form after the render
		if (method_exists($this, 'onAfterRenderOptionsForm'))
		{
			$this->onAfterRenderOptionsForm($html);
		}

		return $html;
	}

	/**
	 *  Display a text before the form options
	 *
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader()
	{
		return;
	}

	/**
	 *  Validate form submitted value
	 *
	 *  @param   mixed  $value           The field's value to validate (Passed by reference)
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		$isEmpty = $this->isEmpty($value);
		$isRequired = $this->field->get('required');

		if ($isEmpty && $isRequired)
		{
			$this->throwError(\JText::_('COM_CONVERTFORMS_FIELD_REQUIRED'), $field_options);
		}

		// Let's do some filtering.
		$value = $this->filterInput($value);
	}

	/**
	 * Checks if submitted value is empty
	 *
	 * @param  mixed $value
	 *
	 * @return bool
	 */
	protected function isEmpty($value)
	{
		if (is_array($value) && count($value) == 0)
		{
			return true;
		} 

		// Note: Do not use empty() as evaluates '0' as true which is a valid value for the Number field.
		if ($value == '' || is_null($value))
		{
			return true;
		}

		return false;
	}

	/**
	 *  Filter user input
	 *
	 *  @param   mixed  $input   User input value
	 *
	 *  @return  mixed           The filtered user input
	 */
	public function filterInput($input)
	{
		$filter = $this->field->get('filter', $this->filterInput);

		// Safehtml is a special filter and we need to initialize InputFilter class differently
		if ($filter == 'safehtml')
		{
			return InputFilter::getInstance([], [], 1, 1)->clean($input, 'html');
		}

		return InputFilter::getInstance()->clean($input, $filter);
	}

	/**	
	 *  Get the field's label which is also parsed for Smart Tags
	 */
	public function getLabel()
	{
		if ($label = $this->field->get('label'))
		{
			return \NRFramework\SmartTags::getInstance()->replace($label);
		}

		// In case the Label option is empty, use the name of the field.
		return $this->field->get('name');
	}

	/**
	 *  Throw an error exception
	 *
	 *  @param   [type]  $message        [description]
	 *
	 *  @return  [type]                  [description]
	 */
	public function throwError($message)
	{
		if (!$label = $this->getLabel())
		{
			$label = $this->field->get('name', ucfirst($this->field->get('type')));
		}

		throw new \Exception($label . ': ' . \JText::_($message));
	}

	/**
	 * Prepare value to be displayed to the user as plain text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValue($value)
	{
		if (is_bool($value))
		{
			return $value ? '1' : '0';
		}

		if (is_array($value))
		{
			return implode(', ', $value);
		}

		// Strings and numbers
		return Helper::escape($value);
	}

	public function prepareValueHTML($value)
	{
		return $this->prepareValue($value);
	}
	
	/**
	 * Convert a field object to Widget
	 *
	 * @param	array	$widget_options		The widget options
	 *
	 * @return	string 	The widget final layout
	 */
    public function toWidget($widget_options = [])
    {
        $default_options = [
            'id'			=> $this->field->input_id,
            'name' 			=> $this->field->input_name,
            'readonly' 		=> isset($this->field->readonly) ? (bool) $this->field->readonly : false,
            'value' 		=> isset($this->field->value) ? (string) $this->field->value : null,
			'required'		=> isset($this->field->required) ? (bool) $this->field->required : false,
			'placeholder'	=> isset($this->field->placeholder) ? $this->field->placeholder : '',
			'load_css_vars' => false,
            'input_class' 	=> 'cf-input',
        ];

        $widget_options = array_merge($default_options, $widget_options);

        return \NRFramework\Widgets\Helper::render($this->getName(), $widget_options);
    }
}
?>