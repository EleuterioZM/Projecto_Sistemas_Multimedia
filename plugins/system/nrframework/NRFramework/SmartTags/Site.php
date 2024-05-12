<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class Site extends SmartTag
{
    /**
     * Returns the site email
     * 
     * @return  string
     */
    public function getEmail()
    {
        return $this->app->get('mailfrom');
    }

    /**
     * Returns the site name
     * 
     * @return  string
     */
    public function getName()
    {
        return $this->app->get('sitename');
    }

    /**
     * Returns the site URL
     * 
     * @return  string
     */
    public function getURL()
    {
        $url = $this->factory->getURI();
        return $url::root();
    }
}