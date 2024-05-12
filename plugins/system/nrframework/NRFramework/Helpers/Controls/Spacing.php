<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Controls;

defined('_JEXEC') or die;

class Spacing
{
    /**
     * Default Spacing Control Positions.
     * 
     * @var  array
     */
    protected static $spacing_positions = ['top', 'right', 'bottom', 'left'];
    
    /**
     * Returns the CSS of the spacing control.
     * 
     * @param   array   $value
     * @param   string  $prefix
     * @param   string  $breakpoint
     * @param   string  $unit
     * 
     * @return  string
     */
    public static function getResponsiveSpacingControlValue($value, $prefix = '', $unit = '', $breakpoint = '')
    {
        $value = self::prepareSpacingControlValue($value, 'desktop');

        if (is_null($value))
        {
            return;
        }

        // Return the value for a specific breakpoint
        if (!empty($breakpoint) && is_string($breakpoint))
        {
            if (!isset($value[$breakpoint]))
            {
                return;
            }

            if (!is_array($value[$breakpoint]) && (string) $value[$breakpoint] !== '0')
            {
                return;
            }

            return $prefix . ': ' . self::getSpacingValue($value[$breakpoint], $unit) . ';';
        }
        
        // Return the value for all breakpoints
        $css = [];

        foreach ($value as $_breakpoint => $values)
        {
            // remove linked property
            if (isset($values['linked']))
            {
                unset($values['linked']);
            }

            if (!$value = self::getSpacingValue($values, $unit))
            {
                continue;
            }
    
            $css[$_breakpoint] = $prefix . ': ' . $value . ';';
        }

        return $css;
    }

    /**
     * Prepares the value
     * 
     * @param   mixed   $value
     * @param   string  $breakpoint
     * 
     * @return  array
     */
    public static function prepareSpacingControlValue($value, $breakpoint = 'desktop')
    {
        if (is_null($value))
        {
            return;
        }

        if (!is_array($value))
        {
            $new_value = [];
            foreach (static::$spacing_positions as $pos)
            {
                $new_value[$pos] = $value;
            }

            if (!empty($breakpoint) && is_string($breakpoint))
            {
                return [
                    $breakpoint => $new_value
                ];
            }
            else
            {
                return $new_value;
            }
        }

        // If no breakpoint exists in the given value, set it to the given $breakpoint.
        if ((!isset($value['desktop']) && !isset($value['tablet']) && !isset($value['mobile'])) && ($breakpoint && !isset($value[$breakpoint])))
        {
            return [$breakpoint => $value];
        }
        
        return $value;
    }

    /**
     * Returns the value of a spacing control (margin, padding).
     * 
     * @param   array   $value
     * @param   string  $unit
     * 
     * @return  string
     */
    public static function getSpacingValue($value, $unit = '')
    {
        if (!is_string($unit))
        {
            return;
        }

        if (is_string($value) && $value === '')
        {
            return;
        }

        if (!$value || !is_array($value))
        {
            $value = self::prepareSpacingControlValue($value, false);
        }

        // If its a multi-dimensional array, return
        if (count($value) !== count($value, COUNT_RECURSIVE))
        {
            return;
        }

        try {
            // If all values are empty, return
            if (empty(array_filter($value, 'strlen')))
            {
                return;
            }
        }
        catch (\Exception $ex)
        {
            return;
        }
        
        $return = [];

        foreach (static::$spacing_positions as $pos)
        {
            $return[$pos] = isset($value[$pos]) && $value[$pos] !== '' ? $value[$pos] : 0;
        }

        if (empty($return))
        {
            return;
        }

        /**
         * All values are duplicates, return only 1 number with their unit.
         * 
         * Example: Given [5, 5, 5, 5] to print the margin in pixels, do not return `margin: 5px 5px 5px 5px`.
         * Rather return `margin: 5px`
         */
        if (count(array_unique($return)) === 1)
        {
            return reset($return) . $unit;
        }

        // add unit suffix
        $return = preg_filter('/$/', $unit, $return);

        return implode(' ', $return);
    }

    /**
     * Checks whether the spacing value is empty.
     * 
     * @param   array    $value
     * 
     * @return  boolean
     */
    public static function isEmpty($value)
    {
        if (!is_array($value))
        {
            return false;
        }
        
        foreach (static::$spacing_positions as $pos)
        {
            if (!isset($value[$pos]) || (empty($value[$pos]) && (string) $value[$pos] !== '0'))
            {
                continue;
            }

            return false;
        }

        return true;
    }
}