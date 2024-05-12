<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use \NRFramework\Cache;

class NRURLs {

    private $url;
    private $shortener;
    private $cache;

    function __construct($url = null)
    {
        if (isset($url)) {
            $this->set($url);
        }

        $this->setCache(true);
    }

    public function set($url)
    {
        $url = trim(filter_var($url, FILTER_SANITIZE_URL));
        return ($this->url = $url);
    }

    public function setCache($state)
    {
        $this->cache = (bool) $state;
    }

    public function setShortener($service)
    {
        $this->shortener = $service;
    }

    public function get()
    {
        return $this->url;
    }

    public function validate($url_ = null)
    {
        $url = isset($url_) ? $url_ : $this->url;

        if (!$url)
        {
            return false;
        }

        // Remove all illegal characters from the URL
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return true;
        }

        return false;
    }

    public function getShort()
    {
        if (!$this->validate() || !isset($this->shortener))
        {
            return false;
        }

        $hash  = MD5($this->shortener->name . $this->url);
        $cache = Cache::read($hash, true);

        if ($cache)
        {
            return $cache;
        }

        // Load Shorten Service Class
        $file   = __DIR__ . "/" . strtolower($this->shortener->name) . '.php';
        $class  = 'nrURLShort' . $this->shortener->name;
        $method = "get";

        require_once(__DIR__ . "/shortener.php");

        if (!class_exists($class) && JFile::exists($file)) {
            require_once($file);
        }

        if (!class_exists($class) || !method_exists($class, $method))
        {
            return false;
        }

        $class_ = new $class($this->shortener, $this->url);
        $data = $class_->$method();

        // Return the original URL if we don't have a valid short URL
        if (!$this->validate($data))
        {  
            return false;
        }

        Cache::set($hash, $data);

        // Store to cache
        if ($this->cache)
        {
            Cache::write($hash, $data);
        }

        return $data;
    }

    /**
     *  Appends extra parameters to the end of the URL
     *
     *  @param   String  $url     Pass URL
     *  @param   String  $params  String of parameters (param=1&param=2)
     *
     *  @return  string           Returns new url
     */
    public function appendParams($params)
    {

        if (!$params)
        {
            return $this;
        }

        $url = $this->url;

        $query = parse_url($url, PHP_URL_QUERY);
        $params = trim($params, "?");
        $params = trim($params, "&");

        if ($query) 
        {
            $url .= '&' . $params;
        } else 
        {
            $url .= '?' . $params;
        }

        $this->set($url);

        return $this;
    }

}

?>