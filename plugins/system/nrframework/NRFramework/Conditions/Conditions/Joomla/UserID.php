<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Joomla;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class UserID extends Condition
{
	/**
     *  Returns the assignment's value
     * 
     *  @return int User ID
     */
	public function value()
	{
		return $this->user->id;
	}
}