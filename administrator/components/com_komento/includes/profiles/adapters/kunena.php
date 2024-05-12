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

class KomentoProfilesKunena extends KomentoBase
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
		$file = JPATH_ADMINISTRATOR . '/components/com_kunena/libraries/factory.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	public function getAvatar()
	{
		$userKNN = KunenaFactory::getUser($this->profile->id);
		return $userKNN->getAvatarURL('kavatar');
	}

	public function getLink()
	{
		$profileKNN = KunenaFactory::getProfile($this->profile->id);
		return $profileKNN->getProfileURL($this->profile->id, '');
	}
}