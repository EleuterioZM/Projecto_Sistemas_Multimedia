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

class Settings extends Base
{
	/**
	 * Renders settings for dropdown used at the back-end
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function dropdown($name, $title, $options = [], $desc = '', $attributes = '', $note = '', $formOptions = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		if ($note && !is_callable($note)) {
			$note = \JText::_($note);
		}

		$wrapperAttributes = \FH::normalize($formOptions, 'wrapperAttributes', '');
		$wrapperClass = \FH::normalize($formOptions, 'wrapperClass', '');
		$class = \FH::normalize($formOptions, 'class', '');
		$showOverlay = \FH::normalize($formOptions, 'overlay', false);
		$overlayText = \FH::normalize($formOptions, 'overlayText', 'FD_UPGRADE_TO_PRO');
		$upgradeUrl = \FH::normalize($formOptions, 'upgradeUrl', '');

		$theme = $this->getTemplate();
		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('class', $class);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('wrapperAttributes', $wrapperAttributes);
		$theme->set('showOverlay', $showOverlay);
		$theme->set('overlayText', $overlayText);
		$theme->set('upgradeUrl', $upgradeUrl);

		$contents = $theme->output('html/settings/dropdown');

		return $contents;
	}

	/**
	 * Renders settings for multiple select list used at the back-end
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function multilist($name, $title, $options = [], $desc = '', $attributes = '', $note = '', $formOptions = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		if ($note) {
			$note = \JText::_($note);
		}

		$wrapperAttributes = \FH::normalize($formOptions, 'wrapperAttributes', '');
		$wrapperClass = \FH::normalize($formOptions, 'wrapperClass', '');
		$class = \FH::normalize($formOptions, 'class', '');

		$theme = $this->getTemplate();
		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('class', $class);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('html/settings/multilist');

		return $contents;
	}

	/**
	 * Renders a password for extension settings
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function password($name, $title, $desc = '', $options = [], $instructions = '', $class = '')
	{
		if (!$desc && $desc !== false) {
			$desc = $title . '_DESC';
		}

		$size = \FH::normalize($options, 'size', '');
		$postfix = \FH::normalize($options, 'postfix', '');
		$prefix = \FH::normalize($options, 'prefix', '');
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');

		// Automatically center the text on the input when the input size is small
		if ($size && $size <= 8 && !$class) {
			$class .= ' text-center';
		}

		$theme = $this->getTemplate();
		$theme->set('attributes', $attributes);
		$theme->set('size', $size);
		$theme->set('class', $class);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);
		$theme->set('postfix', $postfix);

		$contents = $theme->output('html/settings/password');

		return $contents;
	}

	/**
	 * Renders settings for dropdown used at the back-end
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function select2($name, $title, $options = [], $desc = '', $attributes = '', $note = '', $formOptions = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		if ($note && !is_callable($note)) {
			$note = \JText::_($note);
		}

		$wrapperAttributes = \FH::normalize($formOptions, 'wrapperAttributes', '');
		$wrapperClass = \FH::normalize($formOptions, 'wrapperClass', '');
		$class = \FH::normalize($formOptions, 'class', '');
		$multiple = \FH::normalize($formOptions, 'multiple', false);
		
		$theme = $this->getTemplate();
		$theme->set('multiple', $multiple);
		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('class', $class);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('html/settings/select2');

		return $contents;
	}

	/**
	 * Renders a textbox for extension settings
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function text($name, $title, $desc = '', $options = [], $instructions = '', $class = '')
	{
		if (!$desc && $desc !== false) {
			$desc = $title . '_DESC';
		}

		$size = \FH::normalize($options, 'size', '');
		$postfix = \FH::normalize($options, 'postfix', '');
		$prefix = \FH::normalize($options, 'prefix', '');
		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');
		$help = \FH::normalize($options, 'help', '');

		// Ported from Komento
		$wrapperAttributes = \FH::normalize($options, 'wrapperAttributes', '');
		$visible = \FH::normalize($options, 'visible', true);

		// Automatically center the text on the input when the input size is small
		if ($size && $size <= 8 && !$class) {
			$class .= ' text-center';
		}

		$theme = $this->getTemplate();
		$theme->set('help', $help);
		$theme->set('wrapperAttributes', $wrapperAttributes);
		$theme->set('visible', $visible);
		$theme->set('attributes', $attributes);
		$theme->set('size', $size);
		$theme->set('class', $class);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);
		$theme->set('postfix', $postfix);

		$contents = $theme->output('html/settings/text');

		return $contents;
	}

	/**
	 * Renders a settings with textarea
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function textarea($name, $title, $desc = '', $instructions = '', $options = [])
	{
		if (empty($desc)) {
			$desc 	= $title . '_DESC';
		}

		$wrapperAttributes = \FH::normalize($options, 'wrapperAttributes', '');
		$visible = \FH::normalize($options, 'visible', true);

		$theme = $this->getTemplate();
		$theme->set('wrapperAttributes', $wrapperAttributes);
		$theme->set('visible', $visible);
		$theme->set('options', $options);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('html/settings/textarea');

		return $contents;
	}

	/**
	 * Renders a toggle input
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function toggle($name, $title, $desc = '', $attributes = '', $note = '', $wrapperAttributes = '', $options = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		if ($note && !is_callable($note)) {
			$note = \JText::_($note);
		}

		if (is_array($wrapperAttributes)) {
			$wrapperAttributes = implode(' ', $wrapperAttributes);
		}

		$wrapperClass = \FH::normalize($options, 'wrapperClass', '');
		$showOverlay = \FH::normalize($options, 'overlay', false);
		$overlayText = \FH::normalize($options, 'overlayText', 'FD_UPGRADE_TO_PRO');
		$upgradeUrl = \FH::normalize($options, 'upgradeUrl', '');

		$theme = $this->getTemplate();

		$theme->set('options', $options);
		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('wrapperAttributes', $wrapperAttributes);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('showOverlay', $showOverlay);
		$theme->set('overlayText', $overlayText);
		$theme->set('upgradeUrl', $upgradeUrl);

		$contents = $theme->output('html/settings/toggle');

		return $contents;
	}

	/**
	 * Renders the user browser
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function user($name, $title, $desc = '', $options = [], $instructions = '', $class = '')
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$attributes = \FH::normalize($options, 'attributes', '');
		$class = \FH::normalize($options, 'class', '');
		$columns = \FH::normalize($options, 'columns', 10);

		$theme = $this->getTemplate();
		$theme->set('columns', $columns);
		$theme->set('options', $options);
		$theme->set('attributes', $attributes);
		$theme->set('class', $class);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('html/settings/user');

		return $contents;
	}

	/**
	 * Renders the user groups tree checkbox
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function userGroupsTree($name, $title, $desc = '', $checkSuperAdmin = false, $options = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$note = \FH::normalize($options, 'note', '');

		$theme = $this->getTemplate();
		$theme->set('options', $options);
		$theme->set('checkSuperAdmin', $checkSuperAdmin);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('note', $note);

		$contents = $theme->output('html/settings/user.groups.tree');

		return $contents;
	}
}
