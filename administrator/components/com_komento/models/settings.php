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

class KomentoModelSettings extends KomentoModel
{
	protected $element = 'settings';

	/**
	 * Saves the settings
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save($data)
	{
		unset($data['component']);

		$config	= KT::table('Configs');
		$config->load('config');

		$registry = new JRegistry($this->getSettingsParams());

		foreach ($data as $index => $value) {

			// If the value is an array, we would assume that it should be comma separated
			if (is_array($value)) {
				$value = implode(',', $value);
			}

			$registry->set($index, $value);
		}

		// Get the complete INI string
		$config->params	= $registry->toString();

		// Save it
		if (!$config->store()) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the raw data from the database for the config
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRawData()
	{
		$db = KT::db();

		$query	= 'SELECT ' . $db->quoteName('params') . ' '
				. 'FROM ' . $db->quoteName('#__komento_configs') . ' '
				. 'WHERE ' . $db->nameQuote('name') . '=' . $db->Quote('config');

		$db->setQuery($query);

		$result = $db->loadResult();

		// Beautify the result
		$result = json_encode(json_decode($result), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		return $result;
	}

	public function getSettingsParams($key = 'config')
	{
		static $params	= null;

		if (is_null($params)) {
			$db = KT::db();

			$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__komento_configs' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote($key);

			$db->setQuery($query);

			$params	= $db->loadResult();
		}

		return $params;
	}

	/**
	 * Update Email Logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function updateEmailLogo($file)
	{
		$notification = KT::notification();

		return $notification->storeEmailLogo($file);
	}
}
