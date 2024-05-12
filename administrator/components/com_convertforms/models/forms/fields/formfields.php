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

use ConvertForms\FieldsHelper;

class JFormFieldFormFields extends JFormField
{
    /**
     *  Disable field label
     *
     *  @return  void
     */
    protected function getLabel()
    {
        return;
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value.
     *
     * @return  boolean  True on success.
     *
     * @since   3.6
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group))
        {
            return false;
        }

        if ($this->value)
        {
            // Convert JSON passed in the default property to array
            if (is_string($this->value))
            {
                $this->value = json_decode($this->value, true);
            }

            // Migration Fixes
            foreach ($this->value as $key => &$value)
            {
                if (is_object($value))
                {
                    $value = (array) $value;
                }
                
                $value['key'] = (int) str_replace('fields', '', $key);
            }
        }

        return true;
    }

    /**
     *  Get field input
     *
     *  @return  string
     */
    protected function getInput()
    {
    	$html = JLayoutHelper::render('layout', array(
    		'formControl' => $this->name . '[' . $this->fieldname . 'X]', 
    		'items'       => $this->renderItems(),
            'fieldgroups' => FieldsHelper::getFieldTypes(),
            'nextid'      => $this->getNextID()
    	),
    	 __DIR__ . '/formfields/');

        $this->addMediaFiles();

    	return $html;
    }

    private function getNextID()
    {
        $max = 0;

        if (is_array($this->value))
        {
            foreach ($this->value as $key => $item)
            {
                $max = $item['key'] > $max ? $item['key'] : $max;
            }
        }

        return $max + 1;
    }

    private function renderItems()
    {
        $items = array();

        if (!$this->value || !is_array($this->value))
        {
            return $items;
        }
        
        $i = 0;

    	foreach ($this->value as $key => $item)
    	{
	        if (!$class = FieldsHelper::getFieldClass($item['type']))
	        {
	            continue;
	        }

    		$formControl = $this->name . '[' . $this->fieldname . $item['key'] . ']';

            $items[] = array(
                'form' => $class->getOptionsForm($formControl, $item),
                'data' => $item
            );

			$i++;
    	}

    	return $items;
    }

    private function addMediaFiles()
    {
        JHtml::script('com_convertforms/choices.js', ['relative' => true, 'version' => 'auto']);
    }
}