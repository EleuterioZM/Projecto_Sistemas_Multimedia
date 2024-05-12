<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KTLegacy
{
	/**
	 * Deprecated. Use FH::checkToken
	 *
	 * @deprecated	4.0.0
	 */
	public static function checkToken()
	{
		return FH::checkToken();
	}
	
	/**
	 * Deprecated. Use FH::jconfig();
	 *
	 * @deprecated	4.0.0
	 */
	public static function jconfig()
	{
		return FH::jconfig();
	}

	/**
	 * Deprecated. Use FH::loadLanguage
	 *
	 * @deprecated	4.0.0
	 */
	public static function loadLanguage($path = JPATH_ROOT)
	{
		return FH::loadLanguage('com_komento', $path);
	}

	/**
	 * Deprecated. We are no longer using this in Komento
	 *
	 * @deprecated	4.0.0
	 */
	public static function factory($class, $args = [])
	{
		// Reset the indexes
		$args = array_values($args);
		$numArgs = count($args);

		// It's too bad that we have to write these cods but it's much faster compared to call_user_func_array
		if ($numArgs < 1) {
			return new $class();
		}

		if ($numArgs === 1) {
			return new $class($args[0]);
		}

		if ($numArgs === 2) {
			return new $class($args[0], $args[1]);
		}

		if($numArgs === 3 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] );
		}

		if($numArgs === 4 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] );
		}

		if($numArgs === 5 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] );
		}

		if($numArgs === 6 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] );
		}

		if($numArgs === 7 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] , $args[ 6 ] );
		}

		if($numArgs === 8 )
		{
			return new $class($args[0], $args[1] , $args[ 2 ] , $args[ 3 ] , $args[ 4 ] , $args[ 5 ] , $args[ 6 ] , $args[ 7 ]);
		}

		return call_user_func_array($fn, $args);
	}

	/**
	 * Use KT::table instead
	 *
	 * @deprecated	3.0.0
	 **/
	public static function getTable($tableName, $prefix = 'KomentoTable')
	{
		return KT::table($tableName, $prefix);
	}

	/**
	 * Deprecated. This is no longer being used in Komento
	 *
	 * @deprecated	4.0.0
	 **/
	public static function get($lib = '')
	{
		// Try to load up the library
		KT::load($lib);

		$class = 'Komento' . ucfirst($lib);

		$args = func_get_args();

		// Remove the first argument because we know the first argument is always the library.
		if (isset($args[0])) {
			unset($args[0]);
		}

		return KT::factory($class, $args);
	}

	/**
	 * Deprecated as it is not being used
	 *
	 * @deprecated	4.0.0
	 */
	public static function getACL()
	{
		$my = JFactory::getUser();

		KT::import('admin:/includes/acl/acl');

		$acl = KT::ACL()->getRules($my->id, KT::$component);
		$acl = FCArrayHelper::toObject($acl);

		return $acl;
	}

	/**
	 * Deprecated as it is no longer in use
	 *
	 * @deprecated	3.0.0
	 **/
	public static function getClass($filename, $classname)
	{
		// We have already deprecated this in 3.0.x
		return false;
	}

	/**
	 * Deprecated as it is no longer in use
	 *
	 * @deprecated	4.0.0
	 **/
	public static function getHelper($name)
	{
		static $helpers	= [];

		if (empty($helpers[$name])) {
			$file = KT_LIB . '/' . FCJString::strtolower($name) .'/'. FCJString::strtolower($name) . '.php';

			$helpers[$name] = false;

			if (JFile::exists($file)) {
				require_once($file);
				
				$classname = 'Komento' . ucfirst( $name ) . 'Helper';

				$helpers[$name] = class_exists($classname) ? new $classname() : false;
			}
		}

		return $helpers[$name];
	}

	/**
	 * Deprecated. Use KT::model
	 *
	 * @deprecated	4.0.0
	 */
	public static function getModel($name, $backend = false)
	{
		return KT::model($name);
	}

	/**
	 * Deprecated. Use KT::config()
	 *
	 * @deprecated	3.0
	 **/
	public static function getConfig($component = '', $default = true)
	{
		return KT::config($component, $default);
	}

	/**
	 * Deprecated. Use KT::db()
	 *
	 * @deprecated	4.0.0
	 **/
	public static function getDBO()
	{
		return KT::db();
	}

	/**
	 * Deprecated. Use KT::getKonfig()
	 *
	 * @deprecated	3.0
	 **/
	public static function getKonfig($component = '', $default = true)
	{
		return KT::config($component, $default);
	}

	/**
	 * Use FH::makeArray
	 *
	 * @deprecated	4.0.0
	 */
	public static function makeArray($item, $delimeter = null)
	{
		return FH::makeArray($item, $delimeter);
	}

	/**
	 * Use KT::user($id) instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function getProfile($id = null)
	{
		return KT::user($id);
	}

	/**
	 * Deprecated. Use FH::normalize
	 *
	 * @deprecated	4.0.0
	 */
	public static function normalize($data, $key, $default)
	{
		return FH::normalize($data, $key, $default);
	}

	/**
	 * Deprecated. Use FH::normalizeSeparator
	 *
	 * @deprecated	4.0.0
	 */
	public static function normalizeSeparator($path)
	{
		return FH::normalizeSeparator($path);
	}

	/**
	 * Deprecated. Use JAccess::getUsersByGroup
	 *
	 * @deprecated	4.0.0
	 */
	public static function getUsersByGroup($gid)
	{
		return JAccess::getUsersByGroup($gid);
	}

	/**
	 * Deprecated. Use KT::template
	 *
	 * @deprecated	4.0.0
	 */
	public static function getTemplate($theme = false, $options = array())
	{
		return KT::themes();
	}

	/**
	 * Deprecated. Use JRegistry instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function getRegistry($contents = '', $isFile = false)
	{
		$registry = new JRegistry($contents);

		return $registry;
	}

	/**
	 * Determines if the user is a super admin on the site.
	 *
	 * @deprecated	4.0.0
	 */
	public static function isSiteAdmin($id = null)
	{
		return FH::isSiteAdmin($id);
	}

	/**
	 * Deprecated. Use KT::getLocalVersion()
	 *
	 * @deprecated	4.0.0
	 */
	public static function komentoVersion()
	{
		return KT::getLocalVersion();
	}

	/**
	 * Deprecated. Use FH::getJoomlaVersion()
	 *
	 * @deprecated	4.0.0
	 */
	public static function joomlaVersion()
	{
		return FH::getJoomlaVersion();
	}
	
	/**
	 * Use FH::makeFolder
	 *
	 * @deprecated  4.0.0
	 */
	public static function makeFolder($path)
	{
		return FH::makeFolder($path);
	}

	/**
	 * Deprecated. Use JRegistry instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function registry($contents = '', $isFile = false)
	{
		$registry = new JRegistry($contents);

		return $registry;
	}

	/**
	 * Deprecated. Use KT::form()->requireCaptcha() instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function requireCaptcha()
	{
		return KT::form()->requireCaptcha();
	}

	/**
	 * Deprecated. Use KT::form()->requireTerms() instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function requireTerms()
	{
		return KT::form()->requireTerms();
	}

	/**
	 * Deprecated. Use KT::form()->requireField() instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function requireFormField($component, $name)
	{
		return KT::form()->requireField($name);
	}

	/**
	 * Deprecated. Use KT::form()->showField() instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function showField($component, $name)
	{
		return KT::form()->showField($name);
	}

	/**
	 * Deprecated. Use KT::setMessage instead
	 *
	 * @deprecated	4.0.0
	 */
	public static function setError($message)
	{
		return KT::setMessage($message, 'error');
	}

	/**
	 * Deprecated. Use KT::themes()
	 *
	 * @deprecated	4.0.0
	 */
	public static function template($theme = false, $options = array())
	{
		return KT::themes();
	}

	/**
	 * Deprecated. Use KT::token()
	 *
	 * @deprecated	4.0.0
	 */
	public static function token()
	{
		return FH::token();
	}
}