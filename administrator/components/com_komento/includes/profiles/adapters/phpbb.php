<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoProfilesPhpbb extends KomentoBase
{
	protected $profile = null;
	protected $avatar = null;
	protected $link = null;

	public function __construct($profile)
	{
		$config 		= KT::getConfig();
		$phpbbpath		= $config->get( 'layout_phpbb_path' );
		$phpbburl		= $config->get( 'layout_phpbb_url' );
		$phpbburl		= FCJString::rtrim( $phpbburl, '/', '');

		$phpbbDB		= $this->_getPhpbbDBO( $phpbbpath );
		$phpbbConfig	= $this->_getPhpbbConfig();
		$phpbbuserid	= 0;

		FH::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		if (empty($phpbbConfig)) {
			return false;
		}

		$juser	= JFactory::getUser( $profile->id );

		$sql	= 'SELECT `user_id`, `username`, `user_avatar`, `user_avatar_type` '
				. 'FROM `#__users` WHERE LOWER(`username`) = '.$phpbbDB->quote(strtolower($juser->username)).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if (!empty($result->user_avatar)) {

			switch($result->user_avatar_type) {
				case 'avatar.driver.upload':
					$subpath = $phpbbConfig->avatar_upload_path;
					$phpEx = JFile::getExt(__FILE__);
					$source = $phpbburl.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case 'avatar.driver.remote':
					$source = $result->user_avatar;
					break;
				case 'avatar.driver.local':
					$subpath = $phpbbConfig->avatar_gallery_path;
					$source = $phpbburl.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath = '';
					$source = '';
			}
		} else {
			$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('style_name').' '
					. 'FROM '.$phpbbDB->{$nameQuote}('#__styles').' '
					. 'WHERE '.$phpbbDB->{$nameQuote}('style_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath = 'styles/'.$theme->style_name.'/theme/images/no_avatar.gif';
			$source = $phpbburl.'/'.$defaultPath;
		}

		$this->avatar = $source;

		$this->link	= $phpbburl.'/memberlist.php?mode=viewprofile&u='.$phpbbuserid;

		$this->profile = $profile;
	}

	public function exists()
	{
		$phpbbConfig = $this->_getPhpbbConfig();

		if (empty($phpbbConfig)) {
			return false;
		}

		return true;
	}

	private static function _getPhpbbDBO($phpbbpath = null)
	{
		static $phpbbDB = null;

		if ($phpbbDB == null) {
			$files = JPATH_ROOT . '/' . $phpbbpath . '/config.php';

			if (!JFile::exists($files)) {
				$files	= $phpbbpath . '/config.php';
				if (!JFile::exists($files)) {
					return false;
				}
			}

			require($files);

			// Split the path to get the exact driver
			$dbmsArray = explode("\\",$dbms);

			// Alwasy get the last array
			$driver = end($dbmsArray);

			$options = array('driver' => $driver, 'host' => $dbhost, 'user' => $dbuser, 'password' => $dbpasswd, 'database' => $dbname, 'prefix' => $table_prefix);

			$phpbbDB = JDatabase::getInstance($options);
		}

		return $phpbbDB;
	}

	private function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();

		if (!$phpbbDB) {
			return false;
		}

		$sql	= 'SELECT `config_name`, `config_value` '
				. 'FROM `#__config` '
				. 'WHERE `config_name` IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();

		if (empty($result)) {
			return false;
		}

		$phpbbConfig = new stdClass();
		$phpbbConfig->avatar_gallery_path = null;
		$phpbbConfig->avatar_upload_path = null;
		$phpbbConfig->default_style = 1;

		foreach($result as $row) {

			switch($row->config_name) {
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}

		return $phpbbConfig;
	}

	public function getAvatar()
	{
		return $this->avatar;
	}

	public function getLink()
	{
		return $this->link;
	}
}
