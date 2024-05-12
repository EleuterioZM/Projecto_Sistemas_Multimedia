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

class Constraint
{
    /**
     * Bit value of aspect ratio constraint
     */
    const ASPECTRATIO = 1;

    /**
     * Bit value of upsize constraint
     */
    const UPSIZE = 2;

    /**
     * Constraint size
     *
     * @var \Intervention\Image\Size
     */
    private $size;

    /**
     * Integer value of fixed parameters
     *
     * @var int
     */
    private $fixed = 0;

    /**
     * Create a new constraint based on size
     *
     * @param Size $size
     */
    public function __construct(Size $size)
    {
        $this->size = $size;
    }

    /**
     * Returns current size of constraint
     *
     * @return \Intervention\Image\Size
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Fix the given argument in current constraint
     *
     * @param  int $type
     * @return void
     */
    public function fix($type)
    {
        $this->fixed = ($this->fixed & ~(1 << $type)) | (1 << $type);
    }

    /**
     * Checks if given argument is fixed in current constraint
     *
     * @param  int  $type
     * @return boolean
     */
    public function isFixed($type)
    {
        return (bool) ($this->fixed & (1 << $type));
    }

    /**
     * Fixes aspect ratio in current constraint
     *
     * @return void
     */
    public function aspectRatio()
    {
        $this->fix(self::ASPECTRATIO);
    }

    /**
     * Fixes possibility to size up in current constraint
     *
     * @return void
     */
    public function upsize()
    {
        $this->fix(self::UPSIZE);
    }
}
