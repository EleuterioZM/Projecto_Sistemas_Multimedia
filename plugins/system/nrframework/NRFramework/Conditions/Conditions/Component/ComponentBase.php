<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;
use NRFramework\Functions;

/**
 * Base class used by component-based assignments. Class properties defaults to com_content.
 */
abstract class ComponentBase extends Condition
{
	/**
	 * The component's Category Page view name
	 *
	 * @var string
	 */
    protected $viewCategory = 'category';
    
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'article';
    
    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_content';

	/**
	 * Request information
	 *
	 * @var mixed
	 */
    protected $request = null;

    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options = null, $factory = null)
	{
		parent::__construct($options, $factory);
        
        $request = new \stdClass;

        $request->view   = $this->app->input->get('view');
        $request->task   = $this->app->input->get('task');
        $request->option = $this->app->input->get('option');
        $request->layout = $this->app->input->get('layout');
        $request->id     = $this->app->input->getInt('id');

        $this->request = $request;
	}

	/**
     *  Returns the assignment's value
     * 
     *  @return array Category IDs
     */
	public function value()
	{
		return $this->getCategoryIds();
	}

    /**
     *  Indicates whether the current view concerns a Category view
     *
     *  @return  boolean
     */
    protected function isCategoryPage()
    {
        return ($this->request->view == $this->viewCategory);
    }

    /**
     *  Indicates whether the current view concerncs a Single Page view
     *
     *  @return  boolean
     */
    public function isSinglePage()
    {
        return ($this->request->view == $this->viewSingle);
    }

    /**
     *  Check if we are in the right context and we're manipulating the correct component
     *
     *  @return bool
     */
    protected function passContext()
    {
        return ($this->request->option == $this->component_option);
    }

    /**
	 *  Returns category IDs based
	 *
	 *  @return  array
	 */
	protected function getCategoryIDs()
	{
		$id = $this->request->id;

		// Make sure we have an ID.
		if (empty($id))
		{
			return;
		}

		// If this is a Category page, return the Category ID from the Query String
		if ($this->isCategoryPage())
		{
			return (array) $id;
		}

		// If this is a Single Page, return all assosiated Category IDs.
		if ($this->isSinglePage())
		{
			return $this->getSinglePageCategories($id);
		}
	}

    /**
	 * Checks whether the current page is within the selected categories
	 *
	 * @param	string	   $ref_table				The referenced table
	 * @param	string	   $ref_parent_column		The name of the parent column in the referenced table
	 * 
	 * @return	boolean
	 */
    protected function passCategories($ref_table = 'categories', $ref_parent_column = 'parent_id')
    {
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
		}

		// Include Children switch: 0 = No, 1 = Yes, 2 = Child Only
		$inc_children   = $this->params->get('inc_children');

		// Setup supported views
		$view_single   = $this->params->get('view_single', true);
		$view_category = $this->params->get('view_category', false);

		// Check if we are in a valid context
		if (!($view_category && $this->isCategoryPage()) && !($view_single && $this->isSinglePage()))
		{
			return false;
		}

		// Start Checks
		$pass = false;

		// Get current page assosiated category IDs. It can be a single ID of the current Category view or multiple IDs assosiated to active item.
		$catids = $this->getCategoryIds();
		$catids = is_array($catids) ? $catids : (array) $catids;

		foreach ($catids as $catid)
		{
			$pass = in_array($catid, $this->selection);

			if ($pass)
			{
				// If inc_children is either disabled or set to 'Also on Childs', there's no need for further checks. 
				// The condition is already passed.
				if (in_array($this->params->get('inc_children'), [0, 1]))
				{
					break;
				}

				// We are here because we need childs only. Disable pass and continue checking parent IDs.
				$pass = false;
			}

			// Pass check for child items
			if (!$pass && $this->params->get('inc_children'))
			{
				$parent_ids = $this->getParentIDs($catid, $ref_table, $ref_parent_column);

				foreach ($parent_ids as $id)
				{
					if (in_array($id, $this->selection))
					{
						$pass = true;
						break 2;
					}
				}

				unset($parent_ids);
			}
		}

		return $pass;
	}

	/**
	 * Check whether this page passes the validation
	 *
	 * @return void
	 */
	protected function passSinglePage()
	{
		// Make sure we are in the right context
        if (empty($this->selection) || !$this->passContext() || !$this->isSinglePage())
        {
            return false;
		}

        if (!is_array($this->selection))
        {
            $this->selection = Functions::makeArray($this->selection);
		}
		
		return parent::pass();
	}

    /**
     * Get single page's assosiated categories
     *
     * @param   Integer  The Single Page id
     * @return  array
     */
    abstract protected function getSinglePageCategories($id);
}