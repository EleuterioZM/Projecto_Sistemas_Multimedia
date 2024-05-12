<?php 

/**
 * @package         Novarain Framework
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

/**
 *  Novarain Framework URL Shortening Class
 *  Should be extended. The get() method is required.
 */
class NRURLShortener {

    /**
     *  The Shortener Service
     *
     *  @var  object
     */
    protected $service;

    /**
     *  The URL to be shortened
     *
     *  @var  string
     */
    protected $url;

    /**
     *  Sets if the service needs a valid login name
     *
     *  @var  boolean
     */
    protected $needsLogin = true;

    /**
     *  Sets if the service needs a valid API Key
     *
     *  @var  boolean
     */
    protected $needsKey = true;

    /**
     *  Constructor of class
     *
     *  @param  object  $service  The Shortener service information
     *  @param  string  $url      The URL to be shortened
     */
    public function __construct($service, $url) {
        $this->service = $service;
        $this->url = $url;
    }

    /**
     *  Throws an exception
     *
     *  @param   string  $msg  
     *
     *  @return  void
     */
    protected function throwError($msg)
    {
        throw new Exception(JText::sprintf('NR_URL_SHORTENING_FAILED', $this->url, $this->service->name, $msg));
    }

    /**
     *  Checks if credentials are set
     *
     *  @return  boolean  Returns true if credentials are set
     */
    protected function validateCredentials()
    {

        if ($this->needsKey && !isset($this->service->api))
        {
            $this->throwError("API Key not set");

            return false;
        }

        if ($this->needsLogin && !isset($this->service->login))
        {
            $this->throwError("Login not set");

            return false;
        }

        return true;
    }

    /**
     *  Shortens the URL
     *
     *  @return  string  On success returns the shortened URL
     */
    public function get()
    {

        if (!$this->validateCredentials())
        {
            return false;
        }

        $baseURL = $this->baseURL();

        if (!$baseURL)
        {
            return false;
        }

        try
        {
            $response = JHttpFactory::getHttp()->get($baseURL, null, 5);
         
            if ($response === null || $response->code !== 200)
            {
                $this->throwError($response->body);

                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->throwError($e->getMessage());

            return false;
        }

        return trim($response->body);
    }

}

?>