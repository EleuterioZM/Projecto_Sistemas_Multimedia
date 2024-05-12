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

class KomentoViewDownloads extends KomentoAdminView
{

	/**
	 * Confirmation before purging everything
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function confirmPurgeAll()
	{
		$theme 	= KT::themes();
		$contents = $theme->output('admin/downloads/dialogs/purge.all');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation before remove request
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function removeRequest()
	{
		$theme 	= KT::themes();
		$contents = $theme->output('admin/downloads/dialogs/remove.request');

		return $this->ajax->resolve($contents);
	}
}