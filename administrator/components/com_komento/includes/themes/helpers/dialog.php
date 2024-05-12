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

class KomentoThemesDialog
{
	/**
	 * Renders the close dialog button
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function cancelButton($title = 'COM_KOMENTO_CANCEL_BUTTON', $type = 'default', $options = [])
	{
		$options = array_merge($options, [
			'attributes' => 'data-cancel-button'
		]);

		return self::closeButton($title, $type, $options);
	}

	/**
	 * Renders the close dialog button
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function closeButton($title = 'COM_KOMENTO_CLOSE_BUTTON', $type = 'default', $options = [])
	{
		$attributes = FH::normalize($options, 'attributes', 'data-close-button');
		$class = FH::normalize($options, 'class', '');

		return KT::fd()->html('dialog.button', $title, $type, [
			'attributes' => $attributes,
			'class' => $class
		]);
	}

	/**
	 * Renders the primary dialog button
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function submitButton($title, $type = 'primary', $options = [])
	{
		$attributes = FH::normalize($options, 'attributes', 'data-submit-button');
		$class = FH::normalize($options, 'class', '');

		return KT::fd()->html('dialog.button', $title, $type, [
			'attributes' => $attributes,
			'class' => $class
		]);
	}
}
