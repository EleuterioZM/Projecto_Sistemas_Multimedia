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

use Illuminate\Support\ServiceProvider;

class ImageServiceProviderLumen extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // merge default config
        $this->mergeConfigFrom(
          __DIR__.'/../../config/config.php',
          'image'
        );

        // set configuration
        $app->configure('image');

        // create image
        $app->singleton('image',function ($app) {
            return new ImageManager($app['config']->get('image'));
        });

        $app->alias('image', 'Intervention\Image\ImageManager');
    }
}
