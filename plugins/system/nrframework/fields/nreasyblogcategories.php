<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNREasyBlogCategories extends JFormFieldNRTreeSelect
{
	/**
	 * Get a list of all EventBooking Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('a.id as value, a.title as text, COUNT(DISTINCT b.id) AS level, a.parent_id as parent, IF (a.published=1, 0, 1) as disable')
			->from('#__easyblog_category as a')
			->join('LEFT', '#__easyblog_category AS b on a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft')
			->order('a.lft ASC');
			
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
