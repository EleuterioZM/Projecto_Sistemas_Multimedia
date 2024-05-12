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

namespace GuzzleHttp\Promise;
defined('_JEXEC') or die('Unauthorized Access');
/**
 * A promise that has been rejected.
 *
 * Thenning off of this promise will invoke the onRejected callback
 * immediately and ignore other callbacks.
 */
class RejectedPromise implements PromiseInterface
{
    private $reason;

    public function __construct($reason)
    {
        if (is_object($reason) && method_exists($reason, 'then')) {
            throw new \InvalidArgumentException(
                'You cannot create a RejectedPromise with a promise.'
            );
        }

        $this->reason = $reason;
    }

    public function then(
        callable $onFulfilled = null,
        callable $onRejected = null
    ) {
        // If there's no onRejected callback then just return self.
        if (!$onRejected) {
            return $this;
        }

        $queue = Utils::queue();
        $reason = $this->reason;
        $p = new Promise([$queue, 'run']);
        $queue->add(static function () use ($p, $reason, $onRejected) {
            if (Is::pending($p)) {
                try {
                    // Return a resolved promise if onRejected does not throw.
                    $p->resolve($onRejected($reason));
                } catch (\Throwable $e) {
                    // onRejected threw, so return a rejected promise.
                    $p->reject($e);
                } catch (\Exception $e) {
                    // onRejected threw, so return a rejected promise.
                    $p->reject($e);
                }
            }
        });

        return $p;
    }

    public function otherwise(callable $onRejected)
    {
        return $this->then(null, $onRejected);
    }

    public function wait($unwrap = true, $defaultDelivery = null)
    {
        if ($unwrap) {
            throw Create::exceptionFor($this->reason);
        }

        return null;
    }

    public function getState()
    {
        return self::REJECTED;
    }

    public function resolve($value)
    {
        throw new \LogicException("Cannot resolve a rejected promise");
    }

    public function reject($reason)
    {
        if ($reason !== $this->reason) {
            throw new \LogicException("Cannot reject a rejected promise");
        }
    }

    public function cancel()
    {
        // pass
    }
}
