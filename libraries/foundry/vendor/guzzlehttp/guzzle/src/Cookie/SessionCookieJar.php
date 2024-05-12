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

namespace GuzzleHttp\Cookie;

defined('_JEXEC') or die('Unauthorized Access');

/**
 * Persists cookies in the client session
 */
class SessionCookieJar extends CookieJar
{
    /** @var string session key */
    private $sessionKey;
    
    /** @var bool Control whether to persist session cookies or not. */
    private $storeSessionCookies;

    /**
     * Create a new SessionCookieJar object
     *
     * @param string $sessionKey        Session key name to store the cookie
     *                                  data in session
     * @param bool $storeSessionCookies Set to true to store session cookies
     *                                  in the cookie jar.
     */
    public function __construct($sessionKey, $storeSessionCookies = false)
    {
        parent::__construct();
        $this->sessionKey = $sessionKey;
        $this->storeSessionCookies = $storeSessionCookies;
        $this->load();
    }

    /**
     * Saves cookies to session when shutting down
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * Save cookies to the client session
     */
    public function save()
    {
        $json = [];
        foreach ($this as $cookie) {
            /** @var SetCookie $cookie */
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $json[] = $cookie->toArray();
            }
        }

        $_SESSION[$this->sessionKey] = json_encode($json);
    }

    /**
     * Load the contents of the client session into the data array
     */
    protected function load()
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            return;
        }
        $data = json_decode($_SESSION[$this->sessionKey], true);
        if (is_array($data)) {
            foreach ($data as $cookie) {
                $this->setCookie(new SetCookie($cookie));
            }
        } elseif (strlen($data)) {
            throw new \RuntimeException("Invalid cookie data");
        }
    }
}
