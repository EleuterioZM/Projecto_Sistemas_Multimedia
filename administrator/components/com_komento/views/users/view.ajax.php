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

KT::import('admin:/views/views');

class KomentoViewUsers extends KomentoAdminView
{
	/**
	 * Displays a list of users on the site in a dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function browse()
	{
		$theme = KT::themes();

		$output	= $theme->output('admin/users/dialogs/browse');
		
		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation archive user download data from backend
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function confirmArchiveUserData()
	{
		$theme = KT::themes();
		$contents = $theme->output('admin/users/dialogs/archive.confirm');

		return $this->ajax->resolve($contents);
	}
}