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

class PolygonShape extends AbstractShape
{
    /**
     * Array of points of polygon
     *
     * @var array
     */
    public $points;

    /**
     * Create new polygon instance
     *
     * @param array $points
     */
    public function __construct($points)
    {
        $this->points = $this->formatPoints($points);
    }

    /**
     * Draw polygon on given image
     *
     * @param  Image   $image
     * @param  int     $x
     * @param  int     $y
     * @return boolean
     */
    public function applyToImage(Image $image, $x = 0, $y = 0)
    {
        $polygon = new \ImagickDraw;

        // set background
        $bgcolor = new Color($this->background);
        $polygon->setFillColor($bgcolor->getPixel());

        // set border
        if ($this->hasBorder()) {
            $border_color = new Color($this->border_color);
            $polygon->setStrokeWidth($this->border_width);
            $polygon->setStrokeColor($border_color->getPixel());
        }

        $polygon->polygon($this->points);

        $image->getCore()->drawImage($polygon);

        return true;
    }

    /**
     * Format polygon points to Imagick format
     *
     * @param  Array $points
     * @return Array
     */
    private function formatPoints($points)
    {
        $ipoints = [];
        $count = 1;

        foreach ($points as $key => $value) {
            if ($count%2 === 0) {
                $y = $value;
                $ipoints[] = ['x' => $x, 'y' => $y];
            } else {
                $x = $value;
            }
            $count++;
        }

        return $ipoints;
    }
}
