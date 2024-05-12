<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Date;

defined('_JEXEC') or die;

class Month extends DateBase
{
    /**
     * Returns the assignment's value
     * 
     * This returns the month in non-localized strings.
     * 
     * @return string Name of the current month
     */
	public function value()
	{
		return [
            $this->date->format('F', false, false),
            $this->date->format('M', false, false),
            $this->date->format('n', false, false),
            $this->date->format('m', false, false),
        ];
	}
}