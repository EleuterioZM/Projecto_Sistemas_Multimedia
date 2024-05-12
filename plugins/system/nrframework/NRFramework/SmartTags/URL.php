<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class URL extends SmartTag
{
    /**
     * Returns the URL
     * 
     * @return  string
     */
    public function getURL()
    {
        return $this->factory->getURI()->toString();
    }

    /**
     * Returns the URL encoded
     * 
     * @return  string
     */
    public function getEncoded()
    {
        return urlencode($this->factory->getURI()->toString());
    }

    /**
     * Returns the site URL
     * 
     * @return  string
     */
    public function getPath()
    {
        $url = $this->factory->getURI();
        return $url::current();
    }
}