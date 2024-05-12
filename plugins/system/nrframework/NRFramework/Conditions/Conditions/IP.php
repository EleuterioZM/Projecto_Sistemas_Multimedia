<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\User;
use NRFramework\Functions;
use NRFramework\Conditions\Condition;

class IP extends Condition
{
    public function prepareSelection()
    {
        return Functions::makeArray($this->getSelection());
    }

    /**
     * Checks if the user's ip address is within the specified ranges
     *
     * @return bool
     */
    public function pass()
    {
        // get the user's ip address
        $user_ip = $this->value();

        // get the supplied ip addresses/ranges as an array
        foreach ($this->getSelection() as $ip_range)
        {
            if ($this->isInRange($user_ip, $ip_range))
            {
                return true;
            }
        }

        return false;
    }

    /**
     *  Returns the assignment's value
     * 
     *  @return string User IP
     */
	public function value()
	{
		return User::getIP();
	}

    /**
     * Checks if an IP address falls within an IP range
     * Todo: factor out common logic...
     * @param string $user_ip
     * @param string $range
     * @return boolean
     */
    protected function isInRange($user_ip, $range)
    {
        if (empty($user_ip) || empty($range))
        {
            return false;
        }

        // break ip addresses/ranges into parts
        $user_ip_parts = explode('.', $user_ip);
        $ip_range_parts = explode('.', $range);

        for ($i = 0; $i < count($ip_range_parts); $i++)
        {
            $r = $ip_range_parts[$i];

            // parse and check range
            if (strpos($r, '-') !== FALSE)
            {
                list($range_start, $range_end) = explode('-', $r);
                
                // format checks...
                if (!is_numeric($range_start) || !is_numeric($range_end))
                {
                    return false;
                }
                // cast strings to integers
                $range_start = (int) $range_start;
                $range_end = (int) $range_end;

                if ($range_start > $range_end || $range_start < 0 || $range_end > 255)
                {
                    return false;
                }

                if ((int)$user_ip_parts[$i] < $range_start || (int)$user_ip_parts[$i] > $range_end)
                {
                    return false;
                }
            }
            else
            {
                // format checks...
                if (!is_numeric($r))
                {
                    return false;
                }

                $r = (int)$r;

                if ($r < 0 || $r > 255)
                {
                    return false;
                }
                
                if ((int)$user_ip_parts[$i] !== $r)
                {
                    return false;
                }
            }
        } //for loop

        return true;
    }
}