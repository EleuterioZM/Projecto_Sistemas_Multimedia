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

class Label extends Base
{
	/**
	 * Renders the standard label
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function standard($text = '', $type = 'info', $options = [])
	{
		$showRemove = \FH::normalize($options, 'showRemove', false);
		$rounded = \FH::normalize($options, 'rounded', true);
		$icon = \FH::normalize($options, 'icon', '');

		$class = \FH::normalize($options, 'class', '');
		$class .= $rounded ? ' rounded-full' : '';

		// Ensure that it is translated
		$text = \JText::_($text);

		$themes = $this->getTemplate();
		$themes->set('text', $text);
		$themes->set('class', $class);
		$themes->set('type', $type);
		$themes->set('icon', $icon);
		$themes->set('showRemove', $showRemove);

		$output = $themes->output('html/label/standard');

		return $output;
	}

	/**
	 * Renders the avatar label
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function avatar($url, $text = '', $type = 'info', $options = [])
	{
		$showRemove = \FH::normalize($options, 'showRemove', false);

		// Ensure that it is translated
		$text = \JText::_($text);

		$themes = $this->getTemplate();
		$themes->set('text', $text);
		$themes->set('url', $url);
		$themes->set('type', $type);
		$themes->set('showRemove', $showRemove);

		$output = $themes->output('html/label/avatar');

		return $output;
	}
}