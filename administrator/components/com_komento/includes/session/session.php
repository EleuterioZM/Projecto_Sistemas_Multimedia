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

class KomentoSession
{
	var $session;

	public function __construct()
	{
		$this->session = JFactory::getSession();
		return $this->session;
	}

	/**
	 * Allows caller to generate current timestamp
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTime()
	{
		return $this->session->get('time', time(), 'com_komento');
	}

	/**
	 * Allows caller to generate current timestamp
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function setTime()
	{
		$this->session->set('time', time(), 'com_komento');
	}

	public function getLastReplyTime()
	{
		return unserialize($this->session->get('komento_last_reply'));
	}

	public function setReplyTime()
	{
		return $this->session->set('komento_last_reply', serialize(FH::date()->toUnix()));
	}
}
