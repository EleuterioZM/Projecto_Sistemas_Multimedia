<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Component;

defined('_JEXEC') or die;

class EasyBlogCategory extends EasyBlogBase
{
    /**
     *  Pass check
     *
     *  @return bool
     */
    public function pass()
    {
        return $this->passCategories('easyblog_category', 'parent_id');
	}

}