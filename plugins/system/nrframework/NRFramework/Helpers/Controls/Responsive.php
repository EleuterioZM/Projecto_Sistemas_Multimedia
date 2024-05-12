<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Controls;

defined('_JEXEC') or die;

class Responsive
{
	/**
	 * Responsive breakpoints
	 * 
	 * @var  array
	 */
	public static $breakpoints = [
		'desktop',
		'tablet',
		'mobile'
	];
    
    /**
     * Given a responsive value, we prepare its CSS for each breakpoint.
     * 
     * @param   array   $value
     * @param   string  $prefix
     * @param   string  $unit
     * 
     * @return  mixed
     */
    public static function getResponsiveControlValue($value, $prefix = '', $unit = '')
    {
        if (!is_string($unit))
        {
            return;
        }
        
        if (!is_string($prefix) || empty($prefix))
        {
            return;
        }
        
        if (is_string($value) && $value === '')
        {
            return;
        }
        
        if (!$value)
        {
            return;
        }
        
        if (!is_array($value))
        {
            $value = self::prepareResponsiveControlValue($value);
        }

        $css = [];
        
        foreach ($value as $breakpoint => $_value)
        {
            if ($_value === '' || is_null($_value))
            {
                continue;
            }
            
            $css[$breakpoint] = $_value;
        }
        
        if (empty($css))
        {
            return;
        }

        /**
         * All values are duplicates, return the current breakpoint value.
         * 
         * if given [5, 5, 5, 5] to print the margin in pixels, do not return `margin: 5px 5px 5px 5px`.
         * Rather return `margin: 5px`
         */
        if (count(array_unique($css)) === 1)
        {
            $first_element = reset($css);

            if (is_array($first_element) || is_object($first_element))
            {
                return;
            }
            
            return [key($css) => $prefix . ': ' . $first_element . $unit . ';'];
        }

        // add unit suffix
        $css = preg_filter('/^/', $prefix . ': ', $css);
        $css = preg_filter('/$/', $unit . ';', $css);

        return $css;
    }

    /**
     * Prepares the value
     * 
     * @param   mixed  $value
     * 
     * @return  array
     */
    public static function prepareResponsiveControlValue($value)
    {
        if (!$value)
        {
            return;
        }
        
        if (!is_array($value))
        {
            return [
                'desktop' => $value
            ];
        }
        
        return $value;
    }
}