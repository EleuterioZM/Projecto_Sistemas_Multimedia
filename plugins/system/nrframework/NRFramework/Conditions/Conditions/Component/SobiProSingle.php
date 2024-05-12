<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class SobiProSingle extends SobiProBase
{
    /**
	 *  Pass check
	 *
	 *  @return  bool
	 */
	public function pass()
	{
        return $this->passSinglePage();
    }
    
    /**
     *  Returns the assignment's value
     * 
     *  @return int
     */
    public function value()
    {
        return $this->request->id;
    }
}