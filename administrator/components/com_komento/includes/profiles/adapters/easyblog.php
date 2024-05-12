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

class KomentoProfilesEasyblog extends KomentoBase
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
		$filename = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($filename)) {
			return false;
		}

		require_once($filename);

		return true;
	}

	public function getAvatar()
	{
		$profileTable = EB::table('Profile','Table');
		$profileTable->load( $this->profile->id );

		return $profileTable->getAvatar();
	}

	public function getLink()
	{
		$profileTable = EB::table('Profile','Table');
		$profileTable->load($this->profile->id);

		// Check if the author really has privilege to write a blog post because if they dont, then we should not link to the author page
		$acl = EB::acl($this->profile->id);

		if (!$acl->get('add_entry')) {
			return 'javascript:void(0);';
		}

		return $profileTable->getPermalink(false);
	}
}