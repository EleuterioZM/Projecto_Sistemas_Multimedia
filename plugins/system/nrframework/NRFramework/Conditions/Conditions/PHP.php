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

class PHP extends Condition 
{
	/**
	 *  Pass check Custom PHP
	 *
	 *  @return  bool
	 */
	public function pass()
	{
		return (bool) $this->value();
	}
	
	public function value()
	{
		// Enable buffer output
		ob_start();
		$pass = $this->factory->getExecuter($this->selection)->run();
		ob_end_clean();

		return $pass;
	}
}