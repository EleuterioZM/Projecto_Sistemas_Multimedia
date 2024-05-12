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

class RotateCommand extends AbstractCommand
{
    /**
     * Rotates image counter clockwise
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $angle = $this->argument(0)->type('numeric')->required()->value();
        $color = $this->argument(1)->value();
        $color = new Color($color);

        // restrict rotations beyond 360 degrees, since the end result is the same
        $angle = fmod($angle, 360);

        // rotate image
        $image->getCore()->rotateImage($color->getPixel(), ($angle * -1));

        return true;
    }
}
