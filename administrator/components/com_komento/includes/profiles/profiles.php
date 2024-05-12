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

require_once(__DIR__ . '/adapters/default.php');

class KomentoProfiles extends KomentoBase
{
	protected $user = null;
	protected $adapter = null;

	public function __construct($user, $adapter = 'default')
	{
		parent::__construct();

		$this->user = $user;
		$this->adapter = $this->getAdapter($adapter, $user);
	}

	/**
	 * Loads the adapter for the profile system
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAdapter($adapter, $user)
	{
		$file = __DIR__ . '/adapters/' . strtolower($adapter) . '.php';

		require_once($file);

		$className = 'KomentoProfiles'. ucfirst($adapter);

		if (!class_exists($className)) {
			$className = 'KomentoProfilesDefault';
		}

		$adapter = new $className($user);

		if (!$adapter->exists()) {
			$adapter = new KomentoProfilesDefault($user);
		}

		return $adapter;
	}

	public function getAvatar($email = '')
	{
		return $this->adapter->getAvatar($email);
	}

	public function getLink($email = null, $website = '')
	{
		return $this->adapter->getLink($email, $website);
	}
}