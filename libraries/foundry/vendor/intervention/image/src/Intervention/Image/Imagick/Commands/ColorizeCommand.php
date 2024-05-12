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

class ColorizeCommand extends AbstractCommand
{
    /**
     * Changes balance of different RGB color channels
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $red = $this->argument(0)->between(-100, 100)->required()->value();
        $green = $this->argument(1)->between(-100, 100)->required()->value();
        $blue = $this->argument(2)->between(-100, 100)->required()->value();

        // normalize colorize levels
        $red = $this->normalizeLevel($red);
        $green = $this->normalizeLevel($green);
        $blue = $this->normalizeLevel($blue);

        $qrange = $image->getCore()->getQuantumRange();

        // apply
        $image->getCore()->levelImage(0, $red, $qrange['quantumRangeLong'], \Imagick::CHANNEL_RED);
        $image->getCore()->levelImage(0, $green, $qrange['quantumRangeLong'], \Imagick::CHANNEL_GREEN);
        $image->getCore()->levelImage(0, $blue, $qrange['quantumRangeLong'], \Imagick::CHANNEL_BLUE);

        return true;
    }

    private function normalizeLevel($level)
    {
        if ($level > 0) {
            return $level/5;
        } else {
            return ($level+100)/100;
        }
    }
}
