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

class KomentoCaptcha
{
	public $adapter = null;

	public function __construct()
	{
		$config = KT::config();

		if (!$config->get('antispam_captcha_enable')) {
			return;
		}

		$type = 'captcha';

		if ($config->get('antispam_captcha_type') == 1) {
			$type = 'recaptcha';
		}

		if ($config->get('antispam_captcha_type') === 'hcaptcha') {
			$type = 'hcaptcha';
		}

		$this->adapter = $this->getAdapter($type);
	}

	public function getAdapter($type)
	{
		$file = __DIR__ . '/adapters/' . $type . '.php';

		require_once($file);

		$className = 'KomentoCaptcha' . ucfirst($type);
		$adapter = new $className();

		return $adapter;
	}

	/**
	 * Generates the html for captcha to display on the form.
	 *
	 * @since	3.0
	 * @access	public
	 **/
	public function html()
	{
		return $this->adapter->html();
	}

	/**
	 * Clear expired captcha records
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function clear()
	{
	    $db = KT::db();
	    $date = FH::date();

	    $query  = 'DELETE FROM ' . $db->nameQuote('#__komento_captcha') . ' WHERE ' . $db->nameQuote('created') . ' <= DATE_SUB( ' . $db->Quote($date->toSql()) . ', INTERVAL 12 HOUR)';
	    $db->setQuery($query);
	    $db->query();

	    return true;
	}

	/**
	 * Verifies the captcha response
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function verify($data, $params = array())
	{
		return $this->adapter->verify($data);
	}

	/**
	 * Retrieves the error message
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getError()
	{
		return $this->adapter->getError();
	}

	/**
	 * Retrieves the captcha reloading syntax
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getReloadSyntax()
	{
		return $this->adapter->getReloadSyntax();
	}
}
