<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

\FH::autoload();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Connector
{
	private $client = null;

	protected $proxy = null;
	protected $proxyEnabled = null;
	protected $proxyAuth = '';
	protected $proxyUrl = '';

	protected $headers = [];
	protected $query = [];
	protected $method = 'GET';
	protected $headerOnly = false;

	protected $error = '';

	public function __construct($url = '')
	{
		$this->client = new Client();
		$this->url = $url;

		$jconfig = \FH::jconfig();

		$this->proxy = array(
			'enable' => $jconfig->get('proxy_enable'),
			'host' => $jconfig->get('proxy_host'),
			'port' => $jconfig->get('proxy_port'),
			'user' => $jconfig->get('proxy_port'),
			'pass' => $jconfig->get('proxy_pass')
		);

		if ($this->isProxyEnabled()) {
			$this->proxyUrl = $this->proxy['host'] . ':' . $this->proxy['port'];
			$this->proxyAuth = $this->proxy['user'] . ':' . $this->proxy['pass'];
		}
	}

	/**
	 * Performs the request to the url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function execute()
	{
		$options = [
			'allow_redirects' => true,
			'headers' => [],
			'query' => [],
			'curl' => [
				CURLOPT_CAINFO => __DIR__ . '/connector/cacert.pem'
			]
		];

		$queries = $this->extractQueryString($this->url);

		if ($this->headers) {
			$options['headers'] = $this->headers;
		}

		if ($this->query) {
			$queries = array_merge($queries, $this->query);
		}

		$options['query'] = $queries;

		// Making request for head only
		if ($this->headerOnly) {
			$this->response = $this->client->head($this->url, $options);
			return $this;
		}

		try {
			$this->response = $this->client->request($this->method, $this->url, $options);
		} catch (Exception $exception) {
			$this->setError($exception->getMessage());
		} catch (ClientException $exception) {
			$this->setError($exception->getMessage());
		}

		return $this;
	}

	/**
	 * Adds to the query string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function addQuery($key, $value)
	{
		$this->query[$key] = $value;

		return $this;
	}

	/**
	 * Sets value in the header
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function addHeader($key, $value)
	{
		$this->headers[$key] = $value;

		return $this;
	}

	/**
	 * Extracts query string data into an associative array
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function extractQueryString($url)
	{
		// We need to merge the queries from the query string
		$tmp = parse_url($url, PHP_URL_QUERY);

		if (!$tmp) {
			return [];
		}

		parse_str($tmp, $queries);

		return $queries;
	}

	/**
	 * Returns the result that has already been executed.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getResult($withHeaders = false)
	{
		// If there is an error, just return the error
		if ($this->error) {
			return $this->error;
		}

		if ($this->headerOnly) {
			$headers = $this->getResponseHeaders();

			return $headers;
		}

		$contents = (string) $this->response->getBody();

		if ($withHeaders) {
			$headers = $this->getResponseHeaders();

			return $headers . "\r\n\r\n" . $contents;
		}

		return $contents;
	}
	
	/**
	 * Formats the response headers from guzzle into standard string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getResponseHeaders()
	{
		if (!$this->response) {
			return $this->response;
		}

		if (!is_object($this->response) && is_string($this->response)) {
			return $this->response;
		}

		$headers = '';

		// Get all of the response headers.
		$data = $this->response->getHeaders();

		foreach ($data as $name => $values) {
			$headers .= $name . ': ' . implode(', ', $values) . "\r\n";
		}

		return $headers;
	}

	/**
	 * Determines if proxy is enabled
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isProxyEnabled()
	{
		if (is_null($this->proxyEnabled)) {
			$this->proxyEnabled = false;

			if ($this->proxy['enable'] && $this->proxy['host'] && $this->proxy['port'] && $this->proxy['user'] && $this->proxy['pass']) {
				$this->proxyEnabled = true;
			}
		}

		return $this->proxyEnabled;
	}

	/**
	 * Sets an error message for the request
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setError($message)
	{
		$this->error = $message;

		return $this;
	}

	/**
	 * Determines the method used to connect
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setMethod($method = 'GET')
	{
		$this->method = $method;

		return $this;
	}

	/**
	 * Sets the referer in the request
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setReferer($referer)
	{
		$this->headers['Referer'] = $referer;

		return $this;
	}

	/**
	 * Sets the user agent for the request
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setUserAgent($userAgent)
	{
		$this->headers['User-Agent'] = $userAgent;
		return $this;
	}

	/**
	 * Determins if we should only be requesting for head data only
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function useHeadersOnly()
	{
		$this->headerOnly = true;

		return $this;
	}
}
