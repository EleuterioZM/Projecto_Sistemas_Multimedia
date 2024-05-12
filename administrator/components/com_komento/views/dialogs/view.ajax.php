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

class KomentoViewDialogs extends KomentoAdminView
{
	/**
	 * Responsible to generate a generic dialog form given a namespace to the template
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function render()
	{
		$namespace = $this->input->get('file', '', 'default');

		$theme = KT::themes();
		$output = $theme->output($namespace);

		return $this->ajax->resolve($output);
	}
}
