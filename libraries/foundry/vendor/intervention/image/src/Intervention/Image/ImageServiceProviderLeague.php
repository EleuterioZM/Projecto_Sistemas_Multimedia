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

use League\Container\ServiceProvider\AbstractServiceProvider;

class ImageServiceProviderLeague extends AbstractServiceProvider
{
    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var array $provides
     */
    protected $provides = [
        'Intervention\Image\ImageManager'
    ];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Register the server provider.
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->share('Intervention\Image\ImageManager', function () {
            return new ImageManager($this->config);
        });
    }
}
