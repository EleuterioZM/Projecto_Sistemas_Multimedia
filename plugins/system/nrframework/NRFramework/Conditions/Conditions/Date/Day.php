<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Date;

defined('_JEXEC') or die;

class Day extends DateBase
{
    /**
     * Cover special cases where the user checks whether the current day is a Weekday or Weekend.
     *
     * @param  mixed $selection     The current selection
     * 
     * @return array
     */
    public function prepareSelection()
    {
        $selection = (array) $this->getSelection();

        foreach ($selection as $str)
        {
            $str = \strtolower($str);

            if (strpos($str, 'weekday') !== false)
            {
                $selection = array_merge($selection, range(1, 5));
                continue;
            }

            if (strpos($str, 'weekend') !== false)
            {
                $selection = array_merge($selection, [6, 7]);
            }
        }

        return $selection;
    }
    
    /**
     * Return a list with all different formats of the current day.
     * 
     * This returns the day in non-localized strings.
     * 
     * @return array
     */
	public function value()
	{
		return [
            $this->date->format('l', false, false), // 'Friday'
            $this->date->format('D', false, false), // 'Fri'
            $this->date->format('N', false, false), // '1' (Monday) to '7' (Sunday)
        ];
	}
}