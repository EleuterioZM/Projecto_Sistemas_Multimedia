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

class StyleSheets
{
	static $required = false;
	static $initialized = false;

	static $files = [
		'token' => [
		],
		'vendor' => [
		],
		'component' => [
		],
		'module' => [
		],
		'utilities' => [
		],
		'override' => [
		]
	];

	// Internal css libraries for foundry
	static $libraries = [
		'fontawesome' => [
			'path' => '/vendor/fontawesome/css/all',
			'type' => 'vendor'
		],

		'flatpickr' => [
			'path' => '/vendor/flatpickr/flatpickr',
			'type' => 'vendor'
		],

		'mmenu' => [
			'path' => '/vendor/mmenu/mmenu',
			'type' => 'vendor'
		],

		'select2' => [
			'path' => '/vendor/select2/select2',
			'type' => 'vendor'
		],

		'prism' => [
			'path' => '/vendor/prism/prism',
			'type' => 'vendor'
		],
		
		'lightbox' => [
			'path' => '/vendor/lightbox/lightbox',
			'type' => 'vendor'
		],

		'markitup' => [
			'path' => '/vendor/markitup/markitup',
			'type' => 'vendor'
		],

		'daterangepicker' => [
			'path' => '/vendor/daterangepicker/daterangepicker',
			'type' => 'vendor'
		]
	];

	/**
	 * Allows caller to insert new stylesheets into our resource collector
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function add($file, $type)
	{
		if (!in_array($type, array_keys(self::$files))) {
			return false;
		}

		// If the core files are not added yet, we need to add the initial core files
		if (!self::$initialized) {
			self::initialize();
		}

		// Since a css file is added, we assume that css is required
		self::required();

		self::$files[$type][] = ltrim($file, '/');
	}

	/**
	 * Loads a known stylesheet library from Foundry and insert it into the resource collector
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function load($library)
	{
		static $loaded = [];

		if (!isset($loaded[$library])) {
			$loaded[$library] = true;

			$lib = self::getLibrary($library);

			if ($lib) {
				$path = \FH::getMediaPath('css') . $lib->path;

				$version = \FH::getVersion();

				// Append the file name
				$path = self::getFileExtension($path);

				// Append the hash
				$path = $path . '?' . \FH::getCacheBustHash();

				self::add($path, $lib->type);
			}
		}

		return $loaded[$library];
	}

	/**
	 * Generates the path to the css assets file
	 *
	 * @since	1.0
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
	 * Retrieve files that are in the resource collector
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getFiles()
	{
		return self::$files;
	}

	/**
	 * Determines if the file should use a .min.css or not
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getFileExtension($fileName)
	{
		$name = $fileName . (FD_MODE === 'production' ? '.min' : '') . '.css';

		return $name;
	}

	/**
	 * Initializes the base css files that are required in foundry
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function initialize()
	{
		$direction = \JFactory::getDocument()->getDirection();
		$rtl = $direction === 'rtl' ? '-rtl' : '';

		// Base foundry css
		self::$files['token'][] = self::getFileExtension('media/foundry/css/foundry' . $rtl) . '?' . \FH::getCacheBustHash();

		// Utilities css
		self::$files['utilities'][] = self::getFileExtension('media/foundry/css/utilities' . $rtl) . '?' . \FH::getCacheBustHash();
	}

	/**
	 * Determines if stylesheet needs to be loaded on the page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function isRequired()
	{
		return (bool) self::$required;
	}

	/**
	 * Switch to determine if stylesheets should be rendered on the page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function required()
	{
		self::$required = true;
	}
}
