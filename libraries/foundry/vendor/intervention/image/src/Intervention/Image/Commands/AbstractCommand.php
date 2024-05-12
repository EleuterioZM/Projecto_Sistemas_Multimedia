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

namespace Intervention\Image\Commands;

defined('_JEXEC') or die('Unauthorized Access');

use Intervention\Image\Commands\Argument;

abstract class AbstractCommand
{
    /**
     * Arguments of command
     *
     * @var array
     */
    public $arguments;

    /**
     * Output of command
     *
     * @var mixed
     */
    protected $output;

    /**
     * Executes current command on given image
     *
     * @param  \Intervention\Image\Image $image
     * @return mixed
     */
    abstract public function execute($image);

    /**
     * Creates new command instance
     *
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Creates new argument instance from given argument key
     *
     * @param  int $key
     * @return \Intervention\Image\Commands\Argument
     */
    public function argument($key)
    {
        return new Argument($this, $key);
    }

    /**
     * Returns output data of current command
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output ? $this->output : null;
    }

    /**
     * Determines if current instance has output data
     *
     * @return boolean
     */
    public function hasOutput()
    {
        return ! is_null($this->output);
    }

    /**
     * Sets output data of current command
     *
     * @param mixed $value
     */
    public function setOutput($value)
    {
        $this->output = $value;
    }
}
