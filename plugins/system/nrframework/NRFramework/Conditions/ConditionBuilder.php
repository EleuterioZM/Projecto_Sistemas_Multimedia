<?php

/**
 *  @author          Tassos.gr <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions;

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use NRFramework\Conditions\ConditionsHelper;
use NRFramework\Extension;

class ConditionBuilder
{
    public static function pass($rules)
    {
        $rules = self::prepareRules($rules);

        if (empty($rules))
        {
            return true;
        }

        return ConditionsHelper::getInstance()->passSets($rules);
    }

    /**
     * Prepare rules object to run checks
     *
     * @return void
     */
    public static function prepareRules($rules)
    {
        $rules_ = [];

        foreach ($rules as $key => $group)
        {
            if (isset($group['enabled']) AND !(bool) $group['enabled'])
            {
                continue;
            }

            // A group without rules, doesn't make sense.
            if (!isset($group['rules']) OR (isset($group['rules']) AND empty($group['rules'])))
            {
                continue;
            }

            $validRules = [];

            foreach ($group['rules'] as $rule)
            {
                // Make sure rule has a name.
                if (!isset($rule['name']) OR (isset($rule['name']) AND empty($rule['name'])))
                {
                    continue;
                }

                // Rule is invalid if both value and params properties are empty
                if (!isset($rule['value']) && !isset($rule['params']))
                {
                    continue;
                }

                // Skip disabled rules
                if (isset($rule['enabled']) && !(bool) $rule['enabled'])
                {
                    continue;
                }

                // We don't need this property.
                unset($rule['enabled']);

                // Prepare rule value if necessary
                if (isset($rule['value']))
                {
                    $rule['value'] = self::prepareTFRepeaterValue($rule['value']);
                }

                // Verify operator
                if (!isset($rule['operator']) OR (isset($rule['operator']) && empty($rule['operator'])))
                {
                    $rule['operator'] = isset($rule['params']['operator']) ? $rule['params']['operator'] : '';
                }

                $validRules[] = $rule;
            }

            if (count($validRules) > 0)
            {
                $group['rules'] = $validRules;

                if (!isset($group['matching_method']) OR (isset($group['matching_method']) AND empty($group['matching_method'])))
                {
                    $group['matching_method'] = 'all';
                }

                unset($group['enabled']);
                $rules_[] = $group;
            }
        }

        return $rules_;
    }
    
    /**
     * Parse the value of the TF Repeater Input field.
     * 
     * @param   array  $selection
     * 
     * @return  mixed
     */
    private static function prepareTFRepeaterValue($selection)
    {
        // Only proceed when we have an array of arrays selection.
        if (!is_array($selection))
        {
            return $selection;
        }

        $first = array_values($selection)[0];

        if (!is_array($first))
        {
            return $selection;
        }

        if (!isset($first['value']))
        {
            return $selection;
        }

        $new_selection = [];

        foreach ($selection as $value)
        {
            /**
            * We expect a `value` key for TFInputRepeater fields or a key,value pair
            * for plain arrays.
            */
            if (!isset($value['value']))
            {
                /**
                * If no value exists, it means that the passed $assignment->selection is a key,value pair array so we use the value
                * as our returned selection.
                * 
                * This happens when we pass a key,value pair array as $assignment->selection when we expect a TFInputRepeater value
                * so we need to take this into consideration.
                */
                $new_selection[] = $value;
                continue;
            }

            // value must not be empty
            if (empty(trim($value['value'])))
            {
                continue;
            }

            $new_selection[] = $value['value'];
        }

        return $new_selection;
    }

    /**
     * Returns the TGeoIP plugin modal.
     * 
     * @return  string
     */
    public static function getGeoModal()
    {
        // Do not proceed if the database is up-to-date
        if (!\NRFramework\Extension::geoPluginNeedsUpdate())
        {
            return;
        }

        \Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');

        $modalName = 'tf-geodbchecker-modal';

        // The TGeoIP Plugin URL
        $url = \JURI::base(true) . '/index.php?option=com_plugins&view=plugin&tmpl=component&layout=modal&extension_id=' . \NRFramework\Functions::getExtensionID('tgeoip', 'system');

        $options = [
            'title'       => \JText::_('NR_EDIT'),
            'url'         => $url,
            'height'      => '400px',
            'backdrop'    => 'static',
            'bodyHeight'  => '70',
            'modalWidth'  => '70',
            'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'
                    . \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>                 
                    <button type="button" class="btn btn-success" aria-hidden="true"
                    onclick="jQuery(\'#' . $modalName . ' iframe\').contents().find(\'#applyBtn\').click();">'
                    . \JText::_('JAPPLY') . '</button>',
        ];

        return \JHtml::_('bootstrap.renderModal', $modalName, $options);
    }

    /**
     * Prepares the given rules list.
     * 
     * @param   array  $list
     * 
     * @return  array
     */
    public static function prepareXmlRulesList($list)
    {
        if (is_array($list))
        {
            $list = implode(',', array_map('trim', $list));
        }
        else if (is_string($list))
        {
            $list = str_replace(' ', '', $list);
        }

        return $list;
    }

    /**
     * Adds a new condition item or group.
     * 
     * @param   string  $controlGroup       The name of the input used to store the data.
     * @param   string  $groupKey           The group index ID.
     * @param   string  $conditionKey       The added condition item index ID.
     * @param   array   $condition          The condition name we are adding.
     * @param   string  $include_rules       The list of included conditions that override the available conditions.
     * @param   string  $exclude_rules       The list of excluded conditions that override the available conditions.
     * 
     * @return  string
     */
    public static function add($controlGroup, $groupKey, $conditionKey, $condition = null, $include_rules = [], $exclude_rules = [])
    {
        $controlGroup_ = $controlGroup . "[$groupKey][rules][$conditionKey]"; // @Todo - rename input namespace to 'conditions'
        $form = self::getForm('conditionbuilder/base.xml', $controlGroup_, $condition);
        $form->setFieldAttribute('name', 'include_rules', is_array($include_rules) ? implode(',', $include_rules) : $include_rules);
        $form->setFieldAttribute('name', 'exclude_rules', is_array($exclude_rules) ? implode(',', $exclude_rules) : $exclude_rules);

        $options = [
            'name'              => $controlGroup_,
            'enabled'           => !isset($condition['enabled']) ? true : (string) $condition['enabled'] == '1',
            'toolbar'           => $form,
            'groupKey'          => $groupKey,
            'conditionKey'      => $conditionKey,
            'options'           => ''
        ];

        if (isset($condition['name']))
        {
            $optionsHTML = self::renderOptions($condition['name'], $controlGroup_, $condition);
            $options['condition_name'] = $condition['name'];
            $options['options'] = $optionsHTML;
        }

        return self::getLayout('conditionbuilder_row', $options);
    }

    /**
     * Render condition item settings.
     * 
     * @param   string  $name          The name of the condition item.
     * @param   string  $controlGroup  The name of the input used to store the data.
     * @param   object  $formData      The data that will be bound to the form.
     * 
     * @return  string
     */
    public static function renderOptions($name, $controlGroup = null, $formData = null)
    {
        if (!$form = self::getForm('conditions/' . strtolower(str_replace('\\', '/', $name)) . '.xml', $controlGroup, $formData))
        {
            return;
        }

        $form->setFieldAttribute('note', 'ruleName', $name);

        return $form->renderFieldset('general');
    }

    /**
     * Handles loading condition builder given a payload.
     * 
     * @param   array   $payload
     * 
     * @return  string
     */
    public static function initLoad($payload = [])
    {
        if (!$payload)
        {
            return;
        }

        if (!isset($payload['data']) &&
            !isset($payload['name']))
        {
            return;
        }

        if (!$data = json_decode($payload['data']))
        {
            return;
        }

        // transform object to assosiative array
        $data = json_decode(json_encode($data), true);

        // html of condition builder
        $html = '';

        $include_rules = isset($payload['include_rules']) ? $payload['include_rules'] : [];
        $exclude_rules = isset($payload['exclude_rules']) ? $payload['exclude_rules'] : [];

        foreach ($data as $groupKey => $groupConditions)
        {
            $payload = [
                'name' => $payload['name'],
                'groupKey' => $groupKey,
                'groupConditions' => $groupConditions,
                'include_rules' => $include_rules,
                'exclude_rules' => $exclude_rules
            ];
            
            $html .= self::getLayout('conditionbuilder_group', $payload);
            
        }

        return $html;
    }

    /**
     * Render a layout given its name and payload.
     * 
     * @param   string  $name
     * @param   array   $payload
     * 
     * @return  string
     */
    public static function getLayout($name, $payload)
    {
        return LayoutHelper::render($name, $payload, JPATH_PLUGINS . '/system/nrframework/layouts');
    }

    /**
     * Returns the form by binding given data.
     * 
     * @param   string  $name
     * @param   string  $controlGroup
     * @param   array   $data
     * 
     * @return  object
     */
    private static function getForm($name, $controlGroup, $data = null)
    {
        if (!file_exists(JPATH_PLUGINS . '/system/nrframework/xml/' . $name))
        {
            return;
        }
        
        $form = new \JForm('cb', ['control' => $controlGroup]);

        $form->addFieldPath(JPATH_PLUGINS . '/system/nrframework/fields');
        $form->loadFile(JPATH_PLUGINS . '/system/nrframework/xml/' . $name);

        if (!is_null($data))
        {
            $form->bind($data);
        }

        return $form;
    }
}