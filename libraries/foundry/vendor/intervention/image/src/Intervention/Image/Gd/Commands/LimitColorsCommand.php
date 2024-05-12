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

        // create empty canvas
        $resource = imagecreatetruecolor($size->width, $size->height);

        // define matte
        if (is_null($matte)) {
            $matte = imagecolorallocatealpha($resource, 255, 255, 255, 127);
        } else {
            $matte = $image->getDriver()->parseColor($matte)->getInt();
        }

        // fill with matte and copy original image
        imagefill($resource, 0, 0, $matte);

        // set transparency
        imagecolortransparent($resource, $matte);

        // copy original image
        imagecopy($resource, $image->getCore(), 0, 0, 0, 0, $size->width, $size->height);

        if (is_numeric($count) && $count <= 256) {
            // decrease colors
            imagetruecolortopalette($resource, true, $count);
        }

        // set new resource
        $image->setCore($resource);

        return true;
    }
}
