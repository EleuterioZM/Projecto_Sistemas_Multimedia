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

require_once(JPATH_ADMINISTRATOR . '/components/com_komento/constants.php');

class KomentoBase
{
	public $config = null;
	public $jConfig = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $doc = null;
	protected $error = null;

	public function __construct()
	{
		if (!defined('KOMENTO_CLI')) {
			$this->doc = JFactory::getDocument();
			$this->app = JFactory::getApplication();
			$this->input = $this->app->input;
			$this->jConfig = JFactory::getConfig();
			$this->my = JFactory::getUser();
			$this->profile = KT::user();
			$this->access = KT::acl();
		}

		$this->config = KT::config();
	}

	public function setError($message)
	{
		$this->error = $message;
	}

	public function getError()
	{
		if (!$this->error) {
			return false;
		}

		return JText::_($this->error);
	}
}