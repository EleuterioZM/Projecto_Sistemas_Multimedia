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

class InsertCommand extends AbstractCommand
{
    /**
     * Insert another image into given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $source = $this->argument(0)->required()->value();
        $position = $this->argument(1)->type('string')->value();
        $x = $this->argument(2)->type('digit')->value(0);
        $y = $this->argument(3)->type('digit')->value(0);

        // build watermark
        $watermark = $image->getDriver()->init($source);

        // define insertion point
        $image_size = $image->getSize()->align($position, $x, $y);
        $watermark_size = $watermark->getSize()->align($position);
        $target = $image_size->relativePosition($watermark_size);

        // insert image at position
        imagealphablending($image->getCore(), true);
        return imagecopy($image->getCore(), $watermark->getCore(), $target->x, $target->y, 0, 0, $watermark_size->width, $watermark_size->height);
    }
}
