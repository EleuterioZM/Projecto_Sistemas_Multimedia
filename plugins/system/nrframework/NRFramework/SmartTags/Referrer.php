<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

class Referrer extends SmartTag
{
    /**
     * Returns the current Referrer
     * 
     * @return  string
     */
    public function getReferrer()
    {
        return $this->app->input->server->get('HTTP_REFERER', '', 'RAW');
    }
}