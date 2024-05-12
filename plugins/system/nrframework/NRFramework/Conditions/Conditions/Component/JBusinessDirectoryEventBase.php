<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class JBusinessDirectoryEventBase extends JBusinessDirectoryBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'event';

    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options = null, $factory = null)
	{
        parent::__construct($options, $factory);

        $this->request->id = (int) $this->app->input->getInt('eventId');
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
            ->select($db->quoteName('categoryId'))
            ->from('#__jbusinessdirectory_company_event_category')
            ->where($db->quoteName('eventId') . '=' . $db->q($id));

        $db->setQuery($query);

		return $db->loadColumn();
	}
}