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

class KomentoViewKomento extends KomentoAdminView
{
	/**
	 * Displays a list of users on the site in a dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function upgradeToPro()
	{
		$theme = KT::themes();

		$output	= $theme->output('admin/komento/dialogs/upgrade');
		
		return $this->ajax->resolve($output);
	}
}