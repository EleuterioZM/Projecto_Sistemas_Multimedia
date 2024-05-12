<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

use NRFramework\User;

defined('_JEXEC') or die('Restricted access');

class IP extends SmartTag
{
    /**
     * Returns the IP address
     * 
     * @return  string
     */
    public function getIP()
    {
        return User::getIP();
    }
}