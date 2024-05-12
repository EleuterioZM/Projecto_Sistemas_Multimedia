<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

/**
 *  Field Choice Class used by dropdown, checkbox and radio fields
 */
class FieldChoice extends \ConvertForms\Field
{
	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
        'browserautocomplete',
    ];

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
        parent::setField($field);

        // Get input options
        $this->field->choices = $this->getChoices();

        // Use the selected choice as the field value if we don't have a default value set.
        if ($this->field->value == '')
        {
            foreach ($this->field->choices as $choice)
            {
                if ($choice['selected'])
                {
                    $this->field->value = $choice['value'];
                    break;
                }
            }
        }

        if ($this->multiple && !is_array($this->field->value))
        {
            $this->field->value = explode(',', $this->field->value);
        }

		return $this;
	}

    /**
     * Get an array of all field options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        foreach ($this->field->get('choices.choices', []) as $option)
        {
            $option = (array) $option;

            if (!isset($option['label']) || $option['label'] == '')
            {
                continue;
            }

            // Prepare all options, not only the Label (Remember, we can use Smart Tags in any option, right?) with Smart Tags.
            $option = \NRFramework\SmartTags::getInstance()->replace($option);

            $label = trim($option['label']);
            $value = $option['value'] == '' ? $label : $option['value'];
            $calcValue = isset($option['calc-value']) && $option['calc-value'] !== '' ? $option['calc-value'] : $value;
            $isSelected = isset($option['default']) && $option['default'] ? true : false;

            $options[] = [
                'label'      => $label,
                'value'      => $value,
                'calc-value' => $calcValue,
                'selected'   => $isSelected
            ];
        }

        return $options;
    }

	/**
	 *  Set the field choices
	 *
	 *  Return Array sample
	 *
	 *  $choices = array(
     *  	'label'      => 'Color',
     *   	'value'      => 'color,
     *   	'calc-value' => '150r,
     *  	'selected'   => true,
     *   	'disabled'   => false
	 *  )
	 *
	 *  @return  array  The field choices array
	 */
	protected function getChoices()
	{
        $field = $this->field;

		if (!isset($field->choices) || !isset($field->choices['choices']))
        {
            return;
        }

        $choices = array();
        $hasPlaceholder = (isset($field->placeholder) && !empty($field->placeholder));

        // Create a new array of valid only choices
        // @todo Use $this->getOptions() to get the field options
        foreach ($field->choices['choices'] as $key => $choiceValue)
        {
            if (!isset($choiceValue['label']) || $choiceValue['label'] == '')
            {
                continue;
            }

            $label = trim($choiceValue['label']);
            $value = $choiceValue['value'] == '' ? strip_tags($label) : $choiceValue['value'];

            $choices[] = array(
                'label'      => $label,
                'value'      => $value,
                'calc-value' => (isset($choiceValue['calc-value']) && $choiceValue['calc-value'] != '' ? $choiceValue['calc-value'] : $value),
                'selected'   => (isset($choiceValue['default']) && $choiceValue['default'] && !$hasPlaceholder) ? true : false
            );
        }

        // If we have a placeholder available, add it to dropdown choices.
        if ($hasPlaceholder)
        {
            array_unshift($choices, array(
                'label'    => trim($field->placeholder),
                'value'    => '',
                'selected' => true,
                'disabled' => $field->required == '1' ? true : false
            ));
        }

        return $choices;
	}

    /**
     * In choice-based fields, it makes more sense to display the choice's label instead of the choice's value when the field is used for presentation purposes like in the
     *  
     *   1. Thank you Message
     *   2. {all_fields} Smart Tag
     *   3. Form editing page in the back-end
     *   4. PDF Form Submission addon. 
     * 
     *  While we keep using the choice's value in functions like
     *   1. Calculations
     *   2. Conditional fields
     *   3. JSON API.
     *   4. {field.FIELDNAME}
     *
     * @param  string $value The raw value as stored in the database / submitted by the user
     * 
     * @return string
     */
	public function prepareValueHTML($value)
	{
        if (is_array($value))
        {
            foreach ($value as &$value_)
            {
                $value_ = $this->findChoiceLabelByValue($value_);
            }
        } else 
        {
            $value = $this->findChoiceLabelByValue($value);
        }

        return parent::prepareValueHTML($value);
	}

    /**
     * Attempt to assosiate a choice value with a choice label. As a fallback the raw value will be returned.
     * 
     * The attempt may fail for one of the reasons below:
     * 
     *   1. A choice value is renamed.
     *   2. A choice is removed.
     *   3. Multiple choices has the same value (which obviously is a mistake by the user).
     * 
     * The most reliable way to always know the choice's label, is to implement this task https://smilemotive.teamwork.com/#/tasks/30244858
     * and start storing in the database a choice's unique ID instead of the choice's value.
     *
     * @param  mixed $value
     * 
     * @return string
     */
    private function findChoiceLabelByValue($value)
    {
        // In multiple choice fields, the value can't be empty.
        if ($value == '')
        {
            return $value;
        }

        if ($options = $this->getOptions())
        {
            foreach ($options as $option)
            {
                // We might lowercase both values?
                if ($option['value'] == $value)
                {
                    return $option['label'];
                }
            }
        }

        // If we can't assosiacte the given value with a label, return the raw value as a fallback.
        return $value;
    }
}
?>