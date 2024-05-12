<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

use NRFramework\Cache;
use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );

class Extension
{
	/**
	 * Indicates the base url of Tassos.gr Joomla Extensions
	 *
	 * @var string
	 */
	public static $product_base_url = 'https://www.tassos.gr/joomla-extensions';

	/**
	 * Array including already loaded extensions
	 *
	 * @var array
	 */
	public static $cache = [];

	/**
	 * Get extension ID
	 *
	 * @param	string	$element	The extension element name
	 * @param	string	$type		The extension type: component, plugin, library e.t.c
	 * @param	mixed	$folder		The plugin folder: system, content e.t.c
	 *
	 * @return	mixed 	False on failure, Integer on success
	 */
	public static function getID($element, $type = 'component', $folder = null)
	{
		if (!$extension = self::get($element, $type, $folder))
		{
			return false;
		}

		return (int) $extension['extension_id'];
	}

	/**
	 * Get extension data by ID
	 *
	 * @param	string	$extension_id		The extension primary key
	 * 
	 * @return	void
	 */
	public static function getByID($extension_id)
	{
		// Check if element is already cached
		if (isset(self::$cache[$extension_id]))
		{
			return self::$cache[$extension_id];
		}

		// Let's call the database
		$db = \JFactory::getDBO();	

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
			->where($db->quoteName('extension_id') . ' = ' . $extension_id);
			
		$db->setQuery($query);

		return self::$cache[$extension_id] = $db->loadAssoc();
	}

