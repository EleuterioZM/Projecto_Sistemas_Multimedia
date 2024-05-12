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

class HeightenCommand extends ResizeCommand
{
    /**
     * Resize image proportionally to given height
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $height = $this->argument(0)->type('digit')->required()->value();
        $additionalConstraints = $this->argument(1)->type('closure')->value();

        $this->arguments[0] = null;
        $this->arguments[1] = $height;
        $this->arguments[2] = function ($constraint) use ($additionalConstraints) {
            $constraint->aspectRatio();
            if(is_callable($additionalConstraints))
                $additionalConstraints($constraint);
        };

        return parent::execute($image);
    }
}
