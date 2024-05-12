<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRDJEventsCategories extends JFormFieldNRTreeSelect
{
	/**
	 * Get a list of all DJ Events Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('a.id as value, a.name as text, 0 AS level, 0 as parent, 0 as disable')
			->from('#__djev_cats as a')
			->group('a.id, a.name')
			->order('a.id ASC');
			
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