	/**
	 * Get extension information from database
	 *
	 * @param	string	$element	The extension element name
	 * @param	string	$type		The extension type: component, plugin, library e.t.c
	 * @param	mixed	$folder		The plugin folder: system, content e.t.c
	 *
	 * @return	array
	 */
    public static function get($element, $type = 'component', $folder = null)
    {
		// Check if element is already cached
		$hash = md5($element . '_' . $type . '_' . $folder);
		if (isset(self::$cache[$hash]))
		{
			return self::$cache[$hash];
		}

		// Let's call the database
		$db = \JFactory::getDBO();

		switch ($type)
		{
			case 'component':
				$element = 'com_' . str_replace('com_', '', $element);
				break;
			case 'module':
				$element = 'mod_' . str_replace('mod_', '', $element);
				break;
		}
		
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element))
            ->where($db->quoteName('type') . ' = ' . $db->quote($type));

        if (!is_null($folder))
        {
            $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        }

		$db->setQuery($query);

		return self::$cache[$hash] = $db->loadAssoc();
	}

	/**
	 * Get Novarain Framework plugin data
	 *
	 * @return array
	 */
	public static function getFramework()
	{
		return self::get('nrframework', 'plugin', 'system');
	}

	/**
	 * Helper method to check if a plugin is enabled
	 *
	 * @param	string	$element	The extension element name
	 * @param	string	$type		The extension type: component, plugin, library e.t.c
	 *
	 * @return  boolean
	 */
	public static function pluginIsEnabled($element, $folder = 'system') 
	{
		return self::isEnabled($element, 'plugin', $folder);
	}

	/**
	 * Helper method to check if a component is enabled
	 *
	 * @param	string	$element	The component element name
	 *
	 * @return	boolean
	 */
	public static function componentIsEnabled($element) 
	{
		return self::isEnabled($element);
	}

	/**
	 * Checks if an extension is enabled
	 *
	 * @param	string	$element	The extension element name
	 * @param	string	$type		The extension type: component, plugin, library e.t.c
	 * @param	mixed	$folder		The plugin folder: system, content e.t.c
	 *
	 * @return	boolean
	 */
	public static function isEnabled($element, $type = 'component', $folder = 'system')
	{
		switch ($type)
		{
			case 'component':
				if (!$extension = self::get($element))
				{
					return false;
				}

				return (bool) $extension['enabled'];
				break;

			case 'plugin':
				if (!$extension = self::get($element, $type = 'plugin', $folder))
				{
					return false;
				}
		
				return (bool) $extension['enabled'];
				break;
		}
	}

	/**
     *  Checks if an extension is installed
     *
     *  @param   string  $extension  The extension element name
     *  @param   string  $type       The extension's type 
     *  @param   string  $folder     Plugin folder
     *
     *  @return  boolean             Returns true if extension is installed
     */
    public static function isInstalled($extension, $type = 'component', $folder = 'system')
    {
        $db = \JFactory::getDbo();

        switch ($type)
        {
			case 'component':
				$extension_data = self::get('com_' . str_replace('com_', '', $extension));
				return isset($extension_data['extension_id']);
                break;

            case 'plugin':
                return \JFile::exists(JPATH_PLUGINS . '/' . $folder . '/' . $extension . '/' . $extension . '.php');

            case 'module':
                return (\JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/' . $extension . '.php')
                    || \JFile::exists(JPATH_ADMINISTRATOR . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
                    || \JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/' . $extension . '.php')
                    || \JFile::exists(JPATH_SITE . '/modules/mod_' . $extension . '/mod_' . $extension . '.php')
                );

            case 'library':
                return \JFolder::exists(JPATH_LIBRARIES . '/' . $extension);
        }

        return false;
	}
	
	/**
	 * Discover extension's name based on the query string
	 *
	 * @param	boolean	$translate	If set to yes, the name will be returned translated
	 * 
	 * @return	string
	 */
	public static function getExtensionNameByRequest($translate = false)
	{
		$input  = \JFactory::getApplication()->input;
		$option = $input->get('option');

		switch ($option)
		{
			case 'com_fields':
				$name = 'plg_system_acf';
				break;
			case 'com_plugins':
				$plugin = self::getByID($input->get('extension_id'));

				if (is_array($plugin))
				{
					$name = $plugin['name'];
				}
				break;
			default:
				$name = $option;
				break;
		}

		if ($translate)
		{
			$name = explode(' - ', \JText::_($name));
			return end($name);
		}

		return $name;
	}

	/**
	 * Returns Tassos.gr extension checkout URL
	 *
	 * @param	string	$name		 The extension's element name
	 * @param	string	$coupon		 The coupon code to use
	 * @param	bool	$append_utm	 Set whether to append the UTM parameters
	 *
	 * @return	string
	 */
	public static function getTassosExtensionUpgradeURL($name = null, $coupon = 'FREE2PRO', $append_utm = true)
	{
		$name = is_null($name) ? strtolower(self::getExtensionNameByRequest()) : $name;

		switch ($name)
		{
			case 'com_gsd':
			case 'plg_system_gsd':
				$path = 'google-structured-data-markup/subscribe/google-structured-data-professional';
				break;
			case 'com_rstbox':
				$path = 'engagebox/subscribe/engagebox-professional';
				break;
			case 'com_convertforms':
				$path = 'convert-forms/subscribe/convert-forms-professional';
				break;
			case 'plg_system_tweetme':
				$path = 'tweetme/subscribe/tweetme-professional';
				break;
			case 'plg_system_acf':
				$path = 'advanced-custom-fields/subscribe/advanced-custom-fields-professional';
				break;
			default:
				$path = '';
		}

		// Google Analytics UTM Parameters
        $suffix = $append_utm ? '&utm_source=Joomla&utm_medium=upgradebutton&utm_campaign=freeversion' : '';

		return self::$product_base_url . '/' . $path . '/sign-up?coupon_code=' . $coupon . $suffix;
	}

	public static function getProductAlias($extension)
	{
		$extension = is_null($extension) ? self::getExtensionNameByRequest() : $extension;

		switch ($extension)
		{
			case 'com_gsd': case 'plg_system_gsd': return 'google-structured-data-markup';
			case 'com_rstbox': return 'engagebox';
			case 'com_convertforms': return 'convert-forms';
			case 'plg_system_tweetme': return 'tweetme';
			case 'plg_system_acf': return 'advanced-custom-fields';
			case 'plg_system_restrictcontent':
			case 'com_restrictcontent': return 'restrict-conten';
		}
	}

	public static function getProductURL($extension) 
	{
		return self::$product_base_url . '/' . self::getProductAlias($extension);
	}

	public static function getPath($element)
	{
		$parts = explode('_', $element);

		switch ($parts[0])
		{
			case 'com':
				return JPATH_ADMINISTRATOR . '/components/' . $element;
			case 'plg':
				return JPATH_SITE . '/plugins/' . $parts[1] . '/' . $parts[2];
		}
	}

	public static function getVersion($extension, $include_type = false)
	{
		$xml = self::getXML($extension);

		if (!$xml || !isset($xml->version))
		{
			return;
		}

		$version = (string) $xml->version;

		// If enabled, it returns EngageBox Pro
		if ($include_type)
		{
			$isPro = self::isPro($extension);
			$version_type = $isPro ? 'Pro' : 'Free';
			$version .= ' ' . $version_type;
		}

		return $version;
	}

	public static function elementToAlias($element)
	{
		$parts = explode('_', $element);
		return end($parts);
	}

	public static function getXML($element)
	{
		if (!$path = self::getPath($element))
		{
			return;
		}

		$extension_alias = self::elementToAlias($element);
		$xml = $path . '/' . $extension_alias . '.xml';

		return simplexml_load_file($xml);
	}

	/**
	 * Returns a URL where we can check for extension updates.
	 *
	 * @param  strong $extension
	 *
	 * @return mixed  Null of fail, String on success
	 */
	public static function getUpdateServer($extension)
	{
		$xml = self::getXML($extension);

		if (!$xml || !isset($xml->updateservers))
		{
			return;
		}

		$updateserver = trim($xml->updateservers->server);

		// Remove unwanted string added by Free / Pro versions
		$pp = strpos($updateserver, '@');
		if ($pp !== false)
		{
			$updateserver = substr($updateserver, 0, $pp);
		}

		return $updateserver;
	}

	/**
	 * Get the latest extension version from the remote update server
	 *
	 * @param  string $extension
	 *
	 * @return mixed	Null on failure, String on success
	 */
	public static function getLatestVersion($extension)
	{
		// Get the extension's update server URL
		if (!$updateserver = self::getUpdateServer($extension))
		{
			return;
		}

		// Call the Update Server and make sure the response is valid
		$response = \JHttpFactory::getHttp()->get($updateserver);

		if ($response->code != 200 || strpos($response->body, '<updates>') === false)
		{
			return;
		}

		$body = new \SimpleXMLElement($response->body);

		$version = (string) $body->update[0]->version;

		return $version;
	}

	/**
	 * Check if we have the Pro version of the extension
	 *
	 * @param  string $element
	 *
	 * @return bool
	 */
	public static function isPro($element)
	{
		if (!$path = self::getPath($element))
		{
			return false;
		}

		$versionFile = $path . '/version.php';

		// If version file does not exist we assume a PRO version
		if (!\JFile::exists($versionFile))
		{
			return true;
		}

		require $versionFile;

		// If the NR_PRO variable is not set we're probably under development mode. Assume a Pro version.
		if (!isset($NR_PRO))
		{
			return true;
		}

		return (bool) $NR_PRO;
	}

	/**
	 * Checks whether an extension is outdated.
	 * 
	 * @param   string  $extension
	 * @param   int     $days_old
	 * 
	 * @return  bool
	 */
	public static function isOutdated($extension, $days_old = 120)
	{
        $versionFile = Functions::getExtensionPath($extension) . "/version.php";

        if (!file_exists($versionFile))
        {
			return false;
		}
		
		require $versionFile;

		if (!isset($RELEASE_DATE))
		{
			return false;
		}

		if (!$then = strtotime($RELEASE_DATE))
		{
			return false;
		}
		
		$days_old = (int) $days_old;
		$now = time();
		$diff = $now - $then;
		$days_diff = round($diff / (60 * 60 * 24));
		
		if ($days_diff <= $days_old)
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the geolocation plugin needs an update.
	 * 
	 * @return  bool
	 */
    public static function geoPluginNeedsUpdate()
    {
        // Check if TGeoIP plugin is enabled
        if (!self::pluginIsEnabled('tgeoip'))
        {
            return false;
        }

        $plugin_path = JPATH_PLUGINS . '/system/tgeoip/';

        // Load plugin language (Needed by Joomla 4)
        \JFactory::getLanguage()->load('plg_system_tgeoip', $plugin_path);

        // Load TGeoIP classes
        @include_once $plugin_path . 'vendor/autoload.php';
        @include_once $plugin_path . 'helper/tgeoip.php';

        if (!class_exists('TGeoIP'))
        {
            return false;
        }

        // Check if database needs update. 
        $geo = new \TGeoIP();
        if (!$geo->needsUpdate())
        {
            return false;
        }

        // Database is too old and needs an update! Let's inform user.
        return true;
    }

    /**
     * Returns the extension's JED URL.
     * 
     * @param   string  $xml_folder
     * 
     * @return  string
     */
    public static function getExtensionJEDURL($xml_folder = null)
    {
        if (empty($xml_folder))
        {
            return;
        }

        $url = 'https://extensions.joomla.org/extensions/extension/';

        switch ($xml_folder) {
            case 'com_rstbox':
                $url .= 'style-a-design/popups-a-iframes/engage-box';
                break;
            case 'com_convertforms':
                $url .= 'contacts-and-feedback/forms/convert-forms';
                break;
            case 'plg_system_acf':
                $url .= 'authoring-a-content/content-construction/advanced-custom-fields';
                break;
            case 'plg_system_gsd':
                $url .= 'search-a-indexing/web-search/google-structured-data';
                break;
            case 'tm_mailchimpuserautoadd':
                $url .= 'marketing/mailing-a-newsletter-bridges/user-auto-add-to-mailchimp-for-joomla';
                break;
            case 'tweetme':
                $url .= 'social-web/social-share/tweetme';
                break;
            case 'mod_webhotelier':
                $url .= 'vertical-markets/booking-a-reservations/webhotelier-booking-form';
                break;
        }
        
        return $url;
    }

	/**
	 * Returns the installation date of an extension given its extension element.
	 * 
	 * @param   string  $element
	 * 
	 * @return  string
	 */
	public static function getInstallationDate($element = null)
	{
		$alias = self::getExtensionDataFileAlias($element);
		
		$path = self::getExtensionsDataFilePath();
		
		// If file does not exist, abort
		if (!file_exists($path))
		{
			return;
		}

		// If file exists, retrieve its contents
		$content = file_get_contents($path);

		// Decode it
		if (!$content = json_decode($content, true))
		{
			return;
		}

		// If no installation date exists, abort
		if (!isset($content[$alias]))
		{
			return;
		}

		// Ensure install date exists
		if (!isset($content[$alias]['install_date']))
		{
			return;
		}
		
		return $content[$alias]['install_date'];
	}

	/**
	 * Sets the installation date of an extension.
	 * 
	 * @param   string  $element
	 * @param   string  $install_date
	 * 
	 * @return  bool
	 */
	public static function setInstallationDate($element = null, $install_date = null)
	{
		$alias = self::getExtensionDataFileAlias($element);
		
		$path = self::getExtensionsDataFilePath();
		
		// If file does not exist, abort
		if (!file_exists($path))
		{
			file_put_contents($path, json_encode([
				$alias => [
					'install_date' => $install_date
				]
			]));
			return;
		}

		// If file exists, retrieve its contents
		$content = file_get_contents($path);

		// Decode it
		$content = json_decode($content, true);

		if (!isset($content[$alias]))
		{
			$content[$alias]['install_date'] = $install_date;
		}
		else
		{
			foreach ($content as $key => &$value)
			{
				if ($key !== $alias)
				{
					continue;
				}

				if (isset($value['install_date']))
				{
					return false;
				}
				
				$value['install_date'] = $install_date;
			}
		}

		file_put_contents($path, json_encode($content));
	}

	/**
	 * Returns all extensions file details.
	 * 
	 * @return  array
	 */
	public static function getExtensionsFileDetails()
	{
		$file = self::getExtensionsDataFilePath();

		if (!file_exists($file))
		{
			return [];
		}

		if (!$data = file_get_contents($file))
		{
			return [];
		}

		if (!$data = json_decode($data, true))
		{
			return [];
		}

		return $data;
	}

	/**
	 * Returns an extension's alias used to find an extensions data within the extensions.json data file.
	 * 
	 * @param   string  $element
	 * 
	 * @return  string
	 */
	public static function getExtensionDataFileAlias($element)
	{
		$element = str_replace('com_', '', $element);

		switch ($element) {
			case 'rstbox':
				$element = 'engagebox';
				break;
		}

		return $element;
	}

	/**
	 * The file path that stores all extensions data.
	 * 
	 * @return  string
	 */
	public static function getExtensionsDataFilePath()
	{
		return JPATH_SITE . '/media/plg_system_nrframework/data/extensions.json';
	}

	/**
	 * Returns all extensions details.
	 * 
	 * @return  array
	 */
	public static function getExtensionsDetails()
	{
		return [
			'engagebox' => [
				'extension' => 'com_rstbox',
				'type' => 'component'
			],
			'gsd' => [
				'extension' => 'com_gsd',
				'type' => 'component'
			],
			'acf' => [
				'extension' => 'acf',
				'type' => 'plugin'
			],
			'convertforms' => [
				'extension' => 'com_convertforms',
				'type' => 'component'
			]
		];
	}

	/**
	 * Returns the number of tassos.gr installed extensions.
	 * 
	 * @return  int
	 */
	public static function getTotalInstalledExtensions()
	{
		$installed = 0;
		
		foreach (self::getExtensionsDetails() as $key => $value)
		{
			if (!self::isInstalled($value['extension'], $value['type']))
			{
				continue;
			}

			$installed++;
		}

		return $installed;
	}

	/**
	 * Returns the number of users active subscription plans.
	 * 
	 * @param   array  $license_data
	 * 
	 * @return  int
	 */
	public static function getUserTotalPaidPlans($license_data = [])
	{
		if (!$license_data)
		{
			return 0;
		}

		$count = 0;

		foreach ($license_data as $key => $value)
		{
			if (!isset($value['active']))
			{
				continue;
			}
			
			if (!$value['active'])
			{
				continue;
			}
			
			$count++;
		}
		
		return $count;
	}
}