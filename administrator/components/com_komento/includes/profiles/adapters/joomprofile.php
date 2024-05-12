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

class KomentoProfilesJoomprofile extends KomentoBase
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
		$filename = JPATH_ROOT . '/components/com_joomprofile/includes/api.php';

		if (!JFile::exists($filename)) {
			return false;
		}

		require_once($filename);

		return true;
	}

	public function getAvatar()
	{
		$user = JoomProfileApi::getUser($this->profile->id);
		$url = $user->getAvatar(0);

		return $url;
	}

	public function getLink()
	{
		$permalink = 'javascript: void(0);';
		return $permalink;
	}
}