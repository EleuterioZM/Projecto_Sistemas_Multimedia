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

use Intervention\Image\Commands\ExifCommand as BaseCommand;
use Intervention\Image\Exception\NotSupportedException;

class ExifCommand extends BaseCommand
{
    /**
     * Prefer extension or not
     *
     * @var bool
     */
    private $preferExtension = true;

    /**
     *
     */
    public function dontPreferExtension() {
        $this->preferExtension = false;
    }

    /**
     * Read Exif data from the given image
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        if ($this->preferExtension && function_exists('exif_read_data')) {
            return parent::execute($image);
        }

        $core = $image->getCore();

        if ( ! method_exists($core, 'getImageProperties')) {
            throw new NotSupportedException(
                "Reading Exif data is not supported by this PHP installation."
            );
        }

        $requestedKey = $this->argument(0)->value();
        if ($requestedKey !== null) {
            $this->setOutput($core->getImageProperty('exif:' . $requestedKey));
            return true;
        }

        $exif = [];
        $properties = $core->getImageProperties();
        foreach ($properties as $key => $value) {
            if (substr($key, 0, 5) !== 'exif:') {
                continue;
            }

            $exif[substr($key, 5)] = $value;
        }

        $this->setOutput($exif);
        return true;
    }
}
