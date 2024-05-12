<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

use Joomla\Registry\Registry;

class JFormFieldNRResponsiveControl extends JFormFieldText
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    function getInput()
    {
        return $this->getLayout();
    }

    /**
     * Returns html for all devices
     * 
     * @return  array
     */
    private function getFieldsData()
    {
        if (!$fieldsList = $this->getSubformFieldsList())
        {
            return [];
        }
        
        $html = [
            'desktop' => '',
            'tablet'  => '',
            'mobile'  => ''
        ];

        $base_name = $fieldsList['base_name'];

        // Control default value
        $control_default = json_decode($this->default, true);

        // loop for all devices
        foreach ($html as $device => &$device_output)
        {
            // loop all fields
            foreach ($fieldsList['fields'] as $fieldName)
            {
                $name = $fieldName;

                // Default value of the input for breakpoint
                $default = null;

                if ($control_default && isset($control_default[$device][$name]))
                {
                    $default = $control_default[$device][$name];
                }
                
                $field_data = $this->getFieldInputByDevice($name, $device, $default);
                $field_html = $field_data['html'];

                $field_html = str_replace(
                    [
                        '[' . $this->group . '][' . $name . ']',
                        '_' . $this->group . '_' . $name
                    ],
                    [
                        '[' . $this->group . '][' . $base_name . '][' . $name . '][' . $device . ']',
                        '_' . $this->group . '_' . $base_name . '_' . $name . '_' . $device
                    ], $field_html
                );
                
                // Render layout
                $payload = [
                    'label' => $field_data['label'],
                    'description' => $field_data['description'],
                    'data' => $field_html
                ];
                $layout = new JLayoutFile('responsive_control_item', JPATH_PLUGINS . '/system/nrframework/layouts');
                $device_output .= $layout->render($payload);
            }
        }
        
        return $html;
    }

    /**
     * Returns the field layout
     * 
     * @return  string
     */
    private function getLayout()
    {
        JHtml::stylesheet('plg_system_nrframework/responsive_control.css', ['relative' => true, 'version' => 'auto']);
        JHtml::script('plg_system_nrframework/responsive_control.js', ['relative' => true, 'version' => 'auto']);

        $width = isset($this->element['width']) ? (string) $this->element['width'] : '300px';
        $title = isset($this->element['title']) ? (string) $this->element['title'] : '';
        $class = isset($this->element['class']) ? ' ' . (string) $this->element['class'] : '';

        if (defined('nrJ4'))
        {
            $class .= ' isJ4';
        }
        
        $data = [
            'title'  => JText::_($title),
            'width'  => $width,
            'class'  => $class,
            'fields' => $this->getFieldsData()
        ];

        // Render layout
        $layout = new JLayoutFile('responsive_control', JPATH_PLUGINS . '/system/nrframework/layouts');
        return $layout->render($data);
    }

    /**
     * Returns the list of added fields
     * 
     * @return  array
     */
    private function getSubformFieldsList()
    {
        $el = $this->element;

        if (empty(count($el->subform)))
        {
            return [];
        }

        $data = [
            'base_name' => $el->attributes()->name,
            'fields' => []
        ];

        foreach ($el->subform->field as $key => $field)
        {
            $data['fields'][] = (string) $field->attributes()->name;
        }
        
        return $data;
    }

    /**
     * Returns the field's title and value
     * 
     * @param   string  $field_name     The field name of the field.
     * @param   string  $device         The breakpoint of the field.
     * @param   string  $default        The default value of the field.
     * 
     * @return  array
     */
    private function getFieldInputByDevice($field_name, $device, $default = null)
    {
        $el = $this->element;

        $data = [];

        foreach ($el->subform->field as $key => $field)
        {
            if ((string) $field->attributes()->name != $field_name)
            {
                continue;
            }

            // Get input value
            $value = $this->getFieldInputValue($field_name, $device);

            // If no value is set, get the default value (if given)
            if (!$value && $default)
            {
                $value = $default;
            }

            $data = [
                'label' => JText::_((string) $field->attributes()->label),
                'description' => JText::_((string) $field->attributes()->description),
                'html' => $this->form->getInput($field_name, $this->group, $value)
            ];
        }
        
        return $data;
    }

    /**
     * Finds the field input value
     * 
     * @param   string  $field_name
     * @param   string  $device
     * 
     * @return  string
     */
    private function getFieldInputValue($field_name, $device)
    {
        $values = $this->getValue();
        $values = new Registry($values);

        return $values->get($field_name . '.' . $device);
    }

    /**
     * Returns the field value
     * 
     * @return  mixed
     */
    private function getValue()
    {
        if (empty($this->value))
        {
            return;
        }

        return $this->value;
    }
}
