<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class ZooBase extends ComponentBase
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
    protected $component_option = 'com_zoo';

    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options, $factory)
	{
        parent::__construct($options, $factory);

        $this->request->view = $this->app->input->get('view', $this->app->input->get('task'));

        // Normally the item's id can be read by the request parameters BUT if the item 
        // is assosiated to a menu item the item_id parameter is not yet available and 
        // we can only find it out through the menu's parameters.
        $this->request->id = (int) $this->app->input->getInt('item_id', $this->app->getMenu()->getActive()->getParams()->get('item_id'));
    }

    /**
     * Get single page's assosiated categories
     *
     * @param   Integer  The Single Page id
	 * 
     * @return  array
     */
	protected function getSinglePageCategories($id)
	{
        $db = $this->db;

        $query = $db->getQuery(true)
            ->select('category_id')
            ->from('#__zoo_category_item')
            ->where($db->quoteName('item_id') . '=' . $db->q($id));

        $db->setQuery($query);

		return $db->loadColumn();
	}
}