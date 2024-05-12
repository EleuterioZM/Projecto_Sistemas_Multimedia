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
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\Responsive;

class Scripts
{
	static $required = false;

	static $coreFiles = [
		// The ordering of items #1 and #2 must not be changed
		'jq', // The name of this file needs to be like this to prevent conflicts by T4 which tries to remove the file "jquery.js" from the head
		'bootloader',
		'module',
		'script',
		'require',
		'dialog',
		'foundry',
		'component'
	];

	static $files = [
		// Core libraries are libraries that needs to be rendered first
		'core' => [
		],

		'base' => [
		],

		'vendor' => [
		],

		'others' => [
		]
	];

	static $inline = [];

	// Internal css libraries for foundry
	static $libraries = [
		'admin' => [
			'path' => '/admin',
			'type' => 'base'
		],
		'shared' => [
			'path' => '/shared',
			'type' => 'base'
		],
		'flatpickr' => [
			'path' => '/vendor/flatpickr',
			'type' => 'vendor'
		],
		'popper' => [
			'path' => '/vendor/popper',
			'type' => 'vendor'
		],
		'tippy' => [
			'path' => '/vendor/tippy',
			'type' => 'vendor'
		],
		'perfectscrollbar' => [
			'path' => '/vendor/perfectscrollbar',
			'type' => 'vendor'
		],
		'emoji' => [
			'path' => '/vendor/emoji',
			'type' => 'vendor'
		],
		'raty' => [
			'path' => '/vendor/raty',
			'type' => 'vendor'
		],
		'mmenu' => [
			'path' => '/vendor/mmenu',
			'type' => 'vendor'
		],
		'select2' => [
			'path' => '/vendor/select2',
			'type' => 'vendor'
		],
		'tribute' => [
			'path' => '/vendor/tribute',
			'type' => 'vendor'
		],
		'toast' => [
			'path' => '/vendor/toast',
			'type' => 'vendor'
		],
		'prism' => [
			'path' => '/vendor/prism',
			'type' => 'vendor'
		],
		'lightbox' => [
			'path' => '/vendor/lightbox',
			'type' => 'vendor'
		],
		'markitup' => [
			'path' => '/vendor/markitup',
			'type' => 'vendor'
		]
	];

