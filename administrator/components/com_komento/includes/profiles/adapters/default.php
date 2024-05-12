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

class KomentoProfilesDefault extends KomentoBase
{
	protected $profile = null;

	public function __construct($profile)
	{
		parent::__construct();
		$this->profile = $profile;
	}

	public function exists()
	{
		return true;
	}

	public function getAvatar()
	{
		$avatar = FH::getDefaultAvatar();

		if ($this->profile->id && $this->config->get('layout_avatar_character')) {
			$textavatar = KT::textavatar();
			$name = $this->profile->getNameInitial();

			$avatar = $textavatar->getAvatar($name->text);
		}

		return $avatar;
	}

	public function getLink($email = null, $website = '')
	{
		$permalink = 'javascript: void(0);';

		if (!$this->profile->id && $website) {
			$permalink = $website;
		}

		return $permalink;
	}

}
