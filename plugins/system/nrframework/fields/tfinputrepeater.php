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

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('subform');

class JFormFieldTFInputRepeater extends JFormFieldSubform
{
    /**
	 * Method to attach a Form object to the field.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.6
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
        $element->addAttribute('multiple', true);
        
        // By default initialize the field with an empty item.
        if (empty($value))
        {
            $value = [0 => ''];
        }

        // In case we have provided a default value in the XML in JSON format
        if ($value && is_string($value))
        {
            // Attempt to decode as JSON
            $value_ = json_decode($value, true);

            // If JSON decode fails, expect comma-separated or newline-separated values
            if (is_null($value_))
            {
                $value = NRFramework\Functions::makeArray($value);

                $new_value = [];

                foreach ($value as $key => $val)
                {
                    $new_value['value' . $key] = [
                        'value' => $val
                    ];
                }

                $value = $new_value;
            } else 
            {
                $value = $value_;
            }
        }

		parent::setup($element, $value, $group);

		return true;
	}

    /**
     * Method to get a list of options for a list input.
     * @return  array  An array of JHtml options.
     */
    protected function getInput()
    {
        $this->layout = 'joomla.form.field.subform.repeatable-table';

        $this->assets();

        return '<div class="tf-input-repeater ' . $this->class . '">' . parent::getInput() . '<a href="#" class="btn tf-input-repeater-add"><span class="icon-plus"></span></a></div>';
    }

    /**
     * Load field assets.
     * 
     * @return  void
     */
    private function assets()
    {
        JHtml::stylesheet('plg_system_nrframework/tfinputrepeater.css', ['relative' => true, 'versioning' => 'auto']);
        JHtml::script('plg_system_nrframework/tfinputrepeater.js', ['relative' => true, 'version' => true]);
    }
}