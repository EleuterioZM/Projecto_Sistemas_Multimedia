<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Conditions;

use NRFramework\Assignments;

defined('_JEXEC') or die;

class Migrator
{
    /**
     * Migrate old Assignments data to the new Condition Builder object. 
     * 
     * @since   5.0.1
     * 
     * @param   object  $box
     * 
     * @return  void
     */
    public static function do(&$params)
    {
        if ($params->get('mirror') == '1')
        {
            $params->set('display_conditions_type', 'mirror');
            return;
        }

        $assignmentsClass = new Assignments();

        $matching_method_map = [
            'and' => 'all',
            'or'  => 'any'
        ];

        $rules = [
            0 => [
                'matching_method' => $matching_method_map[$params->get('assignmentMatchingMethod', 'and')],
                'enabled' => 1,
                'rules' => []
            ]
        ];

        foreach ($params as $paramKey => $paramValue)
        {
            if (strpos($paramKey, 'assign_') !== 0)
            {
                continue;
            }

            $oldName = str_replace('assign_', '', $paramKey);
            $newName = $assignmentsClass->aliasToClassname($oldName);

            // Skip unknown conditions
            if (!$newName)
            {
                continue;
            }

            // Skip disabled conditions
            if ($paramValue == '0')
            {
                continue;
            }

            // Date assignment doesn't use the value property
            if ($newName == 'Date\Date')
            {
                $params->set($paramKey . '_list', true);

                $publish_up   = $params->get('assign_'. $oldName .'_param_publish_up');
                $publish_down = $params->get('assign_'. $oldName .'_param_publish_down');

                \NRFramework\Functions::fixDateOffset($publish_up);
                \NRFramework\Functions::fixDateOffset($publish_down);

                $params->set('assign_'. $oldName .'_param_publish_up', $publish_up);
                $params->set('assign_'. $oldName .'_param_publish_down', $publish_down);
            }

            // Date assignment doesn't use the value property
            if ($newName == 'Date\Time')
            {
                $params->set($paramKey . '_list', true);
            }

            // Skip conditions with no value
            if (!$value = $params->get($paramKey . '_list'))
            {
                continue;
            }

            $operator = $paramValue == '1' ? 'includes' : 'not_includes';

            // These Conditions have custom operators
            if (in_array($newName, ['Date\Date', 'Date\Time']))
            {
                $operator = $paramValue == '1' ? 'equal' : 'not_equal';
            }

            if ($newName == 'Cookie')
            {
                $operatorMap = [
                    'exists'   => 'exists',
                    'not_exists'  => 'not_exists',
                    'equal'    => 'equal',
                    'not_equal' => 'not_equal',
                    'contains' => 'includes',
                    'not_contains' => 'not_includes',
                    'starts'   => 'starts_with',
                    'not_start'   => 'not_starts_with',
                    'ends'     => 'ends_with',
                    'not_end'   => 'not_ends_with',
                ];

                if ($paramValue == '2')
                {
                    switch ($value)
                    {
                        case 'exists':
                            $value = 'not_exists';
                            break;

                        case 'equal':
                            $value = 'not_equal';
                            break;

                        case 'contains':
                            $value = 'not_contains';
                            break;

                        case 'starts':
                            $value = 'not_start';
                            break;

                        case 'ends':
                            $value = 'not_end';
                            break;
                    }
                }

                $operator = $operatorMap[$value];

                $params->set('assign_cookiename_param_operator', $operator);

                $value = $params->get('assign_cookiename_param_name');
            }

            if ($newName == 'Pageviews')
            {
                $operatorMap = [
                    'exactly' => 'equal',
                    'not_equal' => 'not_equal',
                    'fewer'   => 'less_than',
                    'greater' => 'greater_than',
                ];

                if ($paramValue == '2')
                {
                    switch ($value)
                    {
                        case 'exactly':
                            $value = 'not_equal';
                            break;

                        case 'fewer':
                            $value = 'greater';
                            break;

                        case 'greater':
                            $value = 'fewer';
                            break;
                    }
                }

                $operator = $operatorMap[$value];
                $value = $params->get('assign_pageviews_param_views');
            }
            
            $data = [
                'name'     => $newName,
                'enabled'  => 1,
                'operator' => $operator,
                'value'    => $value
            ];

            // Find params
            foreach ($params as $assignParamKey => $assignParamValue)
            {
                if (strpos($assignParamKey, $paramKey . '_param') !== 0)
                {
                    continue;
                }

                if ($assignParamValue == '')
                {
                    continue;
                }

                $realParamName = str_replace($paramKey . '_param_', '', $assignParamKey);

                $data['params'][$realParamName] = $assignParamValue;
            }

            $rules[0]['rules'][] = $data;
        }

        if (!empty($rules[0]['rules']))
        {
            // Finally, set the rules
            $params->set('display_conditions_type', 'custom');
            $params->set('rules', $rules);
        }
    }
}