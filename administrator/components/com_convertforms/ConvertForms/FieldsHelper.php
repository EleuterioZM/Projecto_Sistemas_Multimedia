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

use Joomla\Registry\Registry;
use ConvertForms\Helper;

/**
 *  ConvertForms Fields Helper Class
 */
class FieldsHelper
{
    /**
     *  List of available field groups and types
     *
     *  Consider using a field class property in order to declare the field group instead.
     *
     *  @var  array
     */
    public static $fields = [
        'common' => [
            'text',
            'textarea',
            'dropdown',
            'radio',
            'checkbox',
            'number',
            'email',
            'tel',
            'url',
            'submit',
        ],
        'layout' => [
            'html',
            'heading',
            'emptyspace',
            'divider',
        ],
        'advanced' => [
            'hidden',
            'datetime',
            'password',
            'fileupload',
            'termsofservice',
            'editor',
            'confirm',
            'rating',
            'rangeslider',
            'colorpicker',
            'recaptcha',
            'recaptchav2invisible',
            'hcaptcha',
            'signature',
            'country',
            'currency'
        ]
    ];

    /**
     *  Returns a list of all available field groups and types
     *
     *  @return  array  
     */
    public static function getFieldTypes()
    {
        $arr = [];

        foreach (self::$fields as $group => $fields)
        {
            if (!count($fields))
            {
                continue;
            }

            $arr[$group] = array(
                'name'  => $group,
                'title' => \JText::_('COM_CONVERTFORMS_FIELDGROUP_' . strtoupper($group))
            );

            foreach ($fields as $key => $field)
            {
                $arr[$group]['fields'][] = array(
                    'name'  => $field,
                    'title' => \JText::_('COM_CONVERTFORMS_FIELD_' . strtoupper($field)),
                    'desc'  => \JText::_('COM_CONVERTFORMS_FIELD_' . strtoupper($field) . '_DESC'),
                    'class' => self::getFieldClass($field)
                );
            }
        }

        return $arr;
    }

    /**
     *  Render field control group used in the front-end
     *
     *  @param   object  $fields  The fields to render
     *
     *  @return  string           The HTML output
     */
    public static function render($fields)
    {
        $html = array();

        foreach ($fields as $key => $field)
        {
            if (!isset($field['type']))
            {
                continue;
            }

            // Skip unknown field types
            if (!$class = self::getFieldClass($field['type']))
            {
                continue;
            }

            $html[] = $class->setField($field)->getControlGroup();
        }

        return implode(' ', $html);
    }

    /**
     *  Constructs and returns the field type class
     *
     *  @param   String  $name  The field type name
     *
     *  @return  Mixed          Object on success, Null on failure
     */
    public static function getFieldClass($name, $field_data = null, $form_data = null)
    {
        $class = __NAMESPACE__ . '\\Field\\' . ucfirst($name);

        if (!class_exists($class))
        {
            return false;
        }

        return new $class($field_data, $form_data);
    }

    public static function prepare($form, $classPrefix = 'cf')
    {
        $params = $form['params'];

        if (!is_array($form['fields']) || count($form['fields']) == 0)
        {
            return;
        }

        $fields_ = [];

        foreach ($form['fields'] as $key => $field)
        {
            $field['namespace'] = $form['id'];

            // Field Classes
            $fieldClasses = [
                $classPrefix . "-input",
                $classPrefix . "-input-shadow-" . ($params->get("inputshadow", "false") ? "1" : "0"),
                isset($field['size']) ? $field['size'] : null,
                isset($field['inputcssclass']) ? $field['inputcssclass'] : null
            ];

            $field['class'] = implode(' ', $fieldClasses);
            $field['form']  = $form;

            $fields_[] = $field;
        }

        $globalCSSVars = [
            'color-primary'          => '#4285F4',
            'color-success'          => '#0F9D58',
            'color-danger'           => '#DB4437',
            'color-warning'          => '#F4B400',
            'color-default'          => '#444',
            'color-grey'             => '#ccc', 
        ];

        $cssVars = [
            // Form settings
            'font'		     		 => trim($params->get('font')),
            'max-width'              => ($params->get('autowidth', 'auto') == 'auto' ? null : (int) $params->get('width', 500) . 'px'),
            'background-color'       => $params->get('bgcolor'),
            'border'                 => $params->get('borderstyle', 'solid') !== 'none' ? implode(' ', [$params->get('borderstyle', 'solid'), (int) $params->get('borderwidth', 2) . 'px', $params->get('bordercolor', '#000')]) : null,
            'border-radius'          => (int) $params->get('borderradius', 0) . 'px',
            'padding'                => $params->get('padding', 20) > 0 ? (int) $params->get('padding', 20) . 'px' : null,
            
            // Label settings
            'label-color'			 => $params->get('labelscolor', '#888'),
            'label-size'			 => (int) $params->get('labelsfontsize', 15) . 'px',
            
            // Input settings
            'input-color'			 => $params->get('inputcolor', '#888'),
            'input-text-align'	     => $params->get('inputalign', 'left'),
            'input-background-color' => $params->get('inputbg', '#fff'),
            'input-border-color'     => $params->get('inputbordercolor', '#ccc'),
            'input-border-radius'	 => (int) $params->get('inputborderradius', '0') . 'px',
            'input-size'		 	 => (int) $params->get('inputfontsize', 15) . 'px',
            'input-padding'		     => (int) $params->get('inputvpadding', 11) . 'px ' . (int) $params->get('inputhpadding', '12') . 'px',
        ];
        
        // Background Image
        if ($params->get('bgimage', false))
        {
            $imgurl = intval($params->get("bgimage")) == 1 ? \JURI::root() . Helper::cleanLocalImage($params->get('bgfile')) : $params->get("bgurl");
            $cssVars['background-image'] = 'url' . '(' . $imgurl . ')';
            $cssVars['background-repeat'] = strtolower($params->get("bgrepeat"));
            $cssVars['background-size'] = strtolower($params->get("bgsize"));
            $cssVars['background-position'] = strtolower($params->get("bgposition"));
        }

        $cssVarsGlobal = self::cssVarsToString($globalCSSVars, '.convertforms');
        $cssVarsForm = self::cssVarsToString($cssVars, '#cf_' . $form['id']);

        $html = self::render($fields_);

        if (\JFactory::getApplication()->isClient('site'))
        {
            Helper::addStyleDeclarationOnce($cssVarsGlobal);
            Helper::addStyleDeclarationOnce($cssVarsForm);
        } else
        {
            $html .= '
                <style>' . $cssVarsGlobal . $cssVarsForm . '</style>
            ';
        }

        return $html;
    }

    public static function cssVarsToString($cssVars, $namespace)
    {
        $output = '';

        foreach (array_filter($cssVars) as $key => $value)
        {
            $output .= '--' . $key . ': ' . $value . ';' . "\n";
        }

        return $namespace . ' {
                ' . $output . '
            }
        ';
    }
}

?>