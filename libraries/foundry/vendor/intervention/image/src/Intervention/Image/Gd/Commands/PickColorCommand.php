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
use Intervention\Image\Gd\Color;

class PickColorCommand extends AbstractCommand
{
    /**
     * Read color information from a certain position
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $x = $this->argument(0)->type('digit')->required()->value();
        $y = $this->argument(1)->type('digit')->required()->value();
        $format = $this->argument(2)->type('string')->value('array');

        // pick color
        $color = imagecolorat($image->getCore(), $x, $y);

        if ( ! imageistruecolor($image->getCore())) {
            $color = imagecolorsforindex($image->getCore(), $color);
            $color['alpha'] = round(1 - $color['alpha'] / 127, 2);
        }

        $color = new Color($color);

        // format to output
        $this->setOutput($color->format($format));

        return true;
    }
}
