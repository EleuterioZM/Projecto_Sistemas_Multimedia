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

namespace Intervention\Image\Gd;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\Exception\NotSupportedException;

class Encoder extends \Intervention\Image\AbstractEncoder
{
    /**
     * Processes and returns encoded image as JPEG string
     *
     * @return string
     */
    protected function processJpeg()
    {
        ob_start();
        imagejpeg($this->image->getCore(), null, $this->quality);
        $this->image->mime = image_type_to_mime_type(IMAGETYPE_JPEG);
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

    /**
     * Processes and returns encoded image as PNG string
     *
     * @return string
     */
    protected function processPng()
    {
        ob_start();
        $resource = $this->image->getCore();
        imagealphablending($resource, false);
        imagesavealpha($resource, true);
        imagepng($resource, null, -1);
        $this->image->mime = image_type_to_mime_type(IMAGETYPE_PNG);
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

    /**
     * Processes and returns encoded image as GIF string
     *
     * @return string
     */
    protected function processGif()
    {
        ob_start();
        imagegif($this->image->getCore());
        $this->image->mime = image_type_to_mime_type(IMAGETYPE_GIF);
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }

    protected function processWebp()
    {
        if ( ! function_exists('imagewebp')) {
            throw new NotSupportedException(
                "Webp format is not supported by PHP installation."
            );
        }

        ob_start();
        imagepalettetotruecolor($this->image->getCore());
        imagealphablending($this->image->getCore(), true);
        imagesavealpha($this->image->getCore(), true);
        imagewebp($this->image->getCore(), null, $this->quality);
        $this->image->mime = defined('IMAGETYPE_WEBP') ? image_type_to_mime_type(IMAGETYPE_WEBP) : 'image/webp';
        $buffer = ob_get_contents();
        ob_end_clean();
        
        return $buffer;
    }

    /**
     * Processes and returns encoded image as TIFF string
     *
     * @return string
     */
    protected function processTiff()
    {
        throw new NotSupportedException(
            "TIFF format is not supported by Gd Driver."
        );
    }

    /**
     * Processes and returns encoded image as BMP string
     *
     * @return string
     */
    protected function processBmp()
    {
        if ( ! function_exists('imagebmp')) {
            throw new NotSupportedException(
                "BMP format is not supported by PHP installation."
            );
        }

        ob_start();
        imagebmp($this->image->getCore());
        $this->image->mime = defined('IMAGETYPE_BMP') ? image_type_to_mime_type(IMAGETYPE_BMP) : 'image/bmp';
        $buffer = ob_get_contents();
        ob_end_clean();
        
        return $buffer;
    }

    /**
     * Processes and returns encoded image as ICO string
     *
     * @return string
     */
    protected function processIco()
    {
        throw new NotSupportedException(
            "ICO format is not supported by Gd Driver."
        );
    }

    /**
     * Processes and returns encoded image as PSD string
     *
     * @return string
     */
    protected function processPsd()
    {
        throw new NotSupportedException(
            "PSD format is not supported by Gd Driver."
        );
    }

    /**
     * Processes and returns encoded image as AVIF string
     *
     * @return string
     */
    protected function processAvif()
    {
        throw new NotSupportedException(
            "AVIF format is not supported by Gd Driver."
        );
    }
}
