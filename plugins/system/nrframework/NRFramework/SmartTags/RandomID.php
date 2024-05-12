<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class RandomID extends SmartTag
{
    /**
     * Returns a random ID
     * 
     * @return  string
     */
    public function getRandomID()
    {
        return bin2hex(\JCrypt::genRandomBytes(8));
    }
}