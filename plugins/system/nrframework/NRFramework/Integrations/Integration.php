<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

// No direct access
defined('_JEXEC') or die;

jimport('joomla.log.log');

use Joomla\Registry\Registry;

class Integration
{
	protected $key;
	protected $endpoint;
	protected $request_successful = false;
	protected $last_error         = '';
	protected $last_response      = [];
	protected $last_request       = [];
	protected $timeout            = 60;
	protected $options;
	protected $encode             = true;
	protected $response_type      = 'json';

	public function __construct()
	{
		$this->options       = new \JRegistry;
		$this->options->set('timeout', $this->timeout);
		$this->options->set('headers.Accept', 'application/json');
		$this->options->set('headers.Content-Type', 'application/json');
	}

	/**
	 * Setter method for the API Key or Access Token
	 *
	 * @param string $apiKey 
	 */
	public function setKey($apiKey)
	{
		$apiKey = is_array($apiKey) && isset($apiKey['api']) ? $apiKey['api'] : $apiKey;

		if (!is_string($apiKey) || empty($apiKey) || is_null($apiKey))
		{
			throw new \Exception('Invalid API Key supplied.');
		}

		$this->key = trim($apiKey);
	}

	/**
	 * Setter method for the endpoint
	 * @param string $url The URL which is set in the account's developer settings
	 * @throws \Exception 
	 */
	public function setEndpoint($url)
	{
		if (!empty($url))
		{
			$this->endpoint = $url;
		}
		else
		{
			throw new \Exception("Invalid Endpoint URL `{$url}` supplied.");
		}
	}

	/**
	 * Was the last request successful?
	 * @return bool  True for success, false for failure
	 */
	public function success()
	{
		return $this->request_successful;
	}

	/**
	 * Get the last error returned by either the network transport, or by the API.
	 * If something didn't work, this should contain the string describing the problem.
	 * @return  array|false  describing the error
	 */
	public function getLastError()
	{
		return $this->last_error ?: false;
	}

	/**
	 * Get an array containing the HTTP headers and the body of the API response.
	 * @return array  Assoc array with keys 'headers' and 'body'
	 */
	public function getLastResponse()
	{
		return $this->last_response;
	}

	/**
	 * Get an array containing the HTTP headers and the body of the API request.
	 * @return array  Assoc array
	 */
	public function getLastRequest()
	{
		return $this->last_request;
	}

	/**
	 * Make an HTTP DELETE request - for deleting data
	 * @param   string $method URL of the API request method
	 * @param   array $args Assoc array of arguments (if any)
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function delete($method, $args = [])
	{
		return $this->makeRequest('delete', $method, $args);
	}

	/**
	 * Make an HTTP GET request - for retrieving data
	 * @param   string $method URL of the API request method
	 * @param   array $args Assoc array of arguments (usually your data)
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function get($method, $args = [])
	{
		return $this->makeRequest('get', $method, $args);
	}

	/**
	 * Make an HTTP PATCH request - for performing partial updates
	 * @param   string $method URL of the API request method
	 * @param   array $args Assoc array of arguments (usually your data)
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function patch($method, $args = [])
	{
		return $this->makeRequest('patch', $method, $args);
	}

	/**
	 * Make an HTTP POST request - for creating and updating items
	 * @param   string $method URL of the API request method
	 * @param   array $args Assoc array of arguments (usually your data)
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function post($method, $args = [])
	{
		return $this->makeRequest('post', $method, $args);
	}

	/**
	 * Make an HTTP PUT request - for creating new items
	 * @param   string $method URL of the API request method
	 * @param   array $args Assoc array of arguments (usually your data)
	 * @return  array|false   Assoc array of API response, decoded from JSON
	 */
	public function put($method, $args = [])
	{
		return $this->makeRequest('put', $method, $args);
	}

