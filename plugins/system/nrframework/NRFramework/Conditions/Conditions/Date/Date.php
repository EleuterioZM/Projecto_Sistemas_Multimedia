<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Date;

defined('_JEXEC') or die;

class Date extends DateBase
{
    /**
	 *  Checks if current date passes the given date range. 
	 *  Dates must be always passed in format: Y-m-d H:i:s
	 *
	 *  @return  bool
	 */
	public function pass()
	{
		$publish_up   = $this->params->get('publish_up');
		$publish_down = $this->params->get('publish_down');

        // No valid dates
		if (!$publish_up && !$publish_up)
		{
			return false;
		}
		
		$up   = $publish_up   ? $this->getDate($publish_up)   : null;
		$down = $publish_down ? $this->getDate($publish_down) : null;

        return $this->checkRange($up, $down);
    }
    
    /**
     *  Returns the assignment's value
     * 
     *  @return \Date Current date
     */
	public function value()
	{
		return $this->date;
	}

	/**
	 * This method is called before the value of the condition is stored into the database.
	 * 
	 * Dates should be always stored in the database in GMT. Thus, we remove the timezone offset from the date.
	 *
	 * @param  array $rule	The condition object.
	 * 
	 * @return void
	 */
	public function onBeforeSave(&$rule)
	{
		\NRFramework\Functions::fixDateOffset($rule['params']['publish_up']);
		\NRFramework\Functions::fixDateOffset($rule['params']['publish_down']);
	}
}