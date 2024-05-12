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

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Users\Site\Model\RegistrationModel;
use Joomla\Component\Users\Site\Model\ProfileModel;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Finder\Site\Model\SuggestionsModel;
use Joomla\Component\Finder\Site\Model\SearchModel;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\Event;

if (!defined('FOUNDRY_CLI')) {
	if (!FCUtility::isJoomla4()) {
		require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');
	}
}

class FCUtility {
	/**
	 * Retrieves Joomla version
	 *
	 * @since	1.1
	 * @access	public
	 */
	public static function getJoomlaVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$jVerArr = explode('.', JVERSION);
			$version = $jVerArr[0] . '.' . $jVerArr[1];
		}

		return $version;
	}

	/**
	 * Determines if the site is on Joomla 3
	 *
	 * @since	1.1
	 * @access	public
	 */
	public static function isJoomla31()
	{
		static $state = null;

		if (is_null($state)) {
			$state = false;

			if (self::getJoomlaVersion() >= '3.1' && !self::isJoomla4()) {
				$state = true;
			}
		}

		return $state;
	}

	/**
	 * Determines if the site is on Joomla 4
	 *
	 * @since	1.1
	 * @access	public
	 */
	public static function isJoomla4()
	{
		static $isJoomla4 = null;

		if (is_null($isJoomla4)) {
			$currentVersion = self::getJoomlaVersion();
			$isJoomla4 = version_compare($currentVersion, '4.0') !== -1;

			return $isJoomla4;
		}

		return $isJoomla4;
	}
}

if (!FCUtility::isJoomla4()) {
	class FCStringBase extends JString
	{
	}
}

if (FCUtility::isJoomla4()) {
	class FCStringBase extends Joomla\String\StringHelper
	{
	}
}

class FCJString extends FCStringBase
{
}

class FCContentHelperRoute
{
	/**
	 * Get the article route.
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getArticleRoute($id, $catid = 0, $language = 0, $layout = null)
	{
		if (FCUtility::isJoomla4()) {
			return RouteHelper::getArticleRoute($id, $catid, $language, $layout);
		}

		return ContentHelperRoute::getArticleRoute($id, $catid, $language, $layout);
	}

	/**
	 * Get the category route.
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getCategoryRoute($catid, $language = 0, $layout = null)
	{
		if (FCUtility::isJoomla4()) {
			return RouteHelper::getCategoryRoute($catid, $language, $layout);
		}

		return ContentHelperRoute::getCategoryRoute($catid, $language, $layout);
	}

	/**
	 * Get the form route.
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getFormRoute($id)
	{
		if (FCUtility::isJoomla4()) {
			return RouteHelper::getFormRoute($id);
		}

		return ContentHelperRoute::getFormRoute($id);
	}
}

class FCArrayHelper
{
	/**
	 * Utility function to map an object to an array
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function fromObject($data)
	 {
		if (FCUtility::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::fromObject($data);
			return $data;
		}


		$data = JArrayHelper::fromObject($data);
		return $data;
	 }

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getValue($array, $name, $default = null, $type = '')
	{
		if (FCUtility::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::getValue($array, $name, $default, $type);
			return $data;
		}

		$data = JArrayHelper::getValue($array, $name, $default, $type);
		return $data;
	}

	/**
	 * Method to convert array to integer values
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function toInteger($array, $default = null)
	{
		if (FCUtility::isJoomla4()) {
			$data = Joomla\Utilities\ArrayHelper::toInteger($array, $default);

			return $data;
		}

		$data = JArrayHelper::toInteger($array, $default);
		return $data;
	}

	/**
	 * Method to determine if an array is an associative array.
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function isAssociative($array)
	{
		if (FCUtility::isJoomla4()) {
			$isAssociative = Joomla\Utilities\ArrayHelper::isAssociative($array);

			return $isAssociative;
		}

		$isAssociative = JArrayHelper::isAssociative($array);
		return $isAssociative;
	}
}

class FCArchive
{
	/**
	 * Load Joomla's Archive
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function load()
	{
		if (FCUtility::isJoomla4()) {
			$archive = new Joomla\Archive\Archive();

			return $archive;
		}

		$archive = new JArchive();

		return $archive;
	}

	/**
	 * Perform extract method from Joomla Archive
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function extract($destination, $extracted)
	{
		$archive = self::load();

		if (!FCUtility::isJoomla4()) {
			$state = $archive::extract($destination, $extracted);

			return $state;
		}

		$state = $archive->extract($destination, $extracted);

		return $state;
	}

	/**
	 * Get a file compression adapter from Joomla Archive
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function getAdapter($type)
	{
		$archive = self::load();

		if (!FCUtility::isJoomla4()) {
			$adapter = $archive::getAdapter($type);

			return $adapter;
		}

		$adapter = $archive->getAdapter($type);

		return $adapter;
	}
}

class FCApplicationHelper
{
	/**
	 * Load up ApplicationHelper
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function load()
	{
		if (FCUtility::isJoomla4()) {
			$app = new Joomla\CMS\Application\ApplicationHelper;

			return $app;
		}

		$app = new JApplicationHelper();

		return $app;
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @since   1.1.0
	 * @access  public
	 */
	public static function getHash($seed)
	{
		$app = self::load();

		return $app::getHash($seed);
	}
}