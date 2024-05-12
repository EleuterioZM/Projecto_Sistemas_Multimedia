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
use Foundry\Libraries\Scripts;

class Alert extends Base
{
	/**
	 * Renders the standard alert with foundry. Supports the following classes:
	 *
	 * success, info, warning and danger
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function standard($text, $class = 'info', $options = [])
	{
		Scripts::load('shared');

		$allowed = ['success', 'info', 'warning', 'danger'];

		if (!in_array($class, $allowed)) {
			$class = 'info';
		}

		$icon = \FH::normalize($options, 'icon', '');
		$dismissible = \FH::normalize($options, 'dismissible', true);
		$button = \FH::normalize($options, 'button', null);
		$attributes = \FH::normalize($options, 'attributes', '');
		$customClass = \FH::normalize($options, 'customClass', '');

		$theme = $this->getTemplate();
		$theme->set('customClass', $customClass);
		$theme->set('dismissible', $dismissible);
		$theme->set('text', $text);
		$theme->set('class', $class);
		$theme->set('icon', $icon);
		$theme->set('button', $button);
		$theme->set('attributes', $attributes);

		$output = $theme->output('html/alert/standard');

		return $output;
	}

	/**
	 * Renders extended alert with foundry to display text, icon, close and buton. Supports the following classes:
	 *
	 * success, info, warning and danger
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function extended($title, $description = null, $class = 'info', $options = [])
	{
		Scripts::load('shared');

		$allowed = ['success', 'info', 'warning', 'danger'];

		if (!in_array($class, $allowed)) {
			$class = 'info';
		}

		$icon = \FH::normalize($options, 'icon', null);
		$button = \FH::normalize($options, 'button', null);
		$dismissible = \FH::normalize($options, 'dismissible', true);
		$customClass = \FH::normalize($options, 'class', '');

		$theme = $this->getTemplate();
		$theme->set('icon', $icon);
		$theme->set('button', $button);
		$theme->set('dismissible', $dismissible);
		$theme->set('title', $title);
		$theme->set('description', $description);
		$theme->set('class', $class);
		$theme->set('customClass', $customClass);
		$output = $theme->output('html/alert/extended');

		return $output;
	}
}