	/**
	 * Performs the underlying HTTP request. Not very exciting.
	 * @param  string $http_verb The HTTP verb to use: get, post, put, patch, delete
	 * @param  string $method The API method to be called
	 * @param  array $args Assoc array of parameters to be passed
	 * @return array|false Assoc array of decoded result
	 * @throws \Exception
	 */
	protected function makeRequest($http_verb, $method, $args = [])
	{
		$url = $this->endpoint;

		if (!empty($method) && !is_null($method) && strpos($url, '?') === false)
		{
			$url .= '/' . $method;
		}

		$this->last_error         = '';
		$this->request_successful = false;
		$this->last_response      = [];
		$this->last_request       = [
			'method'  => $http_verb,
			'path'    => $method,
			'url'     => $url,
			'body'    => '',
			'timeout' => $this->timeout,
		];

		$http = \JHttpFactory::getHttp($this->options);

		switch ($http_verb)
		{
			case 'post':
				$this->attachRequestPayload($args);
				$response = $http->post($url, $this->last_request['body']);
				break;

			case 'get':
				$query = http_build_query($args, '', '&');
				$this->last_request['body'] = $query;
				$response = (strpos($url,'?') !== false) ? $http->get($url . '&' . $query) : $http->get($url . '?' . $query);
				break;

			case 'delete':
				$response = $http->delete($url);
				break;

			case 'patch':
				$this->attachRequestPayload($args);
				$response = $http->patch($url, $this->last_request['body']);
				break;

			case 'put':
				$this->attachRequestPayload($args);
				$response = $http->put($url, $this->last_request['body']);
				break;
		}
		
		// Convert body JSON - Do we really need this line?
		$response->body = $this->convertResponse($response->body);

		// Format response object to array
		$this->last_response = $response;
		$this->determineSuccess();

		// Log request if debug is enabled.
		if (JDEBUG)
		{
			$this->logRequest();
		}

		return $this->last_response->body;
	}

	/**
	 * Log API request to framework's log file to help troubleshoot issues.
	 *
	 * @return void
	 */
	private function logRequest()
	{
		$output = '
			Request
			---------------------------------------------------------------------------
			' . print_r($this->last_request, true) . '

			Response
			---------------------------------------------------------------------------
			' . print_r($this->last_response, true) . '
		';

		try {
			\JLog::add($output, \JLog::DEBUG, 'nrframework');
		} catch (\Throwable $th) {
		}	
	}

	/**
	 * Encode the data and attach it to the request
	 * @param   array $data Assoc array of data to attach
	 */
	protected function attachRequestPayload($data)
	{
		if (!$this->encode) 
		{
			$this->last_request['body'] = http_build_query($data);
			return;
		}

		$this->last_request['body'] = json_encode($data);
	}

	/**
	 * Check if the response was successful or a failure. If it failed, store the error.
	 * 
	 * @return bool     If the request was successful
	 */
	protected function determineSuccess()
	{
		$status  = $this->last_response->code;
		$success = ($status >= 200 && $status <= 299) ? true : false;

		return ($this->request_successful = $success);
	}

	/**
	 *  Converts the HTTP Call response to a traversable type
	 *
	 *  @param   json|xml  $response  
	 *
	 *  @return  array|object 
	 */
	protected function convertResponse($response)
	{
		switch ($this->response_type) 
		{
			case 'json':
				return  json_decode($response, true);
			case 'xml':
				return new \SimpleXMLElement($response);
			case 'text':
				return $response;
		}
	}

	/**
	 * Search Custom Fields declared by the user for a specific custom field. If exists return its value.
	 *
	 * @param	array	$needles	The custom field names
	 * @param	array	$haystack	The custom fields array
	 *
	 * @return	string	The value of the custom field or an empty string if not found
	 */
	protected function getCustomFieldValue($needles, $haystack)
	{
		$needles  = is_array($needles) ? $needles : (array) $needles;
		$haystack = array_change_key_case($haystack);

		$found = '';

		foreach ($needles as $needle)
		{
			$needle = strtolower($needle);

			if (array_key_exists($needle, $haystack))
			{
				$found = trim($haystack[$needle]);
				break;
			}
		}

		return $found;
	}

	/**
	 * Set encode
	 * 
	 * @param   boolean  $encode
	 * 
	 * @return  void
	 */
	public function setEncode($encode)
	{
		$this->encode = $encode;
	}
}