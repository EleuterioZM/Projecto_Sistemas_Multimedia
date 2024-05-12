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

class KomentoTableSubscription extends KomentoTable
{
	public $id = null;
	public $type = null;
	public $component = null;
	public $cid = '';
	public $userid = null;
	public $fullname = '';
	public $email = '';
	public $created = null;
	public $published = null;
	public $interval = null;
	public $sent_out = null;
	public $count = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_subscription', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		if (empty($this->interval)) {
			$this->interval = 'instant';
		}

		if (empty($this->created)) {
			$this->created = FH::date()->toSql();
		}

		if (empty($this->sent_out)) {
			$this->sent_out = FH::date()->toSql();
		}

		return parent::store($updateNulls);
	}

	/**
	 * Validate to see if the current record is valid
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function validate()
	{
		if (!$this->email) {
			$this->setError(JText::_('COM_KOMENTO_SUBSCRIBERS_EMAIL_IS_REQUIRED'));
			return false;
		}

		if (!$this->fullname) {
			$this->setError(JText::_('COM_KOMENTO_SUBSCRIBERS_NAME_IS_REQUIRED'));
			return false;
		}

		if (!$this->cid) {
			$this->setError(JText::_('COM_KOMENTO_SUBSCRIBERS_CID_IS_REQUIRED'));
			return false;
		}

		if (!$this->component) {
			$this->setError(JText::_('COM_KOMENTO_SUBSCRIBERS_COMPONENT_IS_REQUIRED'));
			return false;
		}

		return true;
	}

	/**
	 * Determines if the user's subscription is still pending due to double opt-in
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isPending()
	{
		return $this->published == KT_SUBSCRIPTION_PENDING;
	}

	/**
	 * Determines if the user's subscription is still pending due to double opt-in
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isPublished()
	{
		return $this->published == KT_SUBSCRIPTION_PUBLISHED;
	}

	/**
	 * Determine this subscriber  hether already exits
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isSubscribed()
	{
		$model = KT::model('Subscription');
		$exists = $model->checkSubscriptionExist($this->component, $this->cid, 0, $this->email);

		if ($exists != null) {
			$this->setError(JText::_('COM_KT_SUBSCRIPTION_ALREADY_SUBSCRIBED_ERROR'));
		}
		
		return $exists;
	}

	public function getItemTitle()
	{
		static $_cache = [];

		// contenttitle is generated from subscription formatter
		if (isset($this->contenttitle)) {
			return $this->contenttitle;
		}

		$idx = $this->component . $this->cid;

		if (!isset($_cache[$idx])) {

			// set extension object
			$extension = $this->loadComponent();

			// get permalink
			$_cache[$idx] = $extension->getContentTitle();
		}
	}

	/**
	 * Retrieves the content item's permalink
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getItemPermalink()
	{
		static $_cache = [];

		// pagelink is generated from subscription formatter
		if (isset($this->pagelink)) {
			return $this->pagelink;
		}

		$idx = $this->component . $this->cid;

		if (!isset($_cache[$idx])) {

			// set extension object
			$extension = $this->loadComponent();

			// get permalink
			$_cache[$idx] = $extension->getContentPermalink();
		}

		return $_cache[$idx];
	}


	/**
	 * Retrieves the component's title
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getComponentTitle()
	{
		static $_cache = [];

		// componenttitle is generated from subscription formatter
		if (isset($this->componenttitle)) {
			return $this->componenttitle;
		}

		$idx = $this->component . $this->cid;

		if (!isset($_cache[$idx])) {

			// set extension object
			$extension = $this->loadComponent();

			// get permalink
			$_cache[$idx] = $extension->getComponentName();
		}

		return $_cache[$idx];
	}

	/**
	 * Return the component that being subscribed
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function loadComponent()
	{
		// set extension object
		$extension = KT::loadApplication($this->component)->load($this->cid);

		if ($extension === false) {
			$extension = KT::getErrorApplication($this->component, $this->cid);
		}

		return $extension;
	}
}
