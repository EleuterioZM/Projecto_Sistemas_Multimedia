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

class Html extends Base
{
	/**
	 * Renders the empty state
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function emptyList($message, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$icon = \FH::normalize($options, 'icon', '');
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr'));
		$action = \FH::normalize($options, 'action', false);
		$actionLink = \FH::normalize($options, 'actionLink', 'javascript:void(0);');
		$actionMessage = \FH::normalize($options, 'actionMessage', '');

		$themes = $this->getTemplate();
		$themes->set('icon', $icon);
		$themes->set('class', $class);
		$themes->set('message', $message);
		$themes->set('attributes', $attributes);
		$themes->set('action', $action);
		$themes->set('actionLink', $actionLink);
		$themes->set('actionMessage', $actionMessage);

		$output = $themes->output('html/html/empty.list');

		return $output;
	}

	/**
	 * Renders name of the object
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function name($name, $options = [])
	{
		$useAnchorTag = \FH::normalize($options, 'useAnchorTag', true);
		$permalink = \FH::normalize($options, 'permalink', 'javascript:void(0);');
		$verified = \FH::normalize($options, 'verified', false);
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr'));
		$class = \FH::normalize($options, 'class', '');

		$theme = $this->getTemplate();
		$theme->set('class', $class);
		$theme->set('name', $name);
		$theme->set('useAnchorTag', $useAnchorTag);
		$theme->set('permalink', $permalink);
		$theme->set('verified', $verified);
		$theme->set('attributes', $attributes);

		$output = $theme->output('html/html/name');

		return $output;
	}

	/**
	 * Renders the tooltip markup
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function tooltip($appearance = 'light', $accent = 'foundry')
	{
		static $isLoaded = false;

		// Return nothing when it has been loaded on the page once
		if ($isLoaded) {
			return '';
		}

		Scripts::load('popper');
		Scripts::load('tippy');

		$theme = $this->getTemplate();
		$theme->set('appearance', $appearance);
		$theme->set('accent', $accent);
		$tooltip = $theme->output('html/html/tooltip');

		$isLoaded = true;

		return $tooltip;
	}

	/**
	 * Renders the popover markup
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function popover($appearance = 'light', $accent = 'foundry')
	{
		static $isLoaded = false;

		// Return nothing when it has been loaded on the page once
		if ($isLoaded) {
			return '';
		}

		Scripts::load('popper');
		Scripts::load('tippy');

		$theme = $this->getTemplate();
		$theme->set('appearance', $appearance);
		$theme->set('accent', $accent);
		$output = $theme->output('html/html/popover');

		$isLoaded = true;

		return $output;
	}

	/**
	 * Renders a well container for text
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function well($contents, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');

		$theme = $this->getTemplate();
		$theme->set('contents', $contents);
		$theme->set('class', $class);

		$output = $theme->output('html/html/well');

		return $output;
	}
}