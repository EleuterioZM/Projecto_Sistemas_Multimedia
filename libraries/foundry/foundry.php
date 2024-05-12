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

require_once(__DIR__ . '/autoloader.php');
require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/helper.php');

use Foundry\Libraries\ActionLog;
use Foundry\Libraries\Ajax;
use Foundry\Libraries\Info;

/**
 * Unlike the helper, FoundryLibrary needs to be aware of the current extension it is serving.
 */
class FoundryLibrary
{
	private $paths = null;
	private $identifiers = null;
	private $appearance = 'light';

	public function __construct($component, $componentShortName, $jsName, $title = '', $options = [])
	{
		$this->appearance = \FH::normalize($options, 'appearance', 'light');

		$this->identifiers = (object) [
			'component' => $component,
			'short' => $componentShortName,
			'name' => str_ireplace('com_', '', $component),
			'js' => $jsName,
			'title' => $title ? $title : $jsName
		];

		$this->paths = (object) [
			'admin' => JPATH_ADMINISTRATOR . '/components/' . $this->getComponentName(),
			'lib' => JPATH_ADMINISTRATOR . '/components/' . $this->getComponentName() . '/includes',
			'root' => JPATH_ROOT . '/components/' . $this->getComponentName(),
			'media' => JPATH_ROOT . '/media/' . $this->getComponentName(),
			'override' => JPATH_ROOT . '/components/' . $this->getComponentName() . '/themes/wireframe/foundry'
		];


		// Load foundry's language file since this is needed
		\FH::loadLanguage('lib_foundry');
	}

	/**
	 * Renders the ajax library for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function ajax()
	{
		$ajax = $this->getComponentLibrary('Ajax', $this);

		return $ajax;
	}

	/**
	 * Renders the autoload file within the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function autoload()
	{
		$file = $this->paths->lib . '/vendor/autoload.php';

		require_once($file);
	}

	/**
	 * Retrieves the configuration of the component
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function config()
	{
		static $configurations = [];

		if (!isset($configurations[$this->getComponentShortName(true)])) {
			$configurations[$this->getComponentShortName(true)] = $this->getComponentLibrary('Config');
		}

		return $configurations[$this->getComponentShortName(true)];
	}

	/**
	 * Creates an instance of the action log library to assist extensions
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getActionLog()
	{
		static $actionLog = null;

		if (is_null($actionLog)) {
			$actionLog = new ActionLog($this->getComponentName());
		}

		return $actionLog;
	}

	/**
	 * Creates an instance of the component's library
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComponentLibrary($library)
	{
		$args = func_get_args();
		array_shift($args);

		return call_user_func([$this->getComponentShortName(true), $library], $args);
	}

	/**
	 * Determines the current appearance for foundry
	 *
	 * @since	1.1.5
	 * @access	public
	 */
	public function getAppearance()
	{
		return $this->appearance;
	}

