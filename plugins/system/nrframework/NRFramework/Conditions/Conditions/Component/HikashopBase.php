<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class HikaShopBase extends ComponentBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'product';

    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_hikashop';

    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options, $factory)
	{
		parent::__construct($options, $factory);
        $this->request->id = $this->app->input->get('cid', $this->app->input->getInt('product_id'));
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
            ->from('#__hikashop_product_category')
            ->where($db->quoteName('product_id') . '=' . $db->q($id));

		$db->setQuery($query);
		
		return $db->loadColumn();
	}
}