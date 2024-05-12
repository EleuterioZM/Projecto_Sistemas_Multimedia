<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class J2StoreBase extends ComponentBase
{
    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_j2store';

    /**
     *  Indicates whether the page is a category page
     *
     *  @return  boolean
     */
    protected function isCategory()
    {
        return is_null($this->request->task);
    }

    /**
     *  Indicates whether the page is a single page
     *
     *  @return  boolean
     */
    public function isSinglePage()
    {
        return (in_array($this->request->view, ['products', 'producttags']) && $this->request->task == 'view');
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
		// Get product information
        require_once JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/product.php';

		// Make sure J2Store is loaded
		if (!class_exists('J2Product'))
		{
			return;
        }

		$item = \J2Product::getInstance()->setId($this->request->id)->getProduct();

		if (!is_object($item) || !isset($item->source))
		{
			return;
        }

        return $item->source->catid;
    }
}