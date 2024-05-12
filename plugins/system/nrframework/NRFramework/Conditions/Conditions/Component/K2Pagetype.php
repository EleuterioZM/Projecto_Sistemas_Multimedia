<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class K2Pagetype extends K2Base
{
    /**
     *  Pass check for K2 page types
     *
     *  @return bool
     */
    public function pass()
    {
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
        }

        return parent::pass();
    }

    /**
     *  Returns the assignment's value
     * 
     *  @return string Pagetype
     */
	public function value()
	{
		return $this->getPageType();
    }
}