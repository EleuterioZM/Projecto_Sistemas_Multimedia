<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class AcyMailing extends Condition
{
	/**
	 *  Returns the assignment's value
	 * 
	 *  @return  array  AcyMailing lists
	 */
	public function value()
	{
		return $this->getSubscribedLists();
	}

	/**
	 *  Returns all AcyMailing lists the user is subscribed to
	 *
	 *  @return  array  AcyMailing lists
	 */
	private function getSubscribedLists()
	{
		if (!$user = $this->user->id)
		{
			return false;
		}

		// Get a db connection.
		$db = $this->db;
		 
		// Create a new query object.
		$query = $db->getQuery(true);

		$lists = [];

		// Read AcyMailing v5 lists
		if (\NRFramework\Extension::isInstalled('com_acymailing'))
		{
			$query
				->select(array('list.listid'))
				->from($db->quoteName('#__acymailing_listsub', 'list'))
				->join('INNER', $db->quoteName('#__acymailing_subscriber', 'sub') . ' ON (' . $db->quoteName('list.subid') . '=' . $db->quoteName('sub.subid') . ')')
				->where($db->quoteName('list.status') . ' = 1')
				->where($db->quoteName('sub.userid') . ' = ' . $user)
				->where($db->quoteName('sub.confirmed') . ' = 1')
				->where($db->quoteName('sub.enabled') . ' = 1');
		 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			if ($cols = $db->loadColumn())
			{
				$lists = array_merge($lists, $cols);
			}
		}
		
		// Read AcyMailing > v5 lists
		if (\NRFramework\Extension::isInstalled('com_acym'))
		{
			// Create a new query object.
			$query = $db->getQuery(true);

			$query
				->select(['list.id'])
				->from($db->quoteName('#__acym_user_has_list', 'userlist'))
				->join('INNER', $db->quoteName('#__acym_list', 'list') . ' ON (' . $db->quoteName('list.id') . '=' . $db->quoteName('userlist.list_id') . ')')
				->join('INNER', $db->quoteName('#__acym_user', 'user') . ' ON (' . $db->quoteName('user.id') . '=' . $db->quoteName('userlist.user_id') . ')')
				->where($db->quoteName('user.cms_id') . ' = ' . $user)
				->where($db->quoteName('userlist.status') . ' = 1')
				->where($db->quoteName('userlist.unsubscribe_date') . ' IS NULL');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			$cols = $db->loadColumn();

			foreach ($cols as $value)
			{
				$lists[] = '6:' . $value;
			}
		}

		return $lists;
	}
}
