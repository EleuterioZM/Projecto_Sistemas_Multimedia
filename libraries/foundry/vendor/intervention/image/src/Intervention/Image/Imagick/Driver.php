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

namespace Intervention\Image\Imagick;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\AbstractDriver;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\Image;

class Driver extends AbstractDriver
{
    /**
     * Creates new instance of driver
     *
     * @param Decoder $decoder
     * @param Encoder $encoder
     */
    public function __construct(Decoder $decoder = null, Encoder $encoder = null)
    {
        if ( ! $this->coreAvailable()) {
            throw new NotSupportedException(
                "ImageMagick module not available with this PHP installation."
            );
        }

        $this->decoder = $decoder ? $decoder : new Decoder;
        $this->encoder = $encoder ? $encoder : new Encoder;
    }

    /**
     * Creates new image instance
     *
     * @param  int     $width
     * @param  int     $height
     * @param  mixed   $background
     * @return \Intervention\Image\Image
     */
    public function newImage($width, $height, $background = null)
    {
        $background = new Color($background);

        // create empty core
        $core = new \Imagick;
        $core->newImage($width, $height, $background->getPixel(), 'png');
        $core->setType(\Imagick::IMGTYPE_UNDEFINED);
        $core->setImageType(\Imagick::IMGTYPE_UNDEFINED);
        $core->setColorspace(\Imagick::COLORSPACE_UNDEFINED);

        // build image
        $image = new Image(new static, $core);

        return $image;
    }

    /**
     * Reads given string into color object
     *
     * @param  string $value
     * @return AbstractColor
     */
    public function parseColor($value)
    {
        return new Color($value);
    }

    /**
     * Checks if core module installation is available
     *
     * @return boolean
     */
    protected function coreAvailable()
    {
        return (extension_loaded('imagick') && class_exists('Imagick'));
    }
}
