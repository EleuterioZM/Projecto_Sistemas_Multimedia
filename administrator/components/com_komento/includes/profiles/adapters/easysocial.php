<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoProfilesEasysocial extends KomentoBase
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
		$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	public function getAvatar()
	{
		if (!$this->exists()) {
			return;
		}

		if (KT::getConfig()->get('easysocial_profile_popbox')) {
			ES::initialize();
		}

		$user = ES::user($this->profile->id);

		return $user->getAvatar();
	}

	public function getLink()
	{
		if (!$this->exists()) {
			return;
		}

		$user = ES::user($this->profile->id);
		return htmlspecialchars_decode($user->getPermalink());
	}
}