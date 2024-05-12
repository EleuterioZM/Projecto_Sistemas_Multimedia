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

use Foundry\Helpers\StringHelper;

class KomentoHoneypot
{
	public function __construct()
	{
		$this->config = KT::config();
		$this->input = JFactory::getApplication()->input;
	}

	/**
	 * Retrieves the honeypot key to be used in the form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getKey()
	{
		$key = $this->config->get('antispam_honeypot_key');
		return $key;
	}

	/**
	 * Generates a random honeypot key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function generateKey($length = 8)
	{
		return StringHelper::generateRandomWord($length);
	}

	/**
	 * Determines if the spammer is trapped
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isTrapped()
	{
		$key = $this->getKey();

		$value = $this->input->get($key, '', 'default');

		if ($value) {
			return true;
		}

		return false;
	}
}
