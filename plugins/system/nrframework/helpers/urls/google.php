<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

class nrURLShortGoogle extends NRURLShortener
{
	
	function get()
	{

        if (!$this->validateCredentials())
        {
            return false;
        }

        $baseURL = "https://www.googleapis.com/urlshortener/v1/url?key=".$this->service->api;

        $data = '{ "longUrl": "'.$this->url.'" }';
        $headers['Content-Type'] = 'application/json';

		try
        {
            $response = JHttpFactory::getHttp()->post($baseURL, $data, $headers, 5);
            
            if ($response === null || $response->code !== 200)
            {
                $result = json_decode($response->body);
                $this->throwError($result->error->message);

                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->throwError($e->getMessage());

            return false;
        }

        $data = json_decode($response->body);

        if (!isset($data->id))
        {
            return false;
        }

        return $data->id;
	}
}
