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

class FlipCommand extends ResizeCommand
{
    /**
     * Mirrors an image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $mode = $this->argument(0)->value('h');

        $size = $image->getSize();
        $dst = clone $size;

        switch (strtolower($mode)) {
            case 2:
            case 'v':
            case 'vert':
            case 'vertical':
                $size->pivot->y = $size->height - 1;
                $size->height = $size->height * (-1);
                break;

            default:
                $size->pivot->x = $size->width - 1;
                $size->width = $size->width * (-1);
                break;
        }

        return $this->modify($image, 0, 0, $size->pivot->x, $size->pivot->y, $dst->width, $dst->height, $size->width, $size->height);
    }
}
