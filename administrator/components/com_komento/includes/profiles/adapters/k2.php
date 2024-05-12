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

class KomentoProfilesK2 extends KomentoBase
{
	protected $profile = null;
	protected $result = null;

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
		$routeFile = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$utilitiesFile = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if (!JFile::exists($routeFile) || !JFile::exists($utilitiesFile)) {
			return false;
		}

		require_once($routeFile);
		require_once($utilitiesFile);

		return true;
	}

	public function getAvatar()
	{
		$avatar = K2HelperUtilities::getAvatar($this->profile->id);

		return $avatar;
	}

	public function getLink()
	{
		return K2HelperRoute::getUserRoute( $this->profile->id );
	}
}