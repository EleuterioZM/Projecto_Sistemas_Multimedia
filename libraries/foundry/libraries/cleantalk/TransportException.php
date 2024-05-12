<?php
/**
* @package      Foundry
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Cleantalk;

defined('_JEXEC') or die('Unauthorized Access');

use Exception;

class TransportException extends Exception
{
    /**
     * @param string $url
     * @return self
     */
    public static function fromUrlHostError($url_host)
    {
        return new self("Couldn't resolve host name for \"$url_host\".\nCheck your network connectivity.");
    }
}
