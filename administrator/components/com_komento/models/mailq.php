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

class KomentoModelMailq extends KomentoModel
{
	protected $element = 'mailq';
	private $_pagination = '';
	private $_total = '';
	private $items = [];

	/**
	 * When needed, we can initialize the states for the model
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function initStates()
	{
		$app = JFactory::getApplication();

		// Set filter state
		$filter_publish = $app->getUserStateFromRequest('com_komento.mailq.filter_publish', 'filter_publish', 'pending', 'string');
		$this->setState('filter_publish', $filter_publish);
		
		// Set search state
		$search = $app->getUserStateFromRequest('com_komento.mailq.search', 'search', '', 'string');
		$this->setState('search', $search);

		// Set limit
		$limit = $app->getUserStateFromRequest('com_komento_mailq_limit', 'limit', 20, 'int');
		$this->setState('limit', $limit);

		// Set limit start
		$limitstart = $app->getUserStateFromRequest('com_komento_mailq_limitstart', 'limitstart', 0, 'int');
		$this->setState('limitstart', $limitstart);

		// ordering
		$ordering = $app->getUserStateFromRequest('com.komento.mailq.filter_order', 'filter_order', 'created', 'string');
		$this->setState('ordering', $ordering);

		// ordering direction
		$orderDirection = $app->getUserStateFromRequest('com.komento.mail.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$this->setState('direction', $orderDirection);
	}

	/**
	 * Build query
	 *
	 * @since	3.0
	 * @access	public
	 */
	private function buildQuery()
	{
		$db = KT::db();
		$query = 'SELECT * FROM ' . $db->nameQuote('#__komento_mailq');
		$query .= ' ORDER BY ' . $db->nameQuote('created') . ' DESC';

		return $query;
	}
	
	/**
	 * Calculate the number of pending e-mails that needs to be sent
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getTotalPending()
	{
		$db = KT::db();
		$query = [
			'SELECT COUNT(1) FROM `#__komento_mailq` WHERE `status`=' . $db->Quote(0)
		];

		$db->setQuery($query);
		$total = (int) $db->loadResult();

		return $total;
	}

	/**
	 * Get mailq item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getItems()
	{
		if (empty($this->items)) {
			$query = $this->buildQuery();
			$this->items = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->items;
	}

	public function getItemsWithState($options = array())
	{
		$db = KT::db();

		$sql = $db->sql();

		$sql->select('#__komento_mailq');

		// Determines if user is filtering the items
		$state = $this->getState('filter_publish');

		if ($state != 'all' && !is_null($state)) {
			if ($state === 'sent') {
				$state = 1;
			}

			if ($state === 'pending') {
				$state = 0;
			}

			if ($state === 'sending') {
				$state = 2;
			}

			$sql->where('status', $state);
		}

		// Determines if user is searching for a mail
		$search = $this->getState('search');

		if ($search) {
			$sql->where('(');
			$sql->where('subject' , '%' . $search . '%' , 'LIKE' , 'OR');
			$sql->where('recipient' , '%' . $search . '%' , 'LIKE' , 'OR');
			$sql->where(')');
		}

		// Ordering
		$ordering = $this->getState('ordering');

		if ($ordering) {
			$direction = $this->getState('direction') ? $this->getState('direction') : 'DESC';

			$sql->order($ordering , $direction);
		}

		// Set the total
		$this->setTotal($sql->getTotalSql());

		$result = parent::getData($sql->getSql());

		if ($result) {
			return $result;
		}

		return false;
	}

	/**
	 * Method to purge the emails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purge($type)
	{
		$db = KT::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__komento_mailq');

		switch ($type) {
			case 'pending':
				$query .= ' WHERE ' . $db->nameQuote('status') . '= ' . $db->quote(0);
				break;
			case 'sent':
				$query .= ' WHERE ' . $db->nameQuote('status') . '= ' . $db->quote(1);
			case 'all':
			default:
			break;
		}
		
		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Method to purge all emails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purgeAll()
	{
		return $this->purge('all');
	}

	/**
	 * Method to purge sent emails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purgeSent()
	{
		return $this->purge('sent');
	}

	/**
	 * Method to purge pending emails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purgePending()
	{
		return $this->purge('pending');
	}

	/**
	 * Get pagination
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)){
			$this->_pagination = KT::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}
}