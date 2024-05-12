<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class Month extends Date
{
    /**
     * Returns the current month
     * 
     * @return  string
     */
    public function getMonth()
    {
        return $this->date->format('n');
    }
}