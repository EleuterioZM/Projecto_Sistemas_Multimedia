<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

use \NRFramework\HTML;

defined('_JEXEC') or die;

abstract class JFormFieldNRTreeSelect extends JFormField
{
	/**
	 * Database object
	 *
	 * @var object
	 */
	public $db;

	/**
	 * Indicates whether the options array should be sorted before render.
	 *
	 * @var boolean
	 */
	protected $sortTree = false;
	
	/**
	 * Indicates whether the options array should have the levels re-calculated
	 * 
	 * @var boolean
	 */
    protected $fixLevels = false;
    
    /**
     * Increase the value(ID) of the category by one.
	 * This happens because we have a parent category "Bussiness Directory" that pushes-in the indentation
	 * and we reset it by decreasing the value, level and parent.
     * 
     * @var boolean
     */
    protected $increaseValue = false;

	/**
	 * Output the HTML for the field
	 */
	protected function getInput()
	{
		$this->db = JFactory::getDbo();

		$options = $this->getOptions();

		if ($this->sortTree)
		{
			$options = $this->sortTreeSelectOptions($options);
		}

        if ($this->fixLevels)
        {
			$options = $this->fixLevels($options);
        }
        
        if ($this->increaseValue)
        {
            // Increase by 1 the value(ID) of the category
            foreach ($options as $key => $value)
            {
                $options[$key]->value+=1;
            }
        }

		return HTML::treeselect($options, $this->name, $this->value, $this->id);
	}

	/**
     *  Sorts treeselect options
     * 
     *  @param  array $options
     *  @param  int   $parent_id
     * 
     *  @return array
     */
    protected function sortTreeSelectOptions($options, $parent_id = 0)
    {
        if (empty($options))
        {
            return [];
        }

        $result = [];

        $sub_options = array_filter($options, function($option) use($parent_id)
        {
            return $option->parent == $parent_id;
        });

        foreach ($sub_options as $option)
        {
            $result[] = $option;
            $result = array_merge($result, $this->sortTreeSelectOptions($options, $option->value));
        }

        return $result;
	}
	
	/**
     *  Fixes the levels of the categories
     * 
     *  @param  array $categories
     * 
     *  @return array
     */
    protected function fixLevels($cats)
    {
		// new categories
		$categories = [];
		
        // get category levels
        foreach ($cats as $c)
        {
            $level = 0;
            $parent_id = (int)$c->parent;

            while ($parent_id)
            {
                $level++;
                $parent_id = $this->getNextParentId($cats, $parent_id);
			}
			
            $c->level = $level;
            $categories[] = $c;
		}
		
		return $categories;
	}

    /**
     *  Returns the next parent id
     *  Helper method for getCategories
     * 
     *  @return int
     */
    protected function getNextParentId($categories, $current_pid)
    {
        foreach($categories as $c)
        {
            if ((int)$c->value === $current_pid)
            {
                return (int)$c->parent;
            }
        }
    }

	/**
	 * Get tree options as an Array of objects
	 * Each object should have the attributes: value, text, parent, level, disable
	 *
	 * @return object
	 */
	abstract protected function getOptions();
}