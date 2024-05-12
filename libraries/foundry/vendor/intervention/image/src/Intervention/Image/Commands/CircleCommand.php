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

class CircleCommand extends AbstractCommand
{
    /**
     * Draw a circle centered on given image
     *
     * @param  \Intervention\Image\image $image
     * @return boolean
     */
    public function execute($image)
    {
        $diameter = $this->argument(0)->type('numeric')->required()->value();
        $x = $this->argument(1)->type('numeric')->required()->value();
        $y = $this->argument(2)->type('numeric')->required()->value();
        $callback = $this->argument(3)->type('closure')->value();

        $circle_classname = sprintf('\Intervention\Image\%s\Shapes\CircleShape',
            $image->getDriver()->getDriverName());

        $circle = new $circle_classname($diameter);

        if ($callback instanceof Closure) {
            $callback($circle);
        }

        $circle->applyToImage($image, $x, $y);

        return true;
    }
}
