<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\WebClient;
use NRFramework\Functions;
use NRFramework\Conditions\Condition;

class OS extends Condition
{
    /**
     *  Check the client's operating system
     *
     *  @return bool
     */
    public function prepareSelection()
    {
        $selection = Functions::makeArray($this->getSelection());

        // backwards compatibility check
        // replace 'iphone' and 'ipad' selection values with 'ios'
        return array_map(function($os_selection)
        {
            if ($os_selection === 'iphone' || $os_selection === 'ipad')
            {
                return 'ios';
            }
            return $os_selection;
        }, $selection);
    }

    /**
     *  Returns the assignment's value
     * 
     *  @return string OS name
     */
	public function value()
	{
		return WebClient::getOS();
	}
}