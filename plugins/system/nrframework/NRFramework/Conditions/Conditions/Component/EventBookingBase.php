<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class EventBookingBase extends ComponentBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'event';

    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_eventbooking';

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
            ->from('#__eb_event_categories')
            ->where($db->quoteName('event_id') . '=' . $db->q($id));

        $db->setQuery($query);

		return $db->loadColumn();
	}
}