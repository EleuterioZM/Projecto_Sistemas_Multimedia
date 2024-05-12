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
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as IlluminateApplication;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Actual provider
     *
     * @var \Illuminate\Support\ServiceProvider
     */
    protected $provider;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->provider = $this->getProvider();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this->provider, 'boot')) {
            return $this->provider->boot();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        return $this->provider->register();
    }

    /**
     * Return ServiceProvider according to Laravel version
     *
     * @return \Intervention\Image\Provider\ProviderInterface
     */
    private function getProvider()
    {
        if ($this->app instanceof LumenApplication) {
            $provider = '\Intervention\Image\ImageServiceProviderLumen';
        } elseif (version_compare(IlluminateApplication::VERSION, '5.0', '<')) {
            $provider = '\Intervention\Image\ImageServiceProviderLaravel4';
        } else {
            $provider = '\Intervention\Image\ImageServiceProviderLaravelRecent';
        }

        return new $provider($this->app);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['image'];
    }
}
