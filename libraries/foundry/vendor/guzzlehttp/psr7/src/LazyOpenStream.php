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

namespace GuzzleHttp\Psr7;

defined('_JEXEC') or die('Unauthorized Access');

use Psr\Http\Message\StreamInterface;

/**
 * Lazily reads or writes to a file that is opened only after an IO operation
 * take place on the stream.
 *
 * @final
 */
class LazyOpenStream implements StreamInterface
{
    use StreamDecoratorTrait;

    /** @var string File to open */
    private $filename;

    /** @var string */
    private $mode;

    /**
     * @param string $filename File to lazily open
     * @param string $mode     fopen mode to use when opening the stream
     */
    public function __construct($filename, $mode)
    {
        $this->filename = $filename;
        $this->mode = $mode;
    }

    /**
     * Creates the underlying stream lazily when required.
     *
     * @return StreamInterface
     */
    protected function createStream()
    {
        return Utils::streamFor(Utils::tryFopen($this->filename, $this->mode));
    }
}
