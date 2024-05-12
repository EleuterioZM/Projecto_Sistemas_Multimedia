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

class KomentoViewThemes extends KomentoAdminView
{
	/**
	 * Renders a confirmation dialog to revert changes
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmRevert()
	{
		$id = $this->input->get('id', '', 'default');
		$element = $this->input->get('element', '', 'cmd');

		$theme = KT::themes();
		$theme->set('id', $id);
		$theme->set('element', $element);
		
		$contents = $theme->output('admin/themes/dialogs/revert');

		return $this->ajax->resolve($contents);
	}
}