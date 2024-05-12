<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined( '_JEXEC' ) or die( 'Restricted access' );

class WebClient
{
	/**
	 *  Joomla Application Client
	 *
	 *  @var  object
	 */
	public static $client;

	/**
	 *  Get visitor's Device Type
	 * 
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  string    The client's device type. Can be: tablet, mobile, desktop
	 */
	public static function getDeviceType($ua = null)
	{
        if (!class_exists('Mobile_Detect'))
        {
        	\JLoader::register('Mobile_Detect', JPATH_PLUGINS . '/system/nrframework/helpers/vendors/Mobile_Detect.php');
        }

        $detect = new \Mobile_Detect(null, $ua);

        return ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');
	}

	/**
	 *  Get visitor's Operating System
	 *
	 *  @param	 string	    $ua User Agent string, if null use the implicit one from the server's enviroment
	 * 
	 *  @return  string     Possible values: any of JApplicationWebClient's OS constants (except 'iphone' and 'ipad'), 
     *                                       'ios', 'chromeos'
	 */
	public static function getOS($ua = null)
	{
        // detect iOS and CromeOS (not handled by JApplicationWebClient)
        $ua = self::getClient($ua)->userAgent;

        $ios_regex = '/iPhone|iPad|iPod/i';
        if (preg_match($ios_regex, $ua))
        {
            return 'ios';
        }

        $chromeos_regex = '/CrOS/i';
        if (preg_match($chromeos_regex, $ua))
        {
            return 'chromeos';
        }

        // use JApplicationWebClient for OS detection
		$platformInt = self::getClient($ua)->platform;
		$constants   = self::getClientConstants();
		
		if (isset($constants[$platformInt]))
		{
			return strtolower($constants[$platformInt]);
		}
	}

	/**
	 *  Get visitor's Browser name / version
	 * 
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  array
	 */
	public static function getBrowser($ua = null)
	{
		$browser = new \Joomla\CMS\Environment\Browser($ua);

		// Keep IE's name as 'ie' instead of 'msie' to prevent breaking existing assignments
		$browserName = $browser->getBrowser() == 'msie' ? 'ie' : $browser->getBrowser();

		return [
			'name'    => $browserName,
			'version' => $browser->getVersion()
		];
	}

	/**
	 *  Get the constants from JApplicationWebClient as an array using the Reflection API
	 *
	 *  @return  array
	 */
	private static function getClientConstants()
	{
		$r = new \ReflectionClass('\\Joomla\\Application\\Web\\WebClient');
		$constantsArray = $r->getConstants();

		// flip the associative array
		return array_flip($constantsArray);
	}

	/**
	 *  Get the Application Client helper
	 *  see https://api.joomla.org/cms-3/classes/Joomla.Application.Web.WebClient.html
	 * 
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  object
	 */
	public static function getClient($ua = null)
	{
		if (is_object(self::$client) && $ua == null)
		{
			return self::$client;
		}

		return (self::$client = new \Joomla\Application\Web\WebClient($ua));
	}
}