<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Conditions;

use NRFramework\Factory;

defined('_JEXEC') or die;

/**
 *  Conditions Helper Class
 * 
 *  Singleton
 */
class ConditionsHelper
{
    /**
     *  Factory object 
     * 
     *  @var \NRFramework\Factory
     */
    protected $factory;

    /**
     *  Class constructor
     */
    public function __construct($factory = null)
    {
        $this->factory = is_null($factory) ? new Factory() : $factory;
    }

    /**
     * Get only one instance of the class
     *
     * @return object
     */
    static public function getInstance($factory = null)
    {
        static $instance = null;

		if ($instance === null)
		{
            $instance = new ConditionsHelper($factory);
		}
		
        return $instance;
    }

    /**
     * Passes a set of groups which are connected with OR comparison operator.
     * 
     * Expected object:
     * 
     * $groups = [
     *   [
     *      mathing_method => string (all|any),
     *      rules          => array
     *   ],
     *   [
     *      mathing_method => string (all|any),
     *      rules          => array
     *   ]
     *   ...
     * ];
     * 
     * @param   array  $groups
     * 
     * @return  mixed  On validation error return null, if validation runs return bool
     */
    public function passSets($groups)
    {
        $pass = null;

        // Validations
        if (!is_array($groups) OR (is_array($groups) AND empty($groups)))
        {
            return $pass;
        }

        foreach ($groups as $group)
        {
            // Skip invalid groups
            if (!isset($group['rules']) OR !is_array($group['rules']) OR (is_array($group['rules']) AND empty($group['rules'])))
            {
                continue;
            }

            $matching_method = isset($group['matching_method']) ? $group['matching_method'] : 'all';

            // If a group meets the condition, pass the check and abort so no further tests are executed.
            if ($pass = $this->passSet($group['rules'], $matching_method))
            {
                break;
            }
        }

        return $pass;
    }

    /**
     * Passes a set of rules.
     * 
     * Expected object for rules:
     * 
     * $rules = [
     *   [
     *      name     => string,
     *      value    => mixed,
     *      operator => string,
     *      params   => array
     *   ],
     *   [
     *      name     => string,
     *      value    => mixed,
     *      operator => string,
     *      params   => array
     *   ]
     *   ...
     * ];
     * 
     * @param   array   $rules
     * @param   string  $matchingMethod
     * 
     * @return  bool
     */
    public function passSet($rules, $matchingMethod)
    {
        $pass = null;

        // Validations
        if (!is_array($rules) OR (is_array($rules) AND empty($rules)))
        {
            return $pass;
        }

        foreach ($rules as $rule)
        {
            // Skip unknown rules
            if (!isset($rule['name']))
            {
                continue;
            }

            // Validate rule
            $params   = isset($rule['params'])   ? $rule['params'] : null;
            $value    = isset($rule['value'])    ? $rule['value'] : '';
            $operator = isset($rule['operator']) ? $rule['operator'] : '';

            // Run checks
            $pass = $this->passOne($rule['name'], $value, $operator, $params);

            // Check no further the Ruleset when any of the following happens:
            // 1. We expect ALL Rules to pass but one fails.
            // 2. We expect ANY Rule to pass and one does so.
            if ((!$pass AND $matchingMethod == 'all') OR ($pass AND $matchingMethod == 'any'))
            {
                break;
            }
        }

        return $pass;
    }

    /**
    * Execute given rnule
    *
    * @param  string  $name       The name of the rule. Case-sensitive.
    * @param  mixed   $selection  The value to compare with the value returned by the rule.
    * @param  string  $operator   The operator to use to do the comparison
    * @param  array   $params     Optional rule parameters

    * @return mixed   Null when the validation doesn't run properly, bool otherwize
    */
    public function passOne($name, $selection, $operator, $params = [])
    {
        if (!$rule = $this->getCondition($name, $selection, str_replace('not_', '', $operator), $params))
        {
            return;
        }

        $pass = $rule->pass();

        if (is_null($pass))
        {
            return $pass;
        }
        
		return strpos($operator, 'not_') !== false ? !$pass : $pass;
    }

    /**
    * Initialize the condition class object
    *
    * @param  string  $name       The name of the rule. Case-sensitive.
    * @param  mixed   $selection  The value to compare with the value returned by the rule.
    * @param  string  $operator   The operator to use to do the comparison
    * @param  array   $params     Optional rule parameters

    * @return mixed   Null on failure, object on success
    */
    public function getCondition($name, $selection = null, $operator = '', $params = null)
    {
        if (!$name)
        {
            return;
        }

        $class = __NAMESPACE__ . '\\Conditions\\' . $name;

        if (!class_exists($class))
        {
            return;
        }

        // Prepare rule options
        $options = [
            'selection' => $selection,
            'operator'  => str_replace('not_', '', $operator),
            'params'    => $params
        ];

        $rule = new $class($options, $this->factory);

        return $rule;
    }

    /**
     * Validate and manipulate rules before they are get stored into the database.
     *
     * @param  array $rules
     * 
     * @return void
     */
    public function onBeforeSave(&$rules)
    {
        // If its a string, transform it into an array, otherwise, use the actual value (array)
        $rules = is_string($rules) ? json_decode($rules, true) : $rules;

        if (!is_array($rules))
        {
            return;
        }
        
        foreach ($rules as &$group)
        {
            if (!isset($group['rules']))
            {
                continue;
            }

            foreach ($group['rules'] as &$rule)
            {
                if (!$condition = $this->getCondition($rule['name']))
                {
                    continue;
                }

                if (!\method_exists($condition, 'onBeforeSave'))
                {
                    continue;
                }

                $condition->onBeforeSave($rule);
            }
        }
    }
}