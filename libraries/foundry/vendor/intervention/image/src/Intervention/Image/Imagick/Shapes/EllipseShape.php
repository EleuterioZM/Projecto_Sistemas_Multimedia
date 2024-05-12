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

namespace Intervention\Image\Imagick\Shapes;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\Imagick\Color;

class EllipseShape extends AbstractShape
{
    /**
     * Width of ellipse in pixels
     *
     * @var int
     */
    public $width = 100;

    /**
     * Height of ellipse in pixels
     *
     * @var int
     */
    public $height = 100;

    /**
     * Create new ellipse instance
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width = null, $height = null)
    {
        $this->width = is_numeric($width) ? intval($width) : $this->width;
        $this->height = is_numeric($height) ? intval($height) : $this->height;
    }

    /**
     * Draw ellipse instance on given image
     *
     * @param  Image   $image
     * @param  int     $x
     * @param  int     $y
     * @return boolean
     */
    public function applyToImage(Image $image, $x = 0, $y = 0)
    {
        $circle = new \ImagickDraw;

        // set background
        $bgcolor = new Color($this->background);
        $circle->setFillColor($bgcolor->getPixel());

        // set border
        if ($this->hasBorder()) {
            $border_color = new Color($this->border_color);
            $circle->setStrokeWidth($this->border_width);
            $circle->setStrokeColor($border_color->getPixel());
        }

        $circle->ellipse($x, $y, $this->width / 2, $this->height / 2, 0, 360);

        $image->getCore()->drawImage($circle);

        return true;
    }
}
