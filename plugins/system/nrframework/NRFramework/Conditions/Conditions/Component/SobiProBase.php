<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class SobiProBase extends ComponentBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'entry';

    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_sobipro';

    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options, $factory)
	{
        parent::__construct($options, $factory);
        $this->request->view = 'entry';

		// Make sure SPRequest is loaded
		if (!class_exists('SPRequest'))
		{
			return;
        }
        
        $this->request->id = (int) \SPRequest::sid();
    }
    
    /**
     *  Indicates whether the page is a single page
     *
     *  @return  boolean
     */
    public function isSinglePage()
    {
        return (parent::isSinglePage() && $this->request->task == 'entry.details');
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
            ->select('pid')
            ->from('#__sobipro_relations')
            ->where($db->quoteName('id') . '=' . $db->q($id))
            ->where($db->quoteName('oType') . " = 'entry'");

        $db->setQuery($query);

		return $db->loadColumn();
	}
}