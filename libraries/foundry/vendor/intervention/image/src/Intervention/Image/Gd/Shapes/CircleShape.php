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
);
namespace Intervention\Image\Gd\Shapes;

defined('_JEXEC') or die('Unauthorized Access'

use Intervention\Image\Image;

class CircleShape extends EllipseShape
{
    /**
     * Diameter of circle in pixels
     *
     * @var int
     */
    public $diameter = 100;

    /**
     * Create new instance of circle
     *
     * @param int $diameter
     */
    public function __construct($diameter = null)
    {
        $this->width = is_numeric($diameter) ? intval($diameter) : $this->diameter;
        $this->height = is_numeric($diameter) ? intval($diameter) : $this->diameter;
        $this->diameter = is_numeric($diameter) ? intval($diameter) : $this->diameter;
    }

    /**
     * Draw current circle on given image
     *
     * @param  Image   $image
     * @param  int     $x
     * @param  int     $y
     * @return boolean
     */
    public function applyToImage(Image $image, $x = 0, $y = 0)
    {
        return parent::applyToImage($image, $x, $y);
    }
}
