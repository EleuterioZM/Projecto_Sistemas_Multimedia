<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRJBusinessDirectoryCategories extends JFormFieldNRTreeSelect
{
	/**
	 * Get a list of all J-BusinessDirectory Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
        $filter_type = isset($this->element['filter_type']) ? (string) $this->element['filter_type'] : 1;
		
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('a.id as value, a.name as text, a.level AS level, a.parent_id as parent, IF (a.published=1, 0, 1) as disable')
			->from('#__jbusinessdirectory_categories as a')
            ->join('LEFT', '#__jbusinessdirectory_categories AS b on a.lft > b.lft AND a.rgt < b.rgt')
            ->where($db->quoteName('a.type') . ' = ' . $db->q($filter_type))
			->group('a.id, a.name, a.lft')
			->order('a.lft ASC');
			
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
