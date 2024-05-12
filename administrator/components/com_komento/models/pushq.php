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

class KomentoModelPushq extends KomentoModel
{
	protected $element = 'pushq';
	public $_total = null;
	public $_pagination	= null;
	public $_data = null;

	/**
	 * Add push notification into queue
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function add($component, $cid, $data)
	{
		$db = KT::db();
		$now = FH::date()->toSql();

		$query = "insert into `#__komento_pushq` (`component`, `cid`, `created`, `status`, `userid`, `data`)";
		$query .= " select " . $db->Quote($component) . ", " . $db->Quote($cid) . ", " . $db->Quote($now) . ", " . $db->Quote('0') . ", a.userid, " . $db->Quote(json_encode($data));
		$query .= "   from `#__komento_subscription` as a";
		$query .= "   where a.`component` = " . $db->Quote($component);
		$query .= "   and a.`cid` = " . $db->Quote($cid);
		$query .= "   and a.userid > 0";
		$query .= "   and a.published = " . $db->Quote(KOMENTO_STATE_PUBLISHED);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Retrieve list of items for push processsing as batch
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function getPending($max)
	{
		$db = KT::db();

		$query = "select * from `#__komento_pushq`";
		$query .= " where status = 0";
		$query .= " order by id";
		if ($max) {
			$query .= " LIMIT $max";
		}

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Method to mark the pending records as sent
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function markSent($ids)
	{
		$db = KT::db();

		$query = "update `#__komento_pushq` set `status` = " . $db->Quote(KOMENTO_STATE_PUBLISHED);
		$query .= " where id IN (" . implode(',', $ids) . ")";

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
