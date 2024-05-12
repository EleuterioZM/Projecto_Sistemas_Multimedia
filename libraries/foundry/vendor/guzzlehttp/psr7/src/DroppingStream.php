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
 * Stream decorator that begins dropping data once the size of the underlying
 * stream becomes too full.
 *
 * @final
 */
class DroppingStream implements StreamInterface
{
    use StreamDecoratorTrait;

    private $maxLength;

    /**
     * @param StreamInterface $stream    Underlying stream to decorate.
     * @param int             $maxLength Maximum size before dropping data.
     */
    public function __construct(StreamInterface $stream, $maxLength)
    {
        $this->stream = $stream;
        $this->maxLength = $maxLength;
    }

    public function write($string)
    {
        $diff = $this->maxLength - $this->stream->getSize();

        // Begin returning 0 when the underlying stream is too large.
        if ($diff <= 0) {
            return 0;
        }

        // Write the stream or a subset of the stream if needed.
        if (strlen($string) < $diff) {
            return $this->stream->write($string);
        }

        return $this->stream->write(substr($string, 0, $diff));
    }
}