	/**
	 * Switch to determine if scripts should be rendered on the page
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function required()
	{
		self::$required = true;
	}

	/**
	 * Allows caller to insert new stylesheets into our resource collector
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function add($file, $type)
	{
		if (!in_array($type, array_keys(self::$files))) {
			return false;
		}

		self::required();
		self::$files[$type][] = ltrim($file, '/');
	}


	/**
	 * Insert all core libraries needed
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function addCoreFiles()
	{
		static $added = false;

		if (!$added) {

			if (FD_MODE === 'development') {
				foreach (self::$coreFiles as $file) {
					$path = \FH::getMediaPath('scripts') . '/core/' . $file . '.js';

					// Append the hash
					$filePath = $path . '?' . \FH::getCacheBustHash();

					self::add($filePath, 'core');
				}
			}
				
			// Since all core files will be minified into a single js file
			if (FD_MODE === 'production') {
				// Set path to the single core.js file
				$path = \FH::getMediaPath('scripts') . '/core.min.js';

				$filePath = $path . '?' . \FH::getCacheBustHash();

				self::add($filePath, 'core');
			}

			// Shared is now part of the core script
			// if (!defined('SKIP_FD_SCRIPT_SHARED')) {
			// 	$lib = self::getLibrary('shared');

			// 	if ($lib) {
			// 		$path = \FH::getMediaPath('scripts') . $lib->path;

			// 		$path = self::appendExtension($path);

			// 		self::add($path, $lib->type);
			// 	}
			// }

			// If admin, load the admin script
			if (\FH::isFromAdmin()) {

				$lib = self::getLibrary('admin');

				if ($lib) {
					$path = \FH::getMediaPath('scripts') . $lib->path;

					$path = self::appendExtension($path);

					self::add($path, $lib->type);
				}
			}

			$added = true;
		}

	}

	/**
	 * Helper method to generate extension and necessary cache bust
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public static function appendExtension($filePath, $cacheBusting = true)
	{
		// Append the file name
		$filePath = self::getFileExtension($filePath);

		// Append the hash
		$filePath = $filePath . '?' . \FH::getCacheBustHash();

		return $filePath;
	}

	/**
	 * Retrieve files that are in the resource collector
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getFiles()
	{
		return self::$files;
	}

	/**
	 * Retrieves inline scripts that needs to be attached on the page
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getInlineScripts()
	{
		return self::$inline;
	}

	/**
	 * Determines if the file should use a .min.css or not
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getFileExtension($fileName)
	{
		$name = $fileName . (FD_MODE === 'production' ? '.min' : '') . '.js';

		return $name;
	}

	/**
	 * Retrieves the library for a script
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getLibrary($library)
	{
		$data = \FH::normalize(self::$libraries, $library, null);

		if (!$data) {
			return false;
		}

		$data = (object) $data;

		return $data;
	}

	/**
	 * Allows caller to insert inline scripts into our resource collector
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function inline($codes)
	{
		self::required();

		self::$inline[] = $codes;
	}

	/**
	 * Determines if stylesheet needs to be loaded on the page
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function isRequired()
	{
		return (bool) self::$required;
	}

	/**
	 * Loads a known script library from Foundry and insert it into the resource collector
	 *
	 * @since	1.1.0
	 * @access	public
	 * 
	 * @deprecated since 1.1.3. Please use FD.require().script() instead in order to load the script.
	 */
	public static function load($library)
	{
		static $loaded = [];

		if (!isset($loaded[$library])) {
			$loaded[$library] = true;

			// $lib = self::getLibrary($library);

			// if ($lib) {
			// 	$path = \FH::getMediaPath('scripts') . $lib->path;

			// 	$path = self::appendExtension($path);

			// 	self::add($path, $lib->type);
			// }
		}

		return $loaded[$library];
	}

	/**
	 * Injects classes on the body of the page to allow foundry to style accordingly
	 *
	 * @since	1.1.1
	 * @access	public
	 */
	public static function initializeAdmin()
	{
		static $loaded = null;

		if (is_null($loaded)) {

			// We need Joomla's jquery framework
			\JHtml::_('jquery.framework');

			$version = \FH::isJoomla4() ? '4' : '3';

			ob_start();
?>
<script>
jQuery(document).ready(function($) {
	$('body').addClass('com_foundry si-theme-foundry is-joomla-<?php echo $version;?>');
});
</script>
<?php
			$contents = ob_get_contents();
			ob_end_clean();

			\JFactory::getDocument()->addCustomTag($contents);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Allow the caller to initialize the script
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function init()
	{
		static $initialized = null;

		if (defined('SKIP_FD_SCRIPT')) {
			return true;
		}

		if (is_null($initialized)) {
			\FH::renderjQueryFramework();
			\JHtml::_('behavior.core');

			$doc = \JFactory::getDocument();

			$responsive = new Responsive;

			$doc->addScriptOptions('fd.options', [
				'rootUrl' => rtrim(\JURI::root(), '/'),
				'environment' => FD_MODE,
				'version' => \FH::getVersion(),
				'token' => \FH::token(),
				'scriptVersion' => \FH::getCacheBustHash(),
				'scriptVersioning' => true,
				'isMobile' => $responsive->isMobile()
			]);

			// If there are scripts to render, the core files needs to be rendered too
			self::addCoreFiles();

			$files = self::getFiles();

			$baseUrl = \JURI::root(true);

			foreach ($files as $type => $scriptFiles) {
				if ($scriptFiles) {
					foreach ($scriptFiles as $file) {

						// Do not append the base url if the file contains http:// or https:// or starts with //
						if (strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '//') !== 0) {
							$file = $baseUrl . '/' . $file;
						}

						$doc->addScript($file);
					}
				}
			}

			$initialized = true;
		}
	}
}
