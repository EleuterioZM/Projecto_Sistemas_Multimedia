<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

use NRFramework\Factory;

defined('_JEXEC') or die('Restricted access');

class URL
{
    /**
     *  Class constructor
     */
    public function __construct($path, $factory = null)
    {
        $this->path = trim($path);
        $this->factory = $factory ? $factory : new Factory();
    }

    public function getInstance()
    {
        return clone \JUri::getInstance($this->path);
    }

    public function getDomainName()
    {
        return strtolower(str_ireplace('www.', '', $this->getInstance()->getHost()));
    }

    public function isAbsolute()
    {
        return !is_null($this->getInstance()->getScheme());
    }

    public function isInternal()
    {
        if (!$this->path)
        {
            return false;
        }

        $host = $this->getInstance()->getHost();

        if (is_null($host))
        {
            return true;
        }

        $siteHost = $this->factory->getURI()->getHost();

        return preg_match('#' . preg_quote($siteHost, '#') . '#', $host) ?  true : false;
    }

    /**
     * Transform a relative path to absolute URL
     *
     * @return string
     */
    public function toAbsolute()
    {
        if (empty($this->path))
        {
            return;
        }

        // Check if it's already absolute URL
        if ($this->isAbsolute())
        {
            return $this->path;
        }

        $basePath = \parse_url(\JURI::root());

        $parse_path = $this->getInstance();
        $parse_path->setScheme($basePath['scheme']);
        $parse_path->setHost($basePath['host']);
        $parse_path->setPath($basePath['path'] . $parse_path->getPath());

        return $parse_path->toString();
    }

    /**
     * CDNify a resource
     *
     * @param string $host  The hostname of the CDN to be used
     * @param string $scheme
     *
     * @return string
     */
    public function cdnify($host, $scheme = 'https')
    {
        // Allow only internal URLs
        if (!$this->isInternal())
        {
            return $this->path;
        }

        // Allow only resource files
        $path = $this->getInstance()->getPath();

        if (strpos($path, '.') === false)
        {
            return $this->path;
        }

        return $this->setHost($host, $scheme);
    }

    public function setHost($domain, $scheme = 'https')
    {
        if (empty($this->path))
        {
            return;
        }

        $url_new = $this->getInstance();
        $url_new->setScheme($scheme);
        $url_new->setHost($domain);

        // Path should always start with a slash
        if ($url_new->getPath())
        {
            $url_new->setPath('/' . ltrim($url_new->getPath(), '/'));
        }

        $result = $url_new->toString();

        if ($scheme == '//')
        {
            $result = str_replace('://', '', $result);
        }

        return $result;
    }
}