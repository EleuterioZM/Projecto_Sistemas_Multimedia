<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRDJCatalog2Categories extends JFormFieldNRTreeSelect
{
	/**
	 * Indicates whether the options array should be sorted before render.
	 *
	 * @var boolean
	 */
	protected $sortTree = true;

	/**
	 * Indicates whether the options array should have the levels re-calculated
	 * 
	 * @var boolean
	 */
	protected $fixLevels = true;

	/**
	 * Get a list of all EventBooking Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('id as value, name as text, parent_id as parent, IF (published=1, 0, 1) as disable')
			->from('#__djc2_categories');

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
