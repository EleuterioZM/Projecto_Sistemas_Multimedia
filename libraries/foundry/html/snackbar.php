<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;

class Snackbar extends Base
{
	/**
	 * Renders a standard snackbar
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function standard($text, $actions = [], $options = [])
	{
		$attributes = \FH::normalize($options, 'attributes', '');
		$textClass = \FH::normalize($options, 'textClass', 'text-gray-800');
		$wrapperClass = \FH::normalize($options, 'wrapperClass', '');

		if (is_callable($text)) {
			$text = $text();
		}

		if (is_callable($actions)) {
			$actions = $actions();
		}
		
		$theme = $this->getTemplate();
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('actions', $actions);
		$theme->set('text', $text);
		$theme->set('textClass', $textClass);

		return $theme->output('html/snackbar/standard');
	}
}
