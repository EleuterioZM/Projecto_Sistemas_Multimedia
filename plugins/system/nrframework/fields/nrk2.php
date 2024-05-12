<?php
/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/groupfield.php';

class JFormFieldNRK2 extends NRFormGroupField
{
    public $type = 'K2';

    /**
     *  Pagetypes options
     *
     *  @var  array
     */
    public $pagetype_options = array(
        'itemlist_category' => 'NR_ASSIGN_K2_CATEGORY_OPTION',
        'item_item'         => 'NR_ASSIGN_K2_ITEM_OPTION',
        'item_itemform'     => 'NR_ASSIGN_K2_ITEM_FORM_OPTION',
        'latest_latest'     => 'NR_ASSIGN_K2_LATEST_OPTION',
        'itemlist_tag'      => 'NR_ASSIGN_K2_TAG_OPTION',
        'itemlist_user'     => 'NR_ASSIGN_K2_USER_PAGE_OPTION'
    );

    
    public function getItems()
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('i.id, i.title as name, i.language, c.name as cat, i.published')
            ->from('#__k2_items as i')
            ->join('LEFT', '#__k2_categories AS c ON c.id = i.catid')
            ->order('i.title, i.ordering, i.id');
        $this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('language', 'cat', 'id'));
    }

    public function getPagetypes()
    {
        asort($this->pagetype_options);

		foreach ($this->pagetype_options as $key => $option)
		{
			$options[] = JHTML::_('select.option', $key, JText::_($option));
		}

		return $options;
    }

    public function getTags()
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('t.id, t.name')
            ->from('#__k2_tags as t')
            ->order('t.id');
        $this->db->setQuery($query);
        $list = $this->db->loadObjectList();

        return $this->getOptionsByList($list);        
    }

    public function getCategories()
    {
        $query = $this->db->getQuery(true);
        $query
            ->select('c.id, c.name, c.parent, c.published, c.language')
            ->from('#__k2_categories as c')
            ->where('c.trash = 0')
            ->where('c.id != c.parent')
            ->order('c.ordering');
        $this->db->setQuery($query);
        $cats = $this->db->loadObjectList();

        $options = [];
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
            $options[] = $c;
        }

        // sort options
        $options = $this->sortTreeSelectOptions($options);        
        return $this->getOptionsByList($options, array('language'));
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
            $result = array_merge($result, $this->sortTreeSelectOptions($options, $option->id));
        }

        return $result;
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
            if ((int)$c->id === $current_pid)
            {
                return (int)$c->parent;
            }
        }
    }
}
