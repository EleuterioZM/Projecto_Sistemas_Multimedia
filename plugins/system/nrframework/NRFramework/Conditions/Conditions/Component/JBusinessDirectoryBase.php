<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class JBusinessDirectoryBase extends ComponentBase
{
    /**
     * The component's Single Page view name
     *
     * @var string
     */
    protected $viewSingle = 'companies';

    /**
     * The component's option name
     *
     * @var string
     */
    protected $component_option = 'com_jbusinessdirectory';

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
            ->from('#__jbusinessdirectory_company_category')
            ->where($db->quoteName('companyId') . '=' . $db->q($id));

        $db->setQuery($query);

		return $db->loadColumn();
	}
}