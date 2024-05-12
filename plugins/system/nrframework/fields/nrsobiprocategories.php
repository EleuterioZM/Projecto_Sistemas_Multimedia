<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRSobiProCategories extends JFormFieldNRTreeSelect
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
     * Increase the value(ID) of the category by one.
	 * This happens because we have a parent category "Bussiness Directory" that pushes-in the indentation
	 * and we reset it by decreasing the value, level and parent.
     * 
     * @var boolean
     */
	protected $increaseValue = true;

	/**
	 * Get a list of all SobiPro Categories
	 *
	 * @return void
	 */
	protected function getOptions()
	{
		// Get a database object.
        $db = $this->db;
        
		$query = $db->getQuery(true)
			->select('(a.id - 1) as value, b.sValue as text, (a.parent - 1) as level, (a.parent - 1) as parent, IF (a.state=1, 0, 1) as disable')
			->from('#__sobipro_object as a')
            ->join('LEFT', "#__sobipro_language AS b on a.id = b.id AND b.sKey = 'name'")
			->where($db->quoteName('a.oType') . ' = '. $db->quote('category'));
			
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}
}
