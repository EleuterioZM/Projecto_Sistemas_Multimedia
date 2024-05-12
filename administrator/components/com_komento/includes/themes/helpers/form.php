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

class KomentoThemesForm
{
	/**
	 * Renders a honeypot hidden input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function honeypot($name = '', $attributes = '')
	{
		if (!$name) {
			$config = KT::config();
			$name = $config->get('antispam_honeypot_key');
		}

		return KT::fd()->html('form.text', $name, '', $name, [
			'attributes' => 'style="display: none;"'
		]);
	}
 
	/**
	 * Generates a dropdown list of available extensions integration
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function extensions($inputName, $selected = '', $attributes = [])
	{
		// Get a list of components
		$extensions = KT::components()->getAvailableComponents();

		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$options = [
			'' => 'COM_KOMENTO_SELECT_EXTENSION'
		];

		foreach ($extensions as $extension) {
			$options[$extension] = 'COM_KOMENTO_' . strtoupper($extension);
		}

		return KT::fd()->html('form.dropdown', $inputName, $selected, $options, ['attributes' => $attributes]);
	}
}
