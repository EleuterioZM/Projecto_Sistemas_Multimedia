<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNREShopCategories extends JFormFieldNRTreeSelect
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
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('a.id as value, b.category_name as text, a.category_parent_id as parent, IF (a.published=1, 0, 1) as disable')
			->from('#__eshop_categories as a')
			->join('LEFT', '#__eshop_categorydetails AS b on a.id=b.category_id');

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
