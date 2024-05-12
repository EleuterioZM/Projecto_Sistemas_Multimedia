<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/compatibility.php');

if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}

		echo '</pre>';
		exit;
	}
}
if (!function_exists('vd')) {

	function vd()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		exit;
	}
}

if (!function_exists('pdump')) {
	function pdump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			print_r($arg);
		}
		echo '</pre>';
		exit;
	}
}

use Joomla\String\StringHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Folder;
use Foundry\Libraries\Responsive;
use Foundry\Libraries\Connector;
use Foundry\Libraries\Date;
use Foundry\Libraries\Exception;
use Foundry\Tables\Table;
use Foundry\Models\Mail;
use Foundry\Libraries\Pagination;
use Foundry\Libraries\Giphy;
use Foundry\Libraries\Textavatar;

/**
 * Helpers does not need to be aware of the component.
 * It is just a set of utility functions that can be used throughout the extensions.
 *
 */
class FH
{
	/**
	 * Loads vendor libraries from foundry
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function autoload()
	{
		require_once(__DIR__ . '/vendor/autoload.php');
	}

	/**
	 * Creates an instance of the connector library
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function connector($url = '')
	{
		$connector = new Connector($url);

		return $connector;
	}

	/**
	 * Checks for valid token
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function checkToken()
	{
		return JSession::checkToken('request') or jexit('Invalid Token');
	}

	/**
	 * Clears the cache in the CMS
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function clearCache()
	{
		$arguments = func_get_args();

		$cache = JFactory::getCache();

		foreach ($arguments as $argument) {
			$cache->clean($argument);
		}

		return true;
	}

	/**
	 * Creates a new date instance
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function date($current = '', $offset = null)
	{
		return new Date($current, $offset);
	}

	/**
	 * Renders the exception library
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function exception($message, $type = FD_ERROR, $previous = null)
	{
		$lib = new Exception($message, $type, $previous);

		return $lib;
	}

	/**
	 * Simple implementation to extract keywords from a string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function extractKeywords($string)
	{
		mb_internal_encoding('UTF-8');

		$stopwords = [];
		$string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));

		$matchWords = array_filter(explode(' ',$string), function ($item) use ($stopwords) {
			return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 2 || is_numeric($item));
		});

		$wordCountArr = array_count_values($matchWords);

		arsort($wordCountArr);
		return array_keys(array_slice($wordCountArr, 0, 10));
	}

	/**
	 * Escapes a string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function escape($str)
	{
		return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Retrieve the null date value
	 *
	 * @since   1.1.3
	 * @access  public
	 */
	public static function getNullDate()
	{
		static $date = null;

		if (is_null($date)) {
			$db = JFactory::getDBO();
			$date = $db->getNullDate();
		}

		return $date;
	}

