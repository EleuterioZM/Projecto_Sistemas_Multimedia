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

class Avatar extends Base
{
	/**
	 * Centralized method of retrieving the html output of the avatar
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function render($url, $size, $useAnchorTag, $options = [])
	{
		$sizes = [
			'xs' => 16,
			'sm' => 24,
			'md' => 32,
			'lg' => 64,
			'xl' => 120,
			'default' => 40
		];

		// Ensure that the size provided, truly exists
		if (!in_array($size, array_keys($sizes))) {
			$size = 'default';
		}

		$name = \FH::normalize($options, 'name', '');
		$permalink = \FH::normalize($options, 'permalink', 'javascript:void(0)');
		$attributes = \FH::normalize($options, 'attributes', '');
		$anchorAttributes = \FH::normalize($options, 'anchorAttributes', '');
		$tooltip = \FH::normalize($options, 'tooltip', false);
		$isOnline = \FH::normalize($options, 'isOnline', false);
		$isMobile = \FH::normalize($options, 'isMobile', false);
		$showOnlineState = \FH::normalize($options, 'showOnlineState', false);
		$class = \FH::normalize($options, 'class', '');

		// Allow caller to force a style
		$avatarStyle = \FH::normalize($options, 'style', 'rounded');
		$style = '';

		$class .= $size == 'default' ? '' : ' o-avatar--' . $size;
		$width = $sizes[$size];
		$height = $sizes[$size];

		if ($avatarStyle === 'rounded') {
			$style = 'o-avatar--rounded';
		}

		if ($showOnlineState) {
			$isOnline = $isOnline ? ' is-online' : ' is-offline';
			$isMobile = $isMobile ? ' is-mobile' : '';

			$class .= $isOnline . $isMobile;
		}

		$theme = $this->getTemplate();
		$theme->set('style', $style);
		$theme->set('url', $url);
		$theme->set('class', $class);
		$theme->set('name', $name);
		$theme->set('useAnchorTag', $useAnchorTag);
		$theme->set('anchorAttributes', $anchorAttributes);
		$theme->set('permalink', $permalink);
		$theme->set('attributes', $attributes);
		$theme->set('tooltip', $tooltip);
		$theme->set('width', $width);
		$theme->set('height', $height);
		$theme->set('isOnline', $isOnline);
		$theme->set('isMobile', $isMobile);

		$output = $theme->output('html/avatar/default');

		return $output;
	}

	/**
	 * Renders the extra small avatar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function xs($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}

	/**
	 * Renders the avatar of the user
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function sm($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}

	/**
	 * Renders the medium avatar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function md($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}

	/**
	 * Renders the large avatar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function lg($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}

	/**
	 * Renders the extra large avatar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function xl($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}

	/**
	 * Renders the default avatar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function default($url, $useAnchorTag = true, $options = [])
	{
		return $this->render($url, __FUNCTION__, $useAnchorTag, $options);
	}
}
