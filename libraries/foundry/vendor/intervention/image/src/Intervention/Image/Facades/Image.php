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

namespace Intervention\Image\Facades;

defined('_JEXEC') or die('Unauthorized Access');

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Intervention\Image\Image make(mixed $data)
 * @method static self configure(array $config)
 * @method static \Intervention\Image\Image canvas(int $width, int $height, mixed $background = null)
 * @method static \Intervention\Image\Image cache(\Closure $callback, int $lifetime = null, boolean $returnObj = false)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'image';
    }
}