	/**
	 * Retrieve the current site language tag
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function getCurrentLanguageTag()
	{
		static $tag = null;

		if (is_null($tag)) {
			$tag = JFactory::getLanguage()->getTag();
		}

		return $tag;
	}

	/**
	 * Retrieve the current site language code
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function getCurrentLanguageCode($section = 'site')
	{
		static $codes = [];

		if (!isset($codes[$section])) {
			$codes[$section] = '';

			$default = JComponentHelper::getParams('com_languages')->get($section, 'en-GB');

			// Get the languagefilter params
			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');

			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$langFilterRemoveLangCodeParams = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');

			$languages = JLanguageHelper::getLanguages('lang_code');
			$language = JFactory::getLanguage();


			// check if the languagefilter plugin enabled
			$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

			// Check the 'Remove URL Language Code' option is disabled or not from the languagefilter plugin
			if ($pluginEnabled && !$langFilterRemoveLangCodeParams) {
				$codes[$section] = $languages[$language->getTag()]->sef;

			}

			if ($pluginEnabled && $langFilterRemoveLangCodeParams && $default != $language->getTag()) {
				$codes[$section] = $languages[$language->getTag()]->sef;
			}
		}

		return $codes[$section];
	}

	/**
	 * Retrieve available Joomla languages
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getLanguages($selected = '', $subname = true)
	{
		static $languages = [];

		$key = $selected . (int) $subname;

		if (isset($languages[$key])) {
			return $languages[$key];
		}

		$languages = JLanguageHelper::createLanguageList($selected , constant('JPATH_SITE'), true, true);
		$results = [];

		// Retrieve the correct metadata for the language
		foreach ($languages as $language) {
			$metaLanguage = \JLanguageHelper::getMetadata($language['value']);

			$obj = new stdClass();
			$obj->text = $metaLanguage['name'];
			$obj->value = $language['value'];

			$results[] = $obj;
		}

		if (!$subname) {
			for ($i = 0; $i < count($results); $i++) {
				$results[$i]->text = preg_replace('#\(.*?\)#i', '', $results[$i]->text);
			}
		}

		$languages[$key] = $results;

		return $languages[$key];
	}

	/**
	 * Generates the hash based on foundry version
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getCacheBustHash()
	{
		static $hash = null;

		if (is_null($hash)) {
			$version = FH::getVersion();

			// Append the hash
			$hash = md5($version) . '=1';
		}

		return $hash;
	}

	/**
	 * Retrieves the current Joomla version
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getJoomlaVersion()
	{
		return FCUtility::getJoomlaVersion();
	}

	/**
	 * Generates the relative path to assets folder
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getMediaPath($type = 'css')
	{
		$path = 'media/foundry/' . $type;

		return $path;
	}

	/**
	 * Retrieves the current Foundry version
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$file = JPATH_ROOT . '/libraries/foundry/foundry.xml';
			$xml = simplexml_load_string(file_get_contents($file));

			$version = (string) $xml->version;
		}

		return $version;
	}

	/**
	 * Retrieves current timezones in Joomla
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getJoomlaTimezones()
	{
		$regions = [
			'Africa' => [],
			'America' => [],
			'Antartica' => [],
			'Arctic' => [],
			'Asia' => [],
			'Atlantic' => [],
			'Australia' => [],
			'Europe' => [],
			'Indian' => [],
			'Pacific' => []
		];

		// Get available time zones.
		$zones = \DateTimeZone::listIdentifiers();

		foreach ($regions as $region => &$items) {

			array_filter($zones, function($zone) use ($region, &$items) {

				if (stristr($zone, $region) !== false) {
					$items[] = $zone;
				}

			});
		}

		return $regions;
	}

	/**
	 * Retrieves a list of languages on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getJoomlaLanguages()
	{
		if (class_exists('Joomla\CMS\Language\LanguageHelper')) {
			$languages = Joomla\CMS\Language\LanguageHelper::getKnownLanguages();

			return $languages;
		}

		$languages = JLanguage::getKnownLanguages();
		return $languages;
	}

	/**
	 * Retrieves the configured site name
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getSiteName()
	{
		static $name = null;

		if (is_null($name)) {
			$config = FH::jconfig();
			$name = $config->get('sitename');
		}

		return $name;
	}

	/**
	 * Retrieve the Joomla editor since J3 and J4 behave differently
	 *
	 * @since   1.1.4
	 * @access  public
	 */
	public static function getEditor($type = null)
	{
		if (!$type) {
			$jconfig = self::jconfig();

			$type = $jconfig->get('editor');
		}

		if (self::isJoomla4()) {
			$editor = Editor::getInstance($type);

			return $editor;
		}

		$editor = JFactory::getEditor($type);

		if ($type === 'none' || $type === 'codemirror') {
			JHtml::_('behavior.core');
		}

		return $editor;
	}

