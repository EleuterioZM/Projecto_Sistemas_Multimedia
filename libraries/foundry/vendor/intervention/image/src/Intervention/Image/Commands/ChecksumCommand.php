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

class ChecksumCommand extends AbstractCommand
{
    /**
     * Calculates checksum of given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $colors = [];

        $size = $image->getSize();

        for ($x=0; $x <= ($size->width-1); $x++) {
            for ($y=0; $y <= ($size->height-1); $y++) {
                $colors[] = $image->pickColor($x, $y, 'array');
            }
        }

        $this->setOutput(md5(serialize($colors)));

        return true;
    }
}
