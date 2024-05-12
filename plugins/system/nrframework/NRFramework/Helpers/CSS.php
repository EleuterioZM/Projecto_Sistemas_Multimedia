<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers;

defined('_JEXEC') or die;

class CSS
{
	/**
	 * Transforms an array of CSS variables (key, value) to
	 * a CSS output.
	 * 
	 * @param   array   $cssVars
	 * @param   string  $namespace
	 * 
	 * @return  string
	 */
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