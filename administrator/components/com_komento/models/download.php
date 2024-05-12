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

class KomentoModelDownload extends KomentoModel
{
	protected $element = 'download';
	protected $_total = null;
	protected $_pagination = null;
	protected $_data = null;

	public function __construct($config = [])
	{
		parent::__construct($config);
		$app = JFactory::getApplication();
		
		$limit = $this->app->getUserStateFromRequest('com_komento.download.limit', 'limit', $app->getCfg('list_limit'));
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Retrieves download requests
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getRequests($options = array())
	{
		$db = KT::db();
		$query = array();

		$query[] = 'SELECT * FROM ' . $db->qn('#__komento_download');

		// Set the total number of items.
		$query = implode(' ', $query);

		$countQuery = str_replace('SELECT * FROM', 'SELECT COUNT(1) FROM', $query);
		$db->setQuery($countQuery);
		$this->_total = $db->loadResult();

		// Get the list of users
		$db->setQuery($query);
		$this->_data = $db->loadObjectList();
		$rows = $this->_data;

		if (!$rows) {
			return array();
		}

		$requests = array();

		foreach ($rows as $row) {
			$request = KT::table('Download');
			$request->bind($row);

			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @since  3.1
	 * @access public
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @since  3.1
	 * @access public
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = KT::pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 3.1
	 * @access public
	 */
	public function getCronDownloadReq($max = 1)
	{
		$db = KT::db();

		$query = "select * from `#__komento_download`";
		$query .= " where `state` IN (" . $db->Quote(KOMENTO_DOWNLOAD_REQ_NEW) . ',' . $db->Quote(KOMENTO_DOWNLOAD_REQ_PROCESS) . ")";
		$query .= " order by `id`";
		$query .= " limit 0," . $max;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to retrieve records pending for cron processing.
	 *
	 * @since 3.1
	 * @access public
	 */
	public function getExpiredRequest($max = 10)
	{
		$db = KT::db();
		$config = KT::config();

		$days = $config->get('gdpr_archive_expiry', 14);
		$now = FH::date()->toSql();

		$query = "select a.* from `#__komento_download` as a";
		$query .= " where a.`state` = " . $db->Quote(KOMENTO_DOWNLOAD_REQ_READY);
		$query .= " and a.`created` <= DATE_SUB(" . $db->Quote($now) . ", INTERVAL " . $days . " DAY)";
		$query .= " order by `id`";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Removes all download requests and delete the files
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function purgeRequests()
	{
		$db = KT::db();
		$query = 'DELETE FROM ' . $db->qn('#__komento_download');

		$db->setQuery($query);
		$db->query();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$folders = JFolder::folders(KOMENTO_GDPR_DOWNLOADS, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		$files = JFolder::files(KOMENTO_GDPR_DOWNLOADS, '.', false, true);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return true;
	}
}
