<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Joomla;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class Menu extends Condition 
{
	protected $itemID = null;

	public function __construct($options, $factory)
	{
		parent::__construct($options, $factory);

		$this->itemID = $this->app->input->getInt('Itemid', 0);
	}
	
	/**
	 *  Pass check for menu items
	 *
	 *  @return  bool
	 */
	public function pass()
	{
		$includeChildren = $this->params->get('inc_children', false);
    	$includeNoItemID = $this->params->get('noitem', false);
		
    	// Pass if selection is empty or the itemid is missing
    	if (!$this->itemID || empty($this->selection))
        {
        	return $includeNoItemID;
        }

        // return true if menu type is in selection
		$menutype = 'type.' . $this->getMenuType();
		if ($includeChildren && in_array($menutype, $this->selection))
		{
			return true;
		}

		// return true if menu is in selection and we are not including child items only
		if (in_array($this->itemID, $this->selection))
		{
			return ($includeChildren != 2);
		}

		// Let's discover child items. 
		// Obviously if the option is disabled return false.
		if (!$includeChildren)
		{
			return false;
		}

		// Get menu item parents
		$parent_ids = $this->getParentIds($this->itemID);
		$parent_ids = array_diff($parent_ids, array('1'));

		foreach ($parent_ids as $id)
		{
			if (!in_array($id, $this->selection))
			{
				continue;
			}

			return true;
		}

		return false;
	}

	/**
     *  Returns the assignment's value
     * 
     *  @return integer Menu ID
     */
	public function value()
	{
		return $this->itemID;
	}

	/**
	 *  Get active menu items's menu type
	 *
	 *  @return  bool   False on failure, string on success
	 */
	private function getMenuType()
	{
		if (empty($this->itemID))
		{
			return;
		}

		$menu = $this->app->getMenu()->getItem((int) $this->itemID);

		return isset($menu->menutype) ? $menu->menutype : false;
	}
}