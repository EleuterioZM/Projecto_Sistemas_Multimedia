<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class JBusinessDirectoryBusinessBase extends JBusinessDirectoryBase
{
    /**
     * Class Constructor
     *
     * @param object $options
     * @param object $factory
     */
    public function __construct($options = null, $factory = null)
	{
        parent::__construct($options, $factory);

        $this->request->id = (int) $this->app->input->getInt('companyId');
    }
}