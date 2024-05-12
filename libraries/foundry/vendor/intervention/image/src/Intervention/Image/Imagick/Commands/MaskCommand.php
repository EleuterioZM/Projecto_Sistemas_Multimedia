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

class MaskCommand extends AbstractCommand
{
    /**
     * Applies an alpha mask to an image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $mask_source = $this->argument(0)->value();
        $mask_w_alpha = $this->argument(1)->type('bool')->value(false);

        // get imagick
        $imagick = $image->getCore();

        // build mask image from source
        $mask = $image->getDriver()->init($mask_source);

        // resize mask to size of current image (if necessary)
        $image_size = $image->getSize();
        if ($mask->getSize() != $image_size) {
            $mask->resize($image_size->width, $image_size->height);
        }

        $imagick->setImageMatte(true);

        if ($mask_w_alpha) {

            // just mask with alpha map
            $imagick->compositeImage($mask->getCore(), \Imagick::COMPOSITE_DSTIN, 0, 0);

        } else {

            // get alpha channel of original as greyscale image
            $original_alpha = clone $imagick;
            $original_alpha->separateImageChannel(\Imagick::CHANNEL_ALPHA);

            // use red channel from mask ask alpha
            $mask_alpha = clone $mask->getCore();
            $mask_alpha->compositeImage($mask->getCore(), \Imagick::COMPOSITE_DEFAULT, 0, 0);
            // $mask_alpha->setImageAlphaChannel(\Imagick::ALPHACHANNEL_DEACTIVATE);
            $mask_alpha->separateImageChannel(\Imagick::CHANNEL_ALL);

            // combine both alphas from original and mask
            $original_alpha->compositeImage($mask_alpha, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);

            // mask the image with the alpha combination
            $imagick->compositeImage($original_alpha, \Imagick::COMPOSITE_DSTIN, 0, 0);
        }

        return true;
    }
}
