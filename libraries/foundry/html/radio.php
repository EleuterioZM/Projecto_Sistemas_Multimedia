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

class Radio extends Base
{
	/**
	 * Renders a simple radio form
	 *
	 * @since	1.1.10
	 * @access	public
	 */
	public function standard($name, $checked = false, $value = 1, $id = null, $label = '', $options = [])
	{
		$inline = \FH::normalize($options, 'inline', false);
		$class = \FH::normalize($options, 'class', '');

		if (!$id) {
			$id = $name;
		}

		$theme = $this->getTemplate();
		$theme->set('inline', $inline);
		$theme->set('class', $class);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('checked', $checked);
		$theme->set('label', $label);

		$output = $theme->output('html/radio/standard');

		return $output;
	}

	/**
	 * Renders a radio form with image together
	 *
	 * @since	1.1.10
	 * @access	public
	 */
	public function image($url, $name, $checked = false, $value = 1, $id = null, $label = '', $options = [])
	{
		$inline = \FH::normalize($options, 'inline', false);
		$class = \FH::normalize($options, 'class', '');

		if (!$id) {
			$id = $name;
		}

		$theme = $this->getTemplate();
		$theme->set('inline', $inline);
		$theme->set('class', $class);
		$theme->set('url', $url);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('checked', $checked);
		$theme->set('label', $label);
		$output = $theme->output('html/radio/image');

		return $output;
	}
}