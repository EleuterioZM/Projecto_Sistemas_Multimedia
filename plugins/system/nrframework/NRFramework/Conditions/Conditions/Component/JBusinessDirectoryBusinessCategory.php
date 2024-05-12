<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class JBusinessDirectoryBusinessCategory extends JBusinessDirectoryBusinessBase
{
    /**
     *  Pass check
     *
     *  @return bool
     */
    public function pass()
    {
        return $this->passCategories('jbusinessdirectory_categories', 'parent_id');
	}
}