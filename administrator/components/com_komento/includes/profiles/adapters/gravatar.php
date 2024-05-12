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

class KomentoProfilesGravatar extends KomentoBase
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

	public function getAvatar($email = '')
	{
		if (empty($email)) {
			$email = $this->profile->email;
		}

		// PHP 8.1 compatibility
		if (is_null($email)) {
			$email = '';
		}

		$image = '';
		$config = KT::config();
		$emailKey = md5(strtolower(trim($email)));
		$image = 'http://www.gravatar.com';

		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$image = 'https://secure.gravatar.com';
		}

		$image .= '/avatar/' . $emailKey . '?s=100&amp;d=' . $config->get('gravatar_default_avatar', 'mm');

		return $image;
	}

	public function getLink($email = null, $website = '')
	{
		if (empty($email)) {
			$email = $this->profile->email;
		}

		$link = '';

		if (!$this->profile->id && $website) {
			return $website;
		}

		if ($website) {
			return $website;
		}

		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$link = 'https://secure.gravatar.com/' . md5($email);
		} else {
			$link = 'http://www.gravatar.com/' . md5($email);
		}

		return $link;
	}
}