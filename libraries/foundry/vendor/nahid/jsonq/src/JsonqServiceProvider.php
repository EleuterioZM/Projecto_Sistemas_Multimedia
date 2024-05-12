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

namespace Nahid\JsonQ;

defined('_JEXEC') or die('Unauthorized Access');

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class JsonqServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerJsonq();
        $this->registerJsonManager();
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/jsonq.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('jsonq.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('jsonq');
        }
        $this->mergeConfigFrom($source, 'Jsonq');
    }

    /**
     * register JsonManager.
     */
    protected function registerJsonManager()
    {
        $config = $this->app['config'];
        $this->app->singleton('jsonq.manager', function () use ($config) {
            return new JsonQueriable($config->get('jsonq.json.storage_path'));
        });

        $this->app->alias('jsonq.manager', JsonQueriable::class);
    }

    /**
     * Register Talk class.
     */
    protected function registerJsonq()
    {
        $config = $this->app['config'];
        $this->app->bind('Jsonq', function () use ($config) {
            $path = $config->get('jsonq.json.storage_path');
            $storagePath = $path == '' ? null : $path;

            return new Jsonq($storagePath);
        });

        $this->app->alias('Jsonq', Jsonq::class);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'jsonq',
            'jsonq.manager',
        ];
    }
}
