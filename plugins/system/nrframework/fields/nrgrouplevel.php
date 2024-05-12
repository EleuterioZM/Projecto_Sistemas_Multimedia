<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRGroupLevel extends JFormFieldNRTreeSelect
{
	/**
	 * A helper to get the list of user groups.
	 * Logic from administrator\components\com_config\model\field\filters.php@getUserGroups
	 * 
	 * @return	object
	 */
	protected function getOptions()
	{
		// Get a database object.
		$db = $this->db;

		// Get the user groups from the database.
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups AS a')
			->join('LEFT', '#__usergroups AS b on a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft')
			->order('a.lft ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}