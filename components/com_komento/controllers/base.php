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

class KomentoControllerBase extends JControllerLegacy
{
	protected $app	= null;
	protected $input = null;
	protected $my = null;

	// This will notify the parent class that this is for the back end.
	protected $location = 'frontend';

	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = KT::getConfig();
		$this->doc = JFactory::getDocument();
		$this->profile = KT::user();
		$this->access = KT::acl();
		$this->info = KT::info();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = KT::ajax();
		}

		$this->input = KT::request();
	}

	/**
	 * Retrieves the redirection url
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getReturnUrl($inputName = 'currentUrl')
	{
		$returnUrl = $this->input->get($inputName, '', 'default');

		if ($returnUrl) {
			$returnUrl = base64_decode($returnUrl);

			return $returnUrl;
		}

		return false;
	}
}
