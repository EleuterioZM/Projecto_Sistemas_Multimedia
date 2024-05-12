<?php

/**
 *  @author          Tassos.gr <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework;

use \NRFramework\WebClient;
use \NRFramework\CacheManager;

defined('_JEXEC') or die;

/**
*  Framework Factory Class
*  
*  Used to decouple the framework from it's dependencies and make unit testing easier.
*
*  @todo Rename class to Container and make all methods static.
*/
class Factory
{
    public function isFrontend()
    {
        return $this->getApplication()->isClient('site');
    }

    public static function getCondition($name)
    {
        return \NRFramework\Conditions\ConditionsHelper::getInstance()->getCondition($name);
    }

    public function getDbo()
    {
        return \JFactory::getDbo();
    }

    public function getApplication()
    {
        return \JFactory::getApplication();
    }

    public function getCookie($cookie_name)
    {
        return \JFactory::getApplication()->input->cookie->get($cookie_name);
    }

    public function getDocument()
    {
        return \JFactory::getDocument();
    }

    public function getUser($id = null)
    {
        return \NRFramework\User::get($id);
    }

    public function getCache()
    {
        return CacheManager::getInstance(\JFactory::getCache('novarain', ''));
    }

    public function getDate($date = 'now', $tz = null)
    {
        return \JFactory::getDate($date, $tz);
    }

    public function getURI()
    {
        return \JURI::getInstance();
    }

    public function getURL()
    {
        return \JURI::getInstance()->toString();
    }

    public function getLanguage()
    {
        return \JFactory::getLanguage();
    }

    public function getSession()
    {
        return \JFactory::getSession();
    }

    public function getDevice()
    {
        return WebClient::getDeviceType();
    }

    public function getBrowser()
    {
        return WebClient::getBrowser();
    }

    public function getExecuter($php_code)
    {
        return new \NRFramework\Executer($php_code);
    }
}