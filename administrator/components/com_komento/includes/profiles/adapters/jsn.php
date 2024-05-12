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

class KomentoProfilesJsn extends KomentoBase
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
		$enabled = JComponentHelper::isEnabled('com_jsn');
		$filename = JPATH_ROOT . '/components/com_jsn/helpers/helper.php';

		if (!$enabled || !JFile::exists($filename)) {
			return false;
		}

		require_once($filename);

		return true;
	}

	public function getAvatar()
	{
		$user = JsnHelper::getUser($this->profile->id);
		$avatar = rtrim(JURI::root(), '/') . '/' . $user->getValue('avatar_mini');

		return $avatar;
	}

	public function getLink()
	{
		$permalink = 'javascript: void(0);';
		return $permalink;
	}
}