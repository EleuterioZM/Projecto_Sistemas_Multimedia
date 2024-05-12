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

namespace Intervention\Image\Imagick\Commands;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\Commands\AbstractCommand;
use Intervention\Image\Imagick\Color;

class PixelCommand extends AbstractCommand
{
    /**
     * Draws one pixel to a given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $color = $this->argument(0)->required()->value();
        $color = new Color($color);
        $x = $this->argument(1)->type('digit')->required()->value();
        $y = $this->argument(2)->type('digit')->required()->value();

        // prepare pixel
        $draw = new \ImagickDraw;
        $draw->setFillColor($color->getPixel());
        $draw->point($x, $y);

        // apply pixel
        return $image->getCore()->drawImage($draw);
    }
}
