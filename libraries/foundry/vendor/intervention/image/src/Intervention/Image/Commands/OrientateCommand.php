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

class OrientateCommand extends AbstractCommand
{
    /**
     * Correct image orientation according to Exif data
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        switch ($image->exif('Orientation')) {

            case 2:
                $image->flip();
                break;

            case 3:
                $image->rotate(180);
                break;

            case 4:
                $image->rotate(180)->flip();
                break;

            case 5:
                $image->rotate(270)->flip();
                break;

            case 6:
                $image->rotate(270);
                break;

            case 7:
                $image->rotate(90)->flip();
                break;

            case 8:
                $image->rotate(90);
                break;
        }

        return true;
    }
}
