<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

class nrURLShortTinyURL extends NRURLShortener
{

    protected $needsKey   = false;
    protected $needsLogin = false;

    function baseURL()
    {
        return "http://tinyurl.com/api-create.php?url=".urlencode($this->url);
    }
	
}
