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

class KomentoProfilesHwdmediashare extends KomentoBase
{
	protected $profile = null;

	public function __construct($profile)
	{
		parent::__construct();

		if (!$this->exists()) {
			return;
		}

		$this->profile = $profile;
	}

	public function exists()
	{
		$filename = JPATH_ROOT . '/components/com_hwdmediashare/libraries/factory.php';
		$filename2 = JPATH_ROOT . '/components/com_hwdmediashare/helpers/route.php';

		if (!JFile::exists($filename) || !JFile::exists($filename2)) {
			return false;
		}

		require_once($filename);

		return true;
	}

	public function getAvatar()
	{
		$sql = KT::sql();

		$sql->select( '#__hwdms_users' )
			->where( 'id', $this->profile->id );

		$result = $sql->loadObject();

		if (isset($result->key)) {
			hwdMediaShareFactory::load('files');
			hwdMediaShareFiles::getLocalStoragePath();

			$folders = hwdMediaShareFiles::getFolders($result->key);
			$filename = hwdMediaShareFiles::getFilename($result->key, 10);
			$ext = hwdMediaShareFiles::getExtension($result, 10);

			$path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

			if (file_exists($path)) {
				return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
			}
		}

		return JURI::root(true) . '/media/com_hwdmediashare/assets/images/default-avatar.png';
	}

	public function getLink()
	{
		return JRoute::_(hwdMediaShareHelperRoute::getUserRoute($this->profile->id));
	}
}