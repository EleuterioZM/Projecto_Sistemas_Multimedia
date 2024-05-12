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

class Dropdown extends Base
{
	/**
	 * Renders a dropdown footer section
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function footer($text)
	{
		$contents = '';

		if (is_string($text)) {
			$contents = \JText::_($text);
		}

		if (is_callable($text)) {
			$contents = $text();
		}

		$theme = $this->getTemplate();
		$theme->set('contents', $contents);

		$contents = $theme->output('html/dropdown/footer');

		return $contents;
	}

	/**
	 * Renders a dropdown header section
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function header($text)
	{
		$contents = '';

		if (is_string($text)) {
			$contents = \JText::_($text);
		}

		if (is_callable($text)) {
			$contents = $text();
		}

		$theme = $this->getTemplate();
		$theme->set('contents', $contents);

		$contents = $theme->output('html/dropdown/header');

		return $contents;
	}

	/**
	 * Renders a dropdown item
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function item($text, $link = null, $options = [])
	{
		$attributes = \FH::normalize($options, 'attributes', '');
		$wrapperClass = \FH::normalize($options, 'wrapperClass', '');
		$active = \FH::normalize($options, 'active', false);

		// Additional class on the item
		$class = \FH::normalize($options, 'class', '');

		if (is_null($link)) {
			$link = 'javascript:void(0);';
		}

		$theme = $this->getTemplate();
		$theme->set('class', $class);
		$theme->set('text', $text);
		$theme->set('link', $link);
		$theme->set('attributes', $attributes);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('active', $active);
		
		$contents = $theme->output('html/dropdown/item');

		return $contents;
	}

	/**
	 * Renders settings for dropdown used at the back-end
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function standard($getButton, $getItems, $options = [])
	{
		Scripts::load('shared');
		Scripts::load('popper');
		Scripts::load('tippy');

		$header = \FH::normalize($options, 'header', '');
		$footer = \FH::normalize($options, 'footer', '');
		$divider = \FH::normalize($options, 'divider', true);
		$placement = \FH::normalize($options, 'placement', 'bottom');
		$class = \FH::normalize($options, 'class', '');
		$arrow = \FH::normalize($options, 'arrow', false);
		$appearance = \FH::normalize($options, 'appearance', 'light');
		$accent = \FH::normalize($options, 'theme', 'foundry');

		// Determines the trigger style
		$trigger = \FH::normalize($options, 'trigger', 'click');

		// This determines if the dropdown should be appended to a specific target
		$target = \FH::normalize($options, 'target', 'self');

		$button = $getButton;

		if (is_callable($getButton)) {
			$button = $getButton();
		}

		$items = $getItems;

		if (is_callable($getItems)) {
			$items = $getItems();
		}

		// Event trigger callbacks
		$mount = \FH::normalize($options, 'mount', '');
		$create = \FH::normalize($options, 'create', '');
		$destroy = \FH::normalize($options, 'destroy', '');
		$hidden = \FH::normalize($options, 'hidden', '');
		$content = \FH::normalize($options, 'content', '');
		$show = \FH::normalize($options, 'show', '');

		$theme = $this->getTemplate();
		$theme->set('appearance', $appearance);
		$theme->set('footer', $footer);
		$theme->set('accent', $accent);
		$theme->set('arrow', $arrow);
		$theme->set('content', $content);
		$theme->set('show', $show);
		$theme->set('mount', $mount);
		$theme->set('destroy', $destroy);
		$theme->set('hidden', $hidden);
		$theme->set('create', $create);
		$theme->set('trigger', $trigger);
		$theme->set('items', $items);
		$theme->set('class', $class);
		$theme->set('placement', $placement);
		$theme->set('target', $target);
		$theme->set('button', $button);
		$theme->set('header', $header);
		$theme->set('divider', $divider);

		$contents = $theme->output('html/dropdown/standard');

		return $contents;
	}
}
