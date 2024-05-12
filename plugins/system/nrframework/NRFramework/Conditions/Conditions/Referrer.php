<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

class Referrer extends URLBase
{
   	/**
   	 *  Pass Referrer URL. 
   	 *
   	 *  @return  bool   Returns true if the Referrer URL contains any of the selection URLs 
   	 */
   	public function pass()
   	{
		return $this->passURL($this->value());
    }

    /**
     *  Returns the assignment's value
     * 
     *  @return string Referrer URL
     */
	public function value()
	{
		return $this->app->input->server->get('HTTP_REFERER', '', 'RAW');
	}
}