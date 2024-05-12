<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/treeselect.php';

class JFormFieldNRVirtueMartCategories extends JFormFieldNRTreeSelect
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
			->select('a.virtuemart_category_id as value, b.category_name as text, c.category_parent_id as parent, IF (a.published=1, 0, 1) as disable')
			->from('#__virtuemart_categories as a')
            ->join('LEFT', '#__virtuemart_categories_' . $this->getLanguage() . ' AS b on a.virtuemart_category_id = b.virtuemart_category_id')
			->join('LEFT', '#__virtuemart_category_categories AS c on a.virtuemart_category_id = c.id')
			->order('c.id desc');
		
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
     *  VirtueMart is using different tables per language. Therefore, we need to use their API to get the default language code
     *
     *  @return  string
     */
    private function getLanguage($default = 'en_gb')
    {	
		// Silent inclusion.
		@include_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php'; 

        if (!class_exists('VmConfig'))
		{
			return $default;
        }
            
        // Init configuration
		VmConfig::loadConfig();
		
        return VmConfig::$jDefLang;
    }
}
