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

class Button extends Base
{
	/**
	 * Renders a social button on the page based on the type given
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	private function social($type, $icon, $text, $size = 'sm', $options = [])
	{
		$url = \FH::normalize($options, 'url', 'javascript:void(0);');
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');
		$block = \FH::normalize($options, 'block', false);
		$imageIcon = \FH::normalize($options, 'imageIcon', false);
		$iconOnly = \FH::normalize($options, 'iconOnly', false);

		// Ensure that it is translated
		$text = \JText::_($text);

		$theme = $this->getTemplate();
		$theme->set('iconOnly', $iconOnly);
		$theme->set('size', $size);
		$theme->set('type', $type);
		$theme->set('icon', $icon);
		$theme->set('text', $text);
		$theme->set('attributes', $attributes);
		$theme->set('url', $url);
		$theme->set('class', $class);
		$theme->set('block', $block);
		$theme->set('imageIcon', $imageIcon);

		return $theme->output('html/button/social');
	}

	/**
	 * Renders a sign in with Apple button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function apple($text = '', $options = [], $size = 'sm')
	{
		if ($text === '') {
			$text = 'FD_SIGN_IN_WITH_APPLE';
		}

		$mode = \FH::normalize($options, 'mode', 'light');
		$options['class'] = $mode === 'light' ? 'o-btn--apple--l' : 'o-btn--apple--d';

		return $this->social('apple', 'fdi fab fa-apple', $text, $size, $options);
	}

	/**
	 * Renders an e-mail social button
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function email($text = '', $options = [], $size = 'sm')
	{
		return $this->social('email', 'fdi fa fa-fw fa-envelope mr-2xs', $text, $size, $options);
	}

	/**
	 * Renders a sign in with Facebook button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function facebook($text = '', $options = [], $size = 'sm')
	{
		if ($text === '') {
			$text = 'FD_SIGN_IN_WITH_FB';
		}

		// Backward compatibility
		$authorizedUrl = \FH::normalize($options, 'authorizedUrl', 'javascript:void(0);');
		$options['url'] = $authorizedUrl;

		return $this->social('facebook', 'fdi fab fa-facebook', $text, $size, $options);
	}

	/**
	 * Renders a sign in with Google button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function google($text = '', $options = [], $size = 'sm')
	{
		if ($text === '') {
			$text = 'FD_SIGN_IN_WITH_GOOGLE';
		}

		$options['imageIcon'] = FD_URI_MEDIA . '/images/logo-google.svg';

		return $this->social('google', false, $text, $size, $options);
	}

	/**
	 * Renders a sign in with LinkedIn button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function linkedin($text = '', $options = [], $size = 'sm')
	{
		if ($text === '') {
			$text = 'FD_SIGN_IN_WITH_LINKEDIN';
		}

		return $this->social('linkedin', 'fdi fab fa-linkedin', $text, $size, $options);
	}

	/**
	 * Renders a sign in with Pinterest button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function pinterest($text = '', $options = [], $size = 'sm')
	{
		return $this->social('pinterest', 'fdi fab fa-pinterest', $text, $size, $options);
	}

	/**
	 * Renders a Pocket button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function pocket($text = '', $options = [], $size = 'sm')
	{
		return $this->social('pocket', 'fdi fab fa-get-pocket', $text, $size, $options);
	}

	/**
	 * Renders a Reddit button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function reddit($text = '', $options = [], $size = 'sm')
	{
		return $this->social('reddit', 'fdi fab fa-reddit', $text, $size, $options);
	}

	/**
	 * Renders a hyperlink that behaves like a button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function link($link, $text, $class = 'default', $size = 'default', $options = [], $newWindow = false)
	{
		if (is_null($link)) {
			$link = 'javascript:void(0);';
		}

		$options = array_merge($options, [
			'type' => 'link',
			'link' => $link
		]);

		if ($newWindow) {
			$options['attributes'] = \FH::normalize($options, 'attributes', '');
			$options['attributes'] .= ' target="_blank"';
		}

		return $this->standard($text, $class, $size, $options);
	}

	/**
	 * Renders a submit button on the page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function submit($text, $class = 'default', $size = 'default', $options = [])
	{
		$options = array_merge($options, [
			'type' => 'submit'
		]);

		return $this->standard($text, $class, $size, $options);
	}

	/**
	 * Renders a standard button on the page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function standard($text, $class = 'default', $size = 'default', $options = [])
	{
		$icon = \FH::normalize($options, 'icon', null);
		$attributes = \FH::normalize($options, 'attributes', \FH::normalize($options, 'attr', ''));
		$link = \FH::normalize($options, 'link', '');
		$type = \FH::normalize($options, 'type', 'standard');
		$outline = \FH::normalize($options, 'outline', false);
		$ghost = \FH::normalize($options, 'ghost', false);
		$block = \FH::normalize($options, 'block', false);
		$extraClass = \FH::normalize($options, 'class', '');
		$iconOnly = \FH::normalize($options, 'iconOnly', false);

		// Normalize the button type
		if (!in_array($type, ['standard', 'link', 'submit'])) {
			$type = 'standard';
		}

		// Normalize the button class
		if (!in_array($class, ['default', 'override', 'primary', 'success', 'info', 'warning', 'danger'])) {
			$class = 'default';
		}

		// Normalize the button sizes
		if (!in_array($size, ['custom', 'xs', 'sm', 'md', 'lg', 'xl', '2xl'])) {
			$size = 'md';
		}

		if ($outline) {
			$class .= '-o';
		}

		if ($ghost) {
			$class .= '-ghost';
		}

		$text = \JText::_($text);

		$namespace = 'standard';

		if ($type === 'link') {
			$namespace = 'link';
		}

		$theme = $this->getTemplate();
		$theme->set('iconOnly', $iconOnly);
		$theme->set('type', $type);
		$theme->set('size', $size);
		$theme->set('block', $block);
		$theme->set('extraClass', $extraClass);
		$theme->set('attributes', $attributes);
		$theme->set('icon', $icon);
		$theme->set('link', $link);
		$theme->set('text', $text);
		$theme->set('class', $class);

		$namespace = 'html/button/' . $namespace;

		return $theme->output($namespace);
	}

	/**
	 * Renders a sign in with Twitter button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function twitter($text = '', $options = [], $size = 'sm')
	{
		if ($text === '') {
			$text = 'FD_SIGN_IN_WITH_TWITTER';
		}

		return $this->social('twitter', 'fdi fab fa-twitter', $text, $size, $options);
	}

	/**
	 * Renders a sign in with Twitch button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function twitch($text = '', $options = [], $size = 'sm')
	{
		return $this->social('twitch', 'fdi fab fa-twitch', $text, $size, $options);
	}

	/**
	 * Renders a vk button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function vk($text = '', $options = [], $size = 'sm')
	{
		return $this->social('vk', 'fdi fab fa-vk', $text, $size, $options);
	}

	/**
	 * Renders a xing button
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function xing($text = '', $options = [], $size = 'sm')
	{
		return $this->social('xing', 'fdi fab fa-xing', $text, $size, $options);
	}
}
