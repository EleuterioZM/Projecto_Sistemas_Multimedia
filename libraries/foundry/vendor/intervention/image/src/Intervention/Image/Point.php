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

namespace Intervention\Image;

defined('_JEXEC') or die('Unauthorized Access');

class Point
{
    /**
     * X coordinate
     *
     * @var int
     */
    public $x;

    /**
     * Y coordinate
     *
     * @var int
     */
    public $y;

    /**
     * Creates a new instance
     *
     * @param int $x
     * @param int $y
     */
    public function __construct($x = null, $y = null)
    {
        $this->x = is_numeric($x) ? intval($x) : 0;
        $this->y = is_numeric($y) ? intval($y) : 0;
    }

    /**
     * Sets X coordinate
     *
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = intval($x);
    }

    /**
     * Sets Y coordinate
     *
     * @param int $y
     */
    public function setY($y)
    {
        $this->y = intval($y);
    }

    /**
     * Sets both X and Y coordinate
     *
     * @param int $x
     * @param int $y
     */
    public function setPosition($x, $y)
    {
        $this->setX($x);
        $this->setY($y);
    }
}
