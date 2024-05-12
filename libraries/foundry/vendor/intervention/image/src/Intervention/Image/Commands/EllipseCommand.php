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

namespace Intervention\Image\Commands;

defined('_JEXEC') or die('Unauthorized Access');

use Closure;

class EllipseCommand extends AbstractCommand
{
    /**
     * Draws ellipse on given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $width = $this->argument(0)->type('numeric')->required()->value();
        $height = $this->argument(1)->type('numeric')->required()->value();
        $x = $this->argument(2)->type('numeric')->required()->value();
        $y = $this->argument(3)->type('numeric')->required()->value();
        $callback = $this->argument(4)->type('closure')->value();

        $ellipse_classname = sprintf('\Intervention\Image\%s\Shapes\EllipseShape',
            $image->getDriver()->getDriverName());

        $ellipse = new $ellipse_classname($width, $height);

        if ($callback instanceof Closure) {
            $callback($ellipse);
        }

        $ellipse->applyToImage($image, $x, $y);

        return true;
    }
}