	/**
	 * Retrieves a list of editors from the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getEditors()
	{
		static $editors = null;

		if (is_null($editors)) {
			$db = \JFactory::getDbo();

			$query = [
				'SELECT ' . $db->qn('element') . ' AS ' . $db->qn('value') . ',' . $db->qn('name') . ' AS ' . $db->qn('text'),
				'FROM ' . $db->qn('#__extensions'),
				'WHERE ' . $db->qn('folder') . '=' . $db->Quote('editors'),
				'AND ' . $db->qn('type') . '=' . $db->Quote('plugin'),
				'AND ' . $db->qn('enabled') . '=' . $db->Quote(1),
				'ORDER BY ' . $db->qn('ordering') . ',' . $db->qn('name')
			];

			$query = implode(' ', $query);

			$db->setQuery($query);
			$editors = $db->loadObjectList();
		}

		return $editors;
	}

	/**
	 * Retrieve the folder path of the base email templates
	 *
	 * Deprecated. Use it from Mail Models
	 * 
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getBaseEmailTemplatesFolder($override = false)
	{
		// Backward compatibility
		return Mail::getBaseEmailTemplatesFolder($override);
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function getCurrentTemplate()
	{
		static $template = null;

		if (is_null($template)) {
			$db = \JFactory::getDbo();

			$query = [];
			$query[] = 'SELECT ' . $db->qn('template') . ' FROM ' . $db->qn('#__template_styles');
			$query[] = ' WHERE ' . $db->qn('home') . '!=' . $db->Quote(0);
			$query[] = ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$template = $db->loadResult();
		}

		return $template;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function getTemplateOverrideFolder($extension, $relative = false)
	{
		$folder = JPATH_ROOT . '/';

		if ($relative) {
			$folder = '';
		}

		$folder .= 'templates/stackideas/' . $extension;

		return $folder;
	}


	/**
	 * Retrieves the current site template
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function getTemplateOverrideFolderUri($extension, $relative = false, $stackideasOverride = false)
	{
		$absolutePath = FH::getTemplateOverrideFolder($extension, $relative, $stackideasOverride);
		$uri = str_ireplace(JPATH_ROOT . '/', JURI::root(), $absolutePath);

		return $uri;
	}

	/**
	 * Ride on Joomla's User helper library to obtain two factor methods
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getTwoFactorMethods()
	{
		static $methods = null;

		if (is_null($methods)) {
			JLoader::register('UsersHelper', JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php');

			$methods = UsersHelper::getTwoFactorMethods();
		}

		return $methods;
	}

	/**
	 * Retrieves a list of user groups from the site
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function getUserGroupsTree()
	{
		static $groups = null;

		if (is_null($groups)) {
			$db = \JFactory::getDbo();

			$query = [
				'SELECT a.*, COUNT(DISTINCT(b.`id`)) AS `level` FROM `#__usergroups` AS a',
				'LEFT JOIN `#__usergroups` AS b',
				'ON a.`lft` > b.`lft`',
				'AND a.`rgt` < b.`rgt`',
				'GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`',
				'ORDER BY a.`lft` ASC'
			];

			$query = implode(' ', $query);

			$db->setQuery($query);
			$groups = $db->loadObjectList();
		}

		return $groups;
	}

	/**
	 * Determines if the site has two factor authentication support
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function hasTwoFactor()
	{
		static $hasTwoFactor = null;

		if (is_null($hasTwoFactor)) {
			$hasTwoFactor = false;

			$twoFactor = FH::getTwoFactorMethods();
			$hasTwoFactor = count($twoFactor) > 1;
		}

		return $hasTwoFactor;
	}

	/**
	 * Determines if the site has multi lingual capability
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isMultiLingual()
	{
		static $enabled = null;

		if (is_null($enabled)) {
			$enabled = JPluginHelper::isEnabled('system', 'languagefilter');

			// 3PD: Artio JoomSEF compatibilities
			// The reason why need to check this is because this JoomSEF extension have their own language management
			// In order to use their own language management, the site have to turn off Joomla language filter plugin
			$artio = FH::isArtioLanguageEnabled();

			if ($artio) {
				$enabled = true;
			}
		}

		return $enabled;
	}


	/**
	 * 3PD: Artio JoomSEF
	 *
	 * Determines if Artio JoomSEF is enabled on the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isArtioEnabled()
	{
		static $cache = null;

		if (is_null($cache)) {

			$cache = false;

			// check the file exist or not
			$file = JPATH_ROOT . '/components/com_sef/joomsef.php';
			$exists = file_exists($file);

			if ($exists) {

				require_once($file);

				$sefConfig = SEFConfig::getConfig();
				$isJoomSEFEnabled = JPluginHelper::isEnabled('system', 'joomsef');

				// Check for the component whether have enable the SEF setting
				// And heck if JoomSEF plugin is enabled
				if ($sefConfig->enabled && $isJoomSEFEnabled) {
					$cache = true;
				}
			}
		}

		return $cache;
	}
	
	/**
	 * 3PD: Artio JoomSEF
	 *
	 * Determines if JoomSEF extension is enabled and has multi language enabled
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isArtioLanguageEnabled()
	{
		static $cache = null;

		if (is_null($cache)) {

			$cache = false;

			// Check Artio JoomSEF extension have enable on the site or not
			$enabled = FH::isArtioEnabled();

			if ($enabled) {

				$file = JPATH_ROOT . '/components/com_sef/joomsef.php';
				require_once($file);

				// Check if JoomSEF is enabled
				$sefConfig = SEFConfig::getConfig();

				// Check for the language management
				if ($sefConfig->langEnable) {
					$cache = true;
				}
			}
		}

		return $cache;
	}

	/**
	 * Renders Joomla's configuration object
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function jconfig()
	{
		static $config = null;

		if (is_null($config)) {
			$config = JFactory::getConfig();
		}

		return $config;
	}

	/**
	 * Determines if the user is viewing from the backend
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isFromAdmin()
	{
		if (self::isJoomla4()) {
			$app = JFactory::getApplication();
			$admin = $app->isClient('administrator');

			return $admin;
		}

		$app = JFactory::getApplication();
		$admin = $app->isAdmin();

		return $admin;
	}

	/**
	 * Determines if image magick extension is enabled on the server
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isImagickEnabled()
	{
		static $imagick = null;

		if (is_null($imagick)) {
			$imagick = false;

			if (extension_loaded('imagick')) {
				$imagick = true;
			}
		}

		return $imagick;
	}

	/**
	 * Determines if current view is logged into the site
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isLoggedIn()
	{
		static $loggedIn = null;

		if (is_null($loggedIn)) {
			$my = JFactory::getUser();
			$loggedIn = !$my->guest;
		}

		return $loggedIn;
	}

	/**
	 * Determines if the current instance of Joomla is 3.1 and above
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isJoomla31()
	{
		return FCUtility::isJoomla31();
	}

	/**
	 * Determines if the site is using Joomla 4 currently
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function isJoomla4()
	{
		return FCUtility::isJoomla4();
	}

	/**
	 * Determines if user registration is enabled in Joomla
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isRegistrationEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {
			$enabled = false;
			$params = JComponentHelper::getParams('com_users');

			if ($params->get('allowUserRegistration')) {
				$enabled = true;
			}
		}

		return $enabled;
	}

	/**
	 * Determines if SEF is enabled
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isSefEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {
			$jconfig = FH::jconfig();

			$enabled = $jconfig->get('sef');

			if (FH::isFromAdmin()) {
				$enabled = false;
			}
		}

		return $enabled;
	}

	/**
	 * Determines if the user is a super admin on the site.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isSiteAdmin($id = null)
	{
		static $items = [];

		$user = JFactory::getUser($id);

		if (!isset($items[$user->id])) {
			$items[$user->id] = (bool) $user->authorise('core.admin');
		}

		return $items[$user->id];
	}

	/**
	 * Determine if the site is running on https
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isHttps()
	{
		static $https = null;

		if (is_null($https)) {
			$https = false;
			$uri = JURI::getInstance();
			$protocol = $uri->toString(['scheme']);

			if ($protocol === 'https://') {
				$https = true;
			}
		}

		return $https;
	}

	/**
	 * Loads the language for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function loadLanguage($component, $path = JPATH_ROOT)
	{
		static $loaded = [];

		$index = md5($component . $path);

		if (!isset($loaded[$index])) {
			$language = JFactory::getLanguage();
			$language->load($component, $path);

			$loaded[$index] = true;
		}

		return $loaded[$index];
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function makeArray($item, $delimeter = null)
	{
		// If this is already an array, we don't need to do anything here.
		if (is_array($item)) {
			return $item;
		}

		// Test if source is a JRegistry object
		if ($item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if (is_object($item)) {
			return FCArrayHelper::fromObject($item);
		}

		if (is_integer($item)) {
			return [$item];
		}

		// Test if source is a string.
		if (is_string($item)) {
			if ($item === '') {
				return [];
			}

			// Test for comma separated values.
			if (!is_null($delimeter) && stristr($item , $delimeter) !== false) {
				$data = explode($delimeter , $item);

				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));

			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return json_decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = json_decode($item);

				return FCArrayHelper::fromObject($result);
			}

			return [$item];
		}

		return false;
	}

	/**
	 * Creates a folder on the filesystem based on the path given. If it doesn't exist, create it.
	 *
	 * @since   1.1.2
	 * @access  public
	 */
	public static function makeFolder($path)
	{
		// If folder exists, we don't need to do anything
		if (Folder::exists($path)) {
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		if (Folder::create($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Minifies scripts via compiler server
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function minifyScript($contents)
	{
		$ch = curl_init(FD_COMPILER);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['code' => $contents]));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		$contents = curl_exec($ch);
		curl_close($ch);

		if (false === $contents) {
			throw FH::exception('No HTTP response from compiler server', 500);
		}

		return trim($contents);
	}

	/**
	 * Given contents from css, minify the content
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function minifyCSS($codes)
	{
		$codes = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $codes);
		$codes = str_replace(': ', ':', $codes);
		$codes = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $codes);

		return $codes;
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function normalize($data, $key, $default = null)
	{
		if (!$data) {
			return $default;
		}

		// $key cannot be an array
		if (is_array($key)) {
			$key = $key[0];
		}

		// Object datatype
		if (is_object($data) && isset($data->$key)) {
			return $data->$key;
		}

		// Array datatype
		if (is_array($data) && isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	/**
	 * Normalize directory separator
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function normalizeSeparator($path)
	{
		$path = str_ireplace(['\\' ,'/'] , '/' , $path);

		return $path;
	}

	/**
	 * Rebuilds search database for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function rebuildSearch($data)
	{
		$jsonObject = json_decode($data);

		foreach ($jsonObject->items as &$item) {
			$item->keywords = [];
			$item->keywords = \FH::extractKeyWords($item->label);

			if (isset($item->description)) {
				$item->keywords = array_merge($item->keywords, \FH::extractKeyWords($item->description));
			}

			if ($item->keywords) {
				$item->keywords = implode(' ', $item->keywords);
			}
		}

		$jsonString = json_encode($jsonObject);

		return $jsonString;
	}

	/**
	 * Proxy for Joomla's registry object
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function registry($contents = '', $isFile = false)
	{
		$registry = new JRegistry($contents, $isFile);

		return $registry;
	}

	/**
	 * Renders the responsive library
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function responsive()
	{
		static $lib = null;

		if (is_null($lib)) {
			$lib = new Responsive();
		}

		return $lib;
	}

	/**
	 * Retrieves the token for the user
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function token()
	{
		static $token = null;

		if (is_null($token)) {
			$session = JFactory::getSession();
			$token = $session->getFormToken();
		}

		return $token;
	}

	/**
	 * Converts a given number to a currency format
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function toCurrencyFormat($number)
	{
	  if ($number > 1000) {
		$x = round($number);
		$x_number_format = number_format($x);
		$x_array = explode(',', $x_number_format);
		$x_parts = array('k', 'm', 'b', 't');
		$x_count_parts = count($x_array) - 1;
		$x_display = $x;
		$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		$x_display .= $x_parts[$x_count_parts - 1];

		return $x_display;
	  }

	  return $number;
	}

	/**
	 * Truncate string while maintaining the HTML integrity of the string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function truncateWithHtml($text, $max = 250, $ending = '', $exact = false)
	{
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

		$total_length = StringHelper::strlen($ending);
		$open_tags = [];
		$truncate = '';

		foreach ($lines as $line_matchings) {

			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {

				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {

					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);

					if ($pos !== false) {
						unset($open_tags[$pos]);
					}

				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {

					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, StringHelper::strtolower($tag_matchings[1]));
				}

				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}

			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = StringHelper::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));

			if ($total_length + $content_length > $max) {

				// the number of characters which are left
				$left = $max - $total_length;
				$entities_length = 0;

				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {

					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1] + 1 - $entities_length <= $left) {
							$left--;
							$entities_length += StringHelper::strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}

				$truncate .= StringHelper::substr($line_matchings[2], 0, $left + $entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}

			// if the maximum length is reached, get off the loop
			if ($total_length >= $max) {
				break;
			}
		}

		// If the words shouldn't be cut in the middle...
		if (!$exact) {

			// ...search the last occurance of a space...
			$spacepos = StringHelper::strrpos($truncate, ' ');

			// ...and cut the text in this position
			if (isset($spacepos)) {

				// lets further test if the about truncate string has a html tag or not.
				$remainingString = StringHelper::substr($truncate, $spacepos + 1);
				$remainingString = trim($remainingString);

				// check if string contain any html closing/opening tag before we proceed. #463
				$closingTagV1 = StringHelper::strpos($remainingString, '>');
				$closingTagV2 = StringHelper::strpos($remainingString, '/>');

				// Everything is safe. Let's truncate it.
				if ((!$closingTagV1 && !$closingTagV2) || ($closingTagV1 === 0 && $closingTagV2 === 0)) {
					$truncate = StringHelper::substr($truncate, 0, $spacepos);
				}
			}
		}

		// add the defined ending to the text
		$truncate .= $ending;

		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}

		return $truncate;
	}

	/**
	 * Genereal method to retrieve the table
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function table($name, $prefix, $config = [])
	{
		$table = Table::getInstance($name, $prefix, $config);

		return $table;
	}

	/**
	 * Returns a JURI instance.
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getURI($requestPath = false)
	{
		$uri = JUri::getInstance();

		// Gets the full request path.
		if ($requestPath) {
			$uri = $uri->toString(['path', 'query']);
		}

		return $uri;
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   1.1
	 * @access  public
	*/
	public static function getXml($data, $isFile = true)
	{
		$class = 'SimpleXMLElement';

		if (class_exists('JXMLElement')) {
			$class = 'JXMLElement';
		}

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	}

	/**
	 * Retrieve the IP address
	 *
	 * @since   1.1
	 * @access  public
	*/
	public static function getIp()
	{
		return JFactory::getApplication()->input->server->get('REMOTE_ADDR');
	}

	/**
	 * Determine whether the site enable SEF.
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public static function isSEF()
	{
		static $isSEF = null;

		if (is_null($isSEF)) {
			$jconfig = self::jconfig();
			$isSEF = (int) $jconfig->get('sef') === FD_JROUTER_MODE_SEF;

			if (self::isFromAdmin()) {
				$isSEF = false;
			}
		}

		return $isSEF;
	}

	/**
	 * Determines if the current document is on RTL mode
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function isRTL()
	{
		static $rtl = null;

		if (is_null($rtl)) {
			$lang = JFactory::getLanguage();
			$rtl = $lang->isRTL();
		}

		return $rtl;
	}

	/**
	 * Pluralize a given string
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function pluralize($count, $singularString, $pluralString)
	{
		$count = (int) $count;
		
		if ($count === 1) {
			return $singularString;
		}

		return $pluralString;
	}

	/**
	 * Retrieves the pagination library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function pagination($fd, $total, $limitstart, $limit)
	{
		$pagination = new Pagination($fd, $total, $limitstart, $limit);

		return $pagination;
	}

	/**
	 * Retrieves the GIPHY library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function giphy($fd)
	{
		$giphy = new Giphy($fd);

		return $giphy;
	}

	/**
	 * Retrieves the textavatar library
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function textavatar($options = [])
	{
		static $libraries = [];

		$signature = md5(json_encode($options));

		if (!isset($libraries[$signature])) {
			$libraries[$signature] = new Textavatar($options);
		}

		return $libraries[$signature];
	}

	/**
	 * Renders color picker library from Joomla
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function renderColorPicker()
	{
		if (self::isJoomla4()) {
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'vendor/minicolors/jquery.minicolors.min.js', ['version' => 'auto', 'relative' => true]);
			HTMLHelper::_('stylesheet', 'vendor/minicolors/jquery.minicolors.css', ['version' => 'auto', 'relative' => true]);
			HTMLHelper::_('script', 'system/fields/color-field-adv-init.min.js', ['version' => 'auto', 'relative' => true]);
			return;
		}

		JHTML::_('behavior.colorpicker');
	}

	/**
	 * Abstract method to load jQuery from Joomla
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function renderjQueryFramework()
	{
		if (self::isJoomla4()) {
			HTMLHelper::_('jquery.framework');
			return;
		}

		JHtml::_('jquery.framework');
	}

	/**
	 * Renders modal library from Joomla
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function renderModalLibrary()
	{
		if (self::isJoomla4()) {
			HTMLHelper::_('bootstrap.framework');
			return;
		}

		JHTML::_('behavior.modal');
	}

	/**
	 * Render the qrcode libraries
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function qrcode()
	{
		static $qrcode = null;

		if (is_null($qrcode)) {
			require_once(JPATH_ROOT . '/libraries/foundry/libraries/qrcode/qrlib.php');
			$qrcode = new QRcode();
		}

		return $qrcode;
	}

	/**
	 * Retrieve the default avatar
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function getDefaultAvatar()
	{
		$avatar = rtrim(JURI::root(), '/') . '/media/foundry/images/avatar/default.png';

		return $avatar;
	}

	/**
	 * Converts characters to HTML entities for Schema structure data
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function normalizeSchema($schemaContent)
	{
		// Converts characters to HTML entities
		$schemaContent = htmlentities($schemaContent, ENT_QUOTES);

		// Remove backslash symbol since this will caused invalid JSON data
		$schemaContent = stripslashes($schemaContent);

		return $schemaContent;
	}
}