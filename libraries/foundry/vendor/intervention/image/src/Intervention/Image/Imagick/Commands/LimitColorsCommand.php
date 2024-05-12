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

class LimitColorsCommand extends AbstractCommand
{
    /**
     * Reduces colors of a given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $count = $this->argument(0)->value();
        $matte = $this->argument(1)->value();

        // get current image size
        $size = $image->getSize();

        // build 2 color alpha mask from original alpha
        $alpha = clone $image->getCore();
        $alpha->separateImageChannel(\Imagick::CHANNEL_ALPHA);
        $alpha->transparentPaintImage('#ffffff', 0, 0, false);
        $alpha->separateImageChannel(\Imagick::CHANNEL_ALPHA);
        $alpha->negateImage(false);

        if ($matte) {

            // get matte color
            $mattecolor = $image->getDriver()->parseColor($matte)->getPixel();

            // create matte image
            $canvas = new \Imagick;
            $canvas->newImage($size->width, $size->height, $mattecolor, 'png');

            // lower colors of original and copy to matte
            $image->getCore()->quantizeImage($count, \Imagick::COLORSPACE_RGB, 0, false, false);
            $canvas->compositeImage($image->getCore(), \Imagick::COMPOSITE_DEFAULT, 0, 0);

            // copy new alpha to canvas
            $canvas->compositeImage($alpha, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

            // replace core
            $image->setCore($canvas);

        } else {

            $image->getCore()->quantizeImage($count, \Imagick::COLORSPACE_RGB, 0, false, false);
            $image->getCore()->compositeImage($alpha, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

        }

        return true;

    }
}
