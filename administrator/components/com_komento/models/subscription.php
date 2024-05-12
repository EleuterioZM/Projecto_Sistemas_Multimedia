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

class KomentoModelSubscription extends KomentoModel
{
	protected $element = 'subscription';
	public $_total = null;
	public $_pagination	= null;
	public $_data = null;

	public function __construct($config = [])
	{
		parent::__construct($config);

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('com_komento.subscribers.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_komento.subscribers.limitstart', 'limitstart', 0, 'int');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Get subscribers count for an item.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getSubscriberCount($component, $cid, $userOnly = true)
	{
		$db = KT::db();

		$query = "SELECT count(1) from `#__komento_subscription`";
		$query .= " WHERE `component` = " . $db->Quote($component);
		$query .= " AND `cid` = " . $db->Quote($cid);
		if ($userOnly) {
			$query .= " AND `userid` > 0";
		}
		$query .= " AND `published` = " . $db->Quote(KOMENTO_STATE_PUBLISHED);

		$db->setQuery($query);
		$count = $db->loadResult();

		return $count ? $count : 0;
	}

	/**
	 * Determine if the subscription is exists for user or email on specified component
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function checkSubscriptionExist($component, $cid, $userId = 0, $email = '', $type = 'comment')
	{
		$db = KT::db();

		$query = "SELECT `published` from `#__komento_subscription`";
		$query .= " WHERE `component` = " . $db->Quote($component);
		$query .= " AND `cid` = " . $db->Quote($cid);
		$query .= " AND `type` =" . $db->Quote($type);

		if ($userId) {
			$query .= " AND `userid`=" . $db->Quote($userId);
		} 

		if (!$userId) {
			$query .= " AND `email`=" . $db->Quote($email);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves the subscription id for a given user's subscription against the object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSubscriptionId($component, $cid, $userId = null)
	{
		$user = JFactory::getUser($userId);

		$db = KT::db();

		$query = [
			'SELECT `id` from `#__komento_subscription`',
			"WHERE `component` = " . $db->Quote($component),
			"AND `cid` = " . $db->Quote($cid),
			"AND `type` =" . $db->Quote('comment'),
			"AND `userid`=" . $db->Quote($userId)
		];

		$db->setQuery($query);
		$result = (int) $db->loadResult();

		return $result;
	}

	public function getSubscribers($component, $cid)
	{
		$sql = KT::sql();

		$sql->select('#__komento_subscription')
			->column('fullname')
			->column('email')
			->where('component', $component)
			->where('cid', $cid)
			->where('published', 1);

		return $sql->loadObjectList();
	}

	/**
	 * Get total subscribers from the site
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getTotalSubscribers()
	{
		$sql = KT::sql();

		$sql->select('#__komento_subscription')
			->column('1', 'total', 'count', true)
			->where('published', '1');

		$result	= $sql->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function unsubscribe($component, $cid, $userid, $email = '', $type = 'comment')
	{
		$sql = KT::sql();

		$sql->delete('#__komento_subscription')
			->where('component', $component)
			->where('cid', $cid)
			->where('type', $type);

		if ($userid) {
			$sql->where('userid', $userid);
		} else {
			$sql->where('email', $email);
		}

		$state = $sql->query();

		if (!$state) {
			$this->setError($sql->db->getErrorMsg());
			return false;
		}

		return true;
	}

	public function confirmSubscription($token, $returnURL = '')
	{
		// Load the hashkey
		$hashkeys = KT::table('hashkeys');

		$returnURL = base64_decode($returnURL);

		if (!$hashkeys->loadByKey($token)) {
			$this->app->enqueueMessage(JText::_('COM_KT_SUBSCRIPTION_INVALID_TOKEN'), 'error');
			return $this->app->redirect($returnURL);
		}

		if (empty($hashkeys->uid)) {
			$this->app->enqueueMessage(JText::_('COM_KOMENTO_CONFIRM_SUBSCRIPTION_INVALID_SUBSCRIBE_ID'), 'error');
			return $this->app->redirect($returnURL);
		}

		$subscriptionTable = KT::table('subscription');
		$subscriptionTable->load($hashkeys->uid);
		$subscriptionTable->published = 1;

		if ($subscriptionTable->store()) {
			$hashkeys->delete();
		}

		// Get the item permalink so that we can redirect user to a proper page
		// $model = KT::model('Comments');
		// $itemPermalink = $model->getItemPermalink($subscriptionTable->component, $subscriptionTable->cid);

		$this->app->enqueueMessage(JText::_('COM_KOMENTO_NOTIFICATION_CONFIRMED_SUBSCRIPTION'));
		return $this->app->redirect($returnURL);
	}

	public function remove($subscribers = [])
	{
		if ($subscribers == null) {
			return false;
		}

		if (!is_array($subscribers)) {
			$subscribers = array($subscribers);
		}

		if (count($subscribers) < 1) {
			return false;
		}

		$sql = KT::sql();

		$sql->delete('#__komento_subscription')
			->where('id', $subscribers, 'in');

		$state = $sql->query();

		if (!$state) {
			$this->setError($sql->db->getErrorMsg());
			return false;
		}

		return true;
	}

	public function getUniqueComponents()
	{
		$sql = KT::sql();

		$sql->select('#__komento_subscription')
			->column('component', 'component', 'distinct')
			->order('component');

		return $sql->loadResultArray();
	}

	public function getItems()
	{
		if (empty($this->_data)) {
			$sql = $this->buildQuery();

			$sql->limit($this->getState('limitstart'), $this->getState('limit'));

			$this->_data = $sql->loadObjectList();
		}

		return $this->_data;
	}

	public function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = KT::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		if (is_null($this->_total)) {
			$sql = $this->buildQuery();

			$query = $sql->getTotalSql();

			$sql->db->setQuery($query);
			$this->_total = $sql->db->loadResult();
		}

		return $this->_total;
	}

	public function buildQuery()
	{
		$mainframe = JFactory::getApplication();

		$filter_component = $mainframe->getUserStateFromRequest('com_komento.subscribers.filter_component', 'filter_component', 'all', 'string');
		$filter_type = $mainframe->getUserStateFromRequest('com_komento.subscribers.filter_type', 'filter_type', 'comment', 'string');
		$filter_order = $mainframe->getUserStateFromRequest('com_komento.subscribers.filter_order', 'filter_order', 'created', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_komento.subscribers.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word');

		$sql = KT::sql();

		$sql->select('#__komento_subscription');

		if ($filter_component !== 'all') {
			$sql->where('component', $filter_component);
		}

		if ($filter_type !== 'comment') {
			$sql->where('type', $filter_type);
		}

		$sql->order($filter_order, $filter_order_Dir);

		return $sql;
	}

	public function getSubscribeGDPR($userId, $options = [])
	{
		$db = KT::db();

		$limit = isset($options['limit']) ? $options['limit'] : null;
		$exclude = isset($options['exclude']) ? $options['exclude'] : null;

		$query = 'SELECT *';
		$query .= ' FROM `#__komento_subscription`';
		$query .= ' WHERE `userid` = ' . $db->Quote($userId);

		if ($exclude) {
			$query .= ' AND `id` NOT IN(' . implode(',', $exclude) . ')';
		}

		$query .= ' ORDER BY `type`, `created` DESC';
		$query .= ' LIMIT 0,' . $limit;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Retrieve user's subscriptions
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getUserSubscriptions($userId, $options = [])
	{
		$db = KT::db();

		$limit = isset($options['limit']) ? $options['limit'] : null;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : 0;


		$q = [];

		$q[] = "select a.*";
		$q[] = "from `#__komento_subscription` as a";
		$q[] = "where a.`userid` = " . $db->Quote($userId);

		// get the count for pagination
		$qc = $q;

		$qc[0] = "select count(1)";


		// continue to add the limit into q
		$limit = (int) $limit;
		$limitstart = (int) $limitstart;

		if ($limit) {
			$q[] = "LIMIT " . $limitstart . ", " . $limit;
		}

		// now lets glue the pieces
		$query = implode(" ", $q);

		$cQuery = implode(" ", $qc);


		$db->setQuery($cQuery);

		$total = $db->loadResult();

		$subs = [];

		if ($total) {
			$db->setQuery($query);
			$subs = $db->loadObjectList();
		}

		$this->_total = $total;

		return $subs;
	}

	/**
	 * Retrieve user's email digest setting
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateUserSubscriptions($userid, $key, $value)
	{
		$db = KT::db();

		$q = [];

		$q[] = "update `#__komento_subscription` set";
		$q[] = $db->nameQuote($key) . " = " . $db->Quote($value);
		$q[] = "where `userid` = " . $db->Quote($userid);

		$query = implode(" ", $q);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}


	/**
	 * Retrieve subscribers that opt for email digest
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDigestSubscribers($now, $limit = 5)
	{
		$db = KT::db();

		$intervals = ['daily' => 1,
					  'weekly' => 7,
					  'monthly' => 30];

		$unions = [];

		$query = "select * from (";
		foreach($intervals as $key => $days) {
			$uQuery = " (select `email`, `fullname`, `interval`, `count` from `#__komento_subscription` where `published` = '1' and `interval` = '$key' and `sent_out` <= date_sub('$now', INTERVAL $days DAY))";

			$unions[] = $uQuery;
		}
		$query .= implode(" union ", $unions);
		$query .= ") as x limit $limit";

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to get user's subscriptions based on the user's email
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestEmailSubscriptions($now, $email, $limit = 10)
	{
		$db = KT::db();

		$intervals = ['daily' => 1,
						'weekly' => 7,
						'monthly' => 30];

		$unions = array();

		$query = "";
		foreach($intervals as $key => $days) {
			$uQuery = "(select a.* from `#__komento_subscription` as a";
			$uQuery .= " where a.`published` = '1' and a.`interval` = '$key' and a.`email` = " . $db->Quote($email) . " and a.`sent_out` <= date_sub('$now', INTERVAL $days DAY)";
			$uQuery .= " LIMIT $limit)";

			$unions[] = $uQuery;
		}
		$query .= implode(" union ", $unions);

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Method to get comments that has yet send to subscribers
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestComments($subs, $now)
	{
		$db = KT::db();

		$unions = [];

		foreach ($subs as $sub) {

			$q = [];

			$q[] = "(select a.`id`, a.`component`, a.`cid`, a.`comment`, a.`name`, a.`created`";
			$q[] = "from `#__komento_comments` as a";
			$q[] = "where a.`component` = " . $db->Quote($sub->component);
			$q[] = "and a.`cid` = " . $db->Quote($sub->cid);
			$q[] = "and a.`published` = " . $db->Quote(1);
			$q[] = "and a.`created` >= " . $db->Quote($sub->sent_out) . " and a.created <= " . $db->Quote($now);
			$q[] = "order by a.`lft`";
			$q[] = "LIMIT " . $sub->count . ")";

			$sQuery = implode(" ", $q);

			$unions[] = $sQuery;
		}

		$query = implode(" UNION ", $unions);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Method to update subscribers sent_out datetime
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateDigestSentOut($subs)
	{
		if (! $subs) {
			// do nothing
			return true;
		}

		$db = KT::db();

		$now = FH::date()->toSql();

		$ids = array();
		foreach($subs as $sub) {
			$ids[] = $sub->id;
		}

		$query = "update `#__komento_subscription` set `sent_out` = " . $db->Quote($now);
		$query .= " where `id` IN (" . implode(',', $ids) . ")";

		$db->setQuery($query);
		$db->query();

		return true;
	}

}
