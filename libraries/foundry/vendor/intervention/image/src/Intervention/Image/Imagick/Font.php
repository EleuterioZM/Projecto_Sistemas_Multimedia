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

use Intervention\Image\AbstractFont;
use Intervention\Image\Exception\RuntimeException;
use Intervention\Image\Image;

class Font extends AbstractFont
{
    /**
     * Draws font to given image at given position
     *
     * @param  Image   $image
     * @param  int     $posx
     * @param  int     $posy
     * @return void
     */
    public function applyToImage(Image $image, $posx = 0, $posy = 0)
    {
        // build draw object
        $draw = new \ImagickDraw();
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);

        // set font file
        if ($this->hasApplicableFontFile()) {
            $draw->setFont($this->file);
        } else {
            throw new RuntimeException(
                "Font file must be provided to apply text to image."
            );
        }

        // parse text color
        $color = new Color($this->color);

        $draw->setFontSize($this->size);
        $draw->setFillColor($color->getPixel());
        $draw->setTextKerning($this->kerning);

        // align horizontal
        switch (strtolower($this->align)) {
            case 'center':
                $align = \Imagick::ALIGN_CENTER;
                break;

            case 'right':
                $align = \Imagick::ALIGN_RIGHT;
                break;

            default:
                $align = \Imagick::ALIGN_LEFT;
                break;
        }

        $draw->setTextAlignment($align);

        // align vertical
        if (strtolower($this->valign) != 'bottom') {

            // corrections on y-position
            switch (strtolower($this->valign)) {
                case 'center':
                case 'middle':
                // calculate box size
                $dimensions = $image->getCore()->queryFontMetrics($draw, $this->text);
                $posy = $posy + $dimensions['textHeight'] * 0.65 / 2;
                break;

                case 'top':
                // calculate box size
                $dimensions = $image->getCore()->queryFontMetrics($draw, $this->text, false);
                $posy = $posy + $dimensions['characterHeight'];
                break;
            }
        }

        // apply to image
        $image->getCore()->annotateImage($draw, $posx, $posy, $this->angle * (-1), $this->text);
    }
    
    /**
     * Calculates bounding box of current font setting
     *
     * @return array
     */
    public function getBoxSize()
    {
        $box = [];

        // build draw object
        $draw = new \ImagickDraw();
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);

        // set font file
        if ($this->hasApplicableFontFile()) {
            $draw->setFont($this->file);
        } else {
            throw new RuntimeException(
                "Font file must be provided to apply text to image."
            );
        }

        $draw->setFontSize($this->size);

        $dimensions = (new \Imagick())->queryFontMetrics($draw, $this->text);

        if (strlen($this->text) == 0) {
            // no text -> no boxsize
            $box['width'] = 0;
            $box['height'] = 0;
        } else {
            // get boxsize
            $box['width'] = intval(abs($dimensions['textWidth']));
            $box['height'] = intval(abs($dimensions['textHeight']));
        }

        return $box;
    }
}
