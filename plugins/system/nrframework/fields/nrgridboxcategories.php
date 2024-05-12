<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRGridboxCategories extends JFormFieldNRTreeSelect
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
	 * Get a list of all Gridbox Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
		->select('id as value, title as text, parent as parent, IF (published=1, 0, 1) as disable')
		->from('#__gridbox_categories');
			
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
