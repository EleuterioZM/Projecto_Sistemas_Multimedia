<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class K2Base extends ComponentBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'item';

    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_k2';

    /**
     * Get single page's assosiated categories
     *
     * @param   integer  The Single Page id
	 * 
     * @return  integer
     */
	protected function getSinglePageCategories($id)
	{
        $item = $this->getK2Item();

        return isset($item->catid) ? $item->catid : null;
	}

    /**
     *  Indicates whether the current view concerns a Category view
     *
     *  @return  boolean
     */
    protected function isCategoryPage()
    {
        return ($this->request->layout == 'category' || $this->request->task == 'category' || $this->request->view == 'latest');
    }

    /**
     *  Returns a K2 item
     *
     *  @return object|null
     */
    public function getK2Item()
    {
        $cache = $this->factory->getcache();
        $hash  = md5('k2assitem');

        if ($cache->has($hash))
        {
            return $cache->get($hash);
        }

        return $cache->set($hash, \JModelLegacy::getInstance('Item', 'K2Model')->getData());
    }  
    
    /**
     *  Return tags of a K2 item
     * 
     *  @param   int $id K2 item ID
     * 
     *  @return  array
     */
    public function getK2tags($id = null)
    {
        $id = is_null($id) ? $this->request->id : $id;

        if (!$id)
        {
            return [];
        }

        $cache = $this->factory->getcache();
        $hash  = md5('k2_item_tags' . $id);

        if ($cache->has($hash))
        {
            return $cache->get($hash);
        }

        $q = $this->db->getQuery(true)
            ->select('t.id')
            ->from('#__k2_tags_xref AS tx')
            ->join('LEFT', '#__k2_tags AS t ON t.id = tx.tagID')
            ->where('tx.itemID = ' . $this->db->q($id))
            ->where('t.published = 1');

        $this->db->setQuery($q);
        
        return $cache->set($hash, $this->db->loadColumn());
    }

    /**
     * Get current view layout string
     *
     * @return string
     */
    public function getPageType()
    {
        $view   = $this->request->view;
        $layout = $this->request->layout;

        if (is_null($layout))
        {
            switch ($view)
            {
                case 'item':
                    $layout = 'item';
                    break;
                default:
                    $layout = $this->request->task;
                    break;              
            }
        }

        $pagetype = $view . '_' . $layout;

        return $pagetype;
    }
}