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

class KomentoViewBBcode extends KomentoView
{
	/**
	 * Renders the insert video dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function video()
	{
		$element = $this->input->get('element', '', 'cmd');
		$position = $this->input->get('caretPosition', 0, 'int');

		$theme = KT::themes();
		$theme->set('element', $element);
		$theme->set('position', $position);
		$output = $theme->output('site/bbcode/dialogs/video');

		return $this->ajax->resolve($output);
	}
}
