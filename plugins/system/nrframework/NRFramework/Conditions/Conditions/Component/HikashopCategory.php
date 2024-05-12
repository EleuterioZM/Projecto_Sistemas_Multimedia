<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class HikashopCategory extends HikashopBase
{
    /**
     *  Pass check
     *
     *  @return bool
     */
    public function pass()
    {
        return $this->passCategories('hikashop_category', 'category_parent_id');
	}

	/**
	 *  Returns all parent rows
	 *
	 *  @param   integer  $id      Row primary key
	 *  @param   string   $table   Table name
	 *  @param   string   $parent  Parent column name
	 *  @param   string   $child   Child column name
	 *
	 *  @return  array             Array with IDs
	 */
	public function getParentIds($id = 0, $table = 'hikashop_category', $parent = 'category_parent_id', $child = 'category_id')
	{
		return parent::getParentIds($id, $table, $parent, $child);
	}
}