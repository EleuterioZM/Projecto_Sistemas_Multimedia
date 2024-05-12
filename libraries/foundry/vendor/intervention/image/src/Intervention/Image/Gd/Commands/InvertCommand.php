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

namespace Intervention\Image\Gd\Commands;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\Commands\AbstractCommand;

class InvertCommand extends AbstractCommand
{
    /**
     * Inverts colors of an image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        return imagefilter($image->getCore(), IMG_FILTER_NEGATE);
    }
}