	/**
	 * Generates the extension name for viewer
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getExtensionTitle()
	{
		return $this->identifiers->title;
	}

	/**
	 * Retrieves the component's name
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getName()
	{
		return $this->identifiers->name;
	}

	/**
	 * Generates the url for Xhr requests
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getAjaxUrl($useIndex = true)
	{
		static $url;

		if (isset($url)) {
			return $url;
		}

		$uri = \JUri::getInstance();
		$language = $uri->getVar('lang', 'none');

		// Remove any ' or " from the language because language should only have -
		$app = \JFactory::getApplication();
		$input = $app->input;

		$language = $input->get('lang', '', 'cmd');

		$jconfig = \FH::jconfig();

		// Get the router
		$router = $app->getRouter();

		// It could be admin url or front end url
		$url = rtrim(JURI::base(), '/') . '/';

		if ($useIndex) {
			$url .= 'index.php';
		}

		$url = $url . '?option=' . $this->getComponentName() . '&lang=' . $language;

		// During SEF mode, we need to ensure that the URL is correct.
		$languageFilterEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

		if (\FH::isSefEnabled() && !\FH::isFromAdmin() && $languageFilterEnabled) {

			$sefs = JLanguageHelper::getLanguages('sef');
			$lang_codes   = JLanguageHelper::getLanguages('lang_code');

			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$removeLangCode = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');

			// Determines if the mod_rewrite is enabled on Joomla
			$rewrite = $jconfig->get('sef_rewrite');

			if ($removeLangCode) {
				$defaultLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
				$currentLang = $app->input->cookie->getString(JApplicationHelper::getHash('language'), $defaultLang);

				$defaultSefLang = $lang_codes[$defaultLang]->sef;
				$currentSefLang = $lang_codes[$currentLang]->sef;

				$language = '';

				if ($defaultSefLang != $currentSefLang) {
					$language = $currentSefLang;
				}

			} else {
				// Replace the path if it's on subfolders
				$base = str_ireplace(JURI::root(true), '', $uri->getPath());
				$path = $base;

				if (!$rewrite) {
					$path = Joomla\String\StringHelper::substr($base, 10);
				}

				// Remove trailing / from the url
				$path = Joomla\String\StringHelper::trim($path, '/');
				$parts = explode('/', $path);

				$language = 'none';

				if ($parts) {
					$language = reset($parts);
				}
			}

			if ($language) {
				$language .= '/';
			}

			// Default url
			$url = rtrim(JURI::root(), '/') . '/index.php/' . $language . '?option=' . $this->getComponentName();

			if ($rewrite) {
				$url = rtrim(JURI::root(), '/');

				if ($useIndex) {
					$url .= '/index.php';
				}

				$url .= '/' . $language . '?option=' . $this->getComponentName();
			}
		}

		$menu = JFactory::getApplication()->getmenu();

		if (!empty($menu)) {
			$item = $menu->getActive();

			if (isset($item->id)) {
				$url .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix. Need to sort them out here.
		$currentURL = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($currentURL)) {

			// When the url contains www and the current accessed url does not contain www, fix it.
			if (stristr($currentURL, 'www') === false && stristr($url, 'www') !== false) {
				$url = str_ireplace('www.', '', $url);
			}

			// When the url does not contain www and the current accessed url contains www.
			if (stristr($currentURL, 'www') !== false && stristr($url, 'www') === false) {
				$url = str_ireplace('://', '://www.', $url);
			}
		}

		return $url;
	}

	/**
	 * Retrieves the component's name
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComponentName()
	{
		return $this->identifiers->component;
	}

	/**
	 * Retrieves the component's name
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getComponentShortName($uppercase = false)
	{
		if ($uppercase) {
			return strtoupper($this->identifiers->short);
		}

		return $this->identifiers->short;
	}

	/**
	 * Retrieves the component's name
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getShortName()
	{
		return $this->identifiers->short;
	}

	/**
	 * Retrieves the documentation link for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDocumentationLink()
	{
		$link = 'https://stackideas.com/docs/' . $this->getName();

		return $link;
	}

	/**
	 * Retrieves the override path for foundry theme files
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOverridePath()
	{
		$path = $this->paths->override;

		return $path;
	}

	/**
	 * Standardized method to load global html tempaltes
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function html($namespace)
	{
		static $cache = [];

		$parts = explode('.', $namespace);
		$method = array_pop($parts);

		require_once(__DIR__ . '/html/base.php');

		$index = $this->getComponentName() . '/' . $namespace;

		if (!isset($cache[$index])) {

			$className = implode('', $parts);
			$className = 'Foundry\\Html\\' . ucfirst($className);

			$cache[$index] = new $className($this);
		}

		// Separate the function arguments for php 7.4 compatibility.
		$args = func_get_args();

		// Arguments to send to the method
		$args = array_splice($args, 1);

		if (!method_exists($cache[$index], $method)) {
			return false;
		}

		return call_user_func_array([$cache[$index], $method], $args);
	}

	/**
	 * Generates the info object that is used by extensions
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function info()
	{
		static $instances = [];

		$id = $this->getComponentName();

		if (!isset($instances[$id])) {
			$instances[$id] = new Info($this);
		}
		
		return $instances[$id];
	}

	/**
	 * Retrieves the component's javascript library identifier
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function js()
	{
		return $this->identifiers->js;
	}

	/**
	 * Loads the language for the extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function loadLanguage($path = JPATH_ROOT)
	{
		return FH::loadLanguage($this->getComponentName(), $path);
	}

	/**
	 * Generates the language prefix for an extension
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function lang()
	{
		$prefix = 'COM_' . $this->getComponentShortName(true);

		return $prefix;
	}
}
