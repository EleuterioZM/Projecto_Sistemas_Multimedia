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

class RectangleCommand extends AbstractCommand
{
    /**
     * Draws rectangle on given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $x1 = $this->argument(0)->type('numeric')->required()->value();
        $y1 = $this->argument(1)->type('numeric')->required()->value();
        $x2 = $this->argument(2)->type('numeric')->required()->value();
        $y2 = $this->argument(3)->type('numeric')->required()->value();
        $callback = $this->argument(4)->type('closure')->value();

        $rectangle_classname = sprintf('\Intervention\Image\%s\Shapes\RectangleShape',
            $image->getDriver()->getDriverName());

        $rectangle = new $rectangle_classname($x1, $y1, $x2, $y2);

        if ($callback instanceof Closure) {
            $callback($rectangle);
        }

        $rectangle->applyToImage($image, $x1, $y1);

        return true;
    }
}
