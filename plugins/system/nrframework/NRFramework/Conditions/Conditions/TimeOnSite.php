<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class TimeOnSite extends Condition
{
    /**
     *  Returns the assignment's value
     * 
     *  @return int Time on site in seconds
     */
	public function value()
	{
		return $this->getTimeOnSite();
    }
    
    /**
     *  Returns the user's time on site in seconds
     * 
     *  @return int
     */
    public function getTimeOnSite()
    {
		if (!$sessionStartTime = strtotime($this->getSessionStartTime()))
		{
			return;
		}

		$dateTimeNow = strtotime(\NRFramework\Functions::dateTimeNow());
		return $dateTimeNow - $sessionStartTime;
    }

    /**
     *  Returns the sessions start time
     * 
     *  @return string
     */
    private function getSessionStartTime()
    {
        $session = $this->factory->getSession();
        
        $var = 'starttime';
        $sessionStartTime = $session->get($var);

        if (!$sessionStartTime)
        {
            $date = \NRFramework\Functions::dateTimeNow();
            $session->set($var, $date);
        }

        return $session->get($var);
    }
}