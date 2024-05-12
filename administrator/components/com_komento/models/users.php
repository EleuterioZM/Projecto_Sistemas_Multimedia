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

class KomentoModelUsers extends KomentoModel
{
	protected $element = 'users';
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct($config = [])
	{
		parent::__construct($config);
		$app = JFactory::getApplication();
		
		$limit = $this->app->getUserStateFromRequest('com_komento.users.limit', 'limit', $app->getCfg('list_limit'));
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves a list of registered user from Joomla
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function getUsers($options = array())
	{
		if (empty($this->_data)) {
			$query = $this->buildQuery($options);
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to build the query for the users
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function buildQuery($options = [])
	{
		$db = KT::db();

		$filter_order = $this->app->getUserStateFromRequest('com_komento.users.filter_order', 'filter_order', 'u.`id`', 'cmd');
		$filter_order_Dir = $this->app->getUserStateFromRequest('com_komento.users.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$search = $this->app->getUserStateFromRequest('com_komento.users.search', 'search', '', 'string');

		// if there is search passed in, use that
		$search = isset($options['search']) ? $options['search'] : $search;
		$search = trim(FCJString::strtolower($search));
		$search = $db->escape($search);

		$query = "SELECT u.`id`, u.`name`, u.`username`, u.`email`, u.`registerDate`, u.`lastvisitDate` FROM " . $db->nameQuote('#__users') . " AS u WHERE 1";

		if ($search) {
			
			if (stripos($search, 'id:') === 0) {

				$query .= ' AND u.`id` = ' . (int) substr($search, 3);
			
			} else {
			
				$query .= ' AND ((LOWER(u.`name`) LIKE ' . $db->Quote('%' . $search . '%') . ') OR (LOWER(u.`username`) LIKE ' . $db->Quote('%' . $search . '%') . '))';
			}
		}

		$exclusion = isset($options['exclusion']) ? $options['exclusion'] : [];

		if ($exclusion) {

			$exclusionIds = array();

			foreach ($exclusion as $exclusionId) {
				$exclusionIds[] = $db->Quote($exclusionId);
			}

			$exclusionIds = implode(',', $exclusionIds);

			$query .= ' AND u.`id` NOT IN (' . $exclusionIds . ')';
		}

		$query .= " ORDER BY " . $filter_order . ' ' . $filter_order_Dir;

		return $query;
	}

	public function getPagination()
	{
		if (empty($this->_pagination)) {
			$this->_pagination = KT::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		if (empty($this->_total)) {
	
			$query = $this->buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Retrieves a list of users for mention
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getMentionUsers($options = array())
	{
		if (empty($this->_data)) {
			$query = $this->buildQuery($options);
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Retrieves the user's id
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUserId($key , $value)
	{
		$db = KT::db();
		$sql = $db->sql();

		$sql->select('#__users');
		$sql->column('id');
		$sql->where($key, $value);

		$db->setQuery($sql);

		$id = $db->loadResult();
		return $id;
	}

	/**
	 * Determines if a user exceeded their moderation threshold so they won't be moderated again.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function exceededModerationThreshold($userId, $limit)
	{
		$db = $this->db;

		$query = array();
		$query[] = 'SELECT COUNT(1) as `CNT` FROM `#__komento_comments` AS a';
		$query[] = ' WHERE a.`created_by` = ' . $db->Quote($userId);
		$query[] = ' AND a.`published` = ' . $db->Quote('1');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result >= $limit) {
			return true;
		}

		return false;
	}
}
