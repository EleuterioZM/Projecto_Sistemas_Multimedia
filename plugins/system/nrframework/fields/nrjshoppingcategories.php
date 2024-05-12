<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRJShoppingCategories extends JFormFieldNRTreeSelect
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
			->select($db->quoteName('name_' . $this->getLanguage(), 'text'))
			->select('category_id as value, category_parent_id as parent, IF (category_publish=1, 0, 1) as disable')
			->from('#__jshopping_categories');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
     *  JoomShopping is using different columns per language. Therefore, we need to use their API to get the default language code.
     *
     *  @return  string
     */
    private function getLanguage($default = 'en-GB')
    {	
		// Silent inclusion.
        @include_once JPATH_SITE . '/components/com_jshopping/lib/factory.php';

        if (!class_exists('JSFactory'))
        {
            return $default;
        }

		return JSFactory::getConfig()->defaultLanguage;
    }
}
