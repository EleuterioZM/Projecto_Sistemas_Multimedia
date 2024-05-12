<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class Page extends SmartTag
{
    /**
     * Returns the page title
     * 
     * @return  string
     */
    public function getTitle()
    {
        return $this->doc->getTitle();
    }

    /**
     * Returns the page description
     * 
     * @return  string
     */
    public function getDesc()
    {
        return $this->doc->getMetaData('description');
    }

    /**
     * Returns the page keywords
     * 
     * @return  string
     */
    public function getKeywords()
    {
        return $this->doc->getMetaData('keywords');
    }

    /**
     * Returns the locale
     * 
     * @return  string
     */
    public function getLang()
    {
        return $this->doc->getLanguage();
    }

    /**
     * Returns the language code used in URLs
     * 
     * @return  string
     */
    public function getLangURL()
    {
        return explode('-',  $this->doc->getLanguage())[0];
    }

    /**
     * Returns the page generator
     * 
     * @return  string
     */
    public function getGenerator()
    {
        return $this->doc->getGenerator();
    }

    /**
     * Returns the browser title
     * 
     * @return  string
     */
    public function getBrowserTitle()
    {
		if (!$menu = $this->app->getMenu()->getActive())
		{
            return '';
        }
        
        return $menu->getParams()->get('page_title');
    }
}