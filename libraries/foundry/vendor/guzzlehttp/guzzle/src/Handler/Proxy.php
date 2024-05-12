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

namespace GuzzleHttp\Handler;

defined('_JEXEC') or die('Unauthorized Access');

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;

/**
 * Provides basic proxies for handlers.
 */
class Proxy
{
    /**
     * Sends synchronous requests to a specific handler while sending all other
     * requests to another handler.
     *
     * @param callable $default Handler used for normal responses
     * @param callable $sync    Handler used for synchronous responses.
     *
     * @return callable Returns the composed handler.
     */
    public static function wrapSync(
        callable $default,
        callable $sync
    ) {
        return function (RequestInterface $request, array $options) use ($default, $sync) {
            return empty($options[RequestOptions::SYNCHRONOUS])
                ? $default($request, $options)
                : $sync($request, $options);
        };
    }

    /**
     * Sends streaming requests to a streaming compatible handler while sending
     * all other requests to a default handler.
     *
     * This, for example, could be useful for taking advantage of the
     * performance benefits of curl while still supporting true streaming
     * through the StreamHandler.
     *
     * @param callable $default   Handler used for non-streaming responses
     * @param callable $streaming Handler used for streaming responses
     *
     * @return callable Returns the composed handler.
     */
    public static function wrapStreaming(
        callable $default,
        callable $streaming
    ) {
        return function (RequestInterface $request, array $options) use ($default, $streaming) {
            return empty($options['stream'])
                ? $default($request, $options)
                : $streaming($request, $options);
        };
    }
}
