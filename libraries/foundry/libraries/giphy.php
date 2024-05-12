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

class Giphy
{
	private $base = 'https://api.giphy.com/v1/';

	private $endpoints = [
		'search' => '/search',
		'trending' => '/trending'
	];

	public function __construct($fd)
	{
		$this->fd = $fd;
		$this->config = $this->fd->config();
		$this->items = null;
		$this->base = $this->base;

		$this->key = $this->config->get('giphy_apikey');
		$this->limit = $this->config->get('giphy_limit');
	}

	/**
	 * Request data from the GIPHY API
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getData($type, $query = false)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$result = $this->getItems($query, $type);

		if (isset($result->data)) {
			$this->items = $result->data;

			return $this->items;
		}

		return false;
	}

	/**
	 * Get giphy items from the connector with the query given
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getItems($query, $type)
	{
		$url = $this->getUrl('trending', $type);
		$options = $this->trending();

		if ($query) {
			$url = $this->getUrl('search', $type);
			$options = $this->search($query);
		}

		$url .= '?' . http_build_query($options);

		$connector = \FH::connector($url);
		$data = $connector->execute()->getResult();

		$items = json_decode($data);

		return $items;
	}

	/**
	 * Retrieve the url for a specific endpoint
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getUrl($endpoint, $type)
	{
		if (!isset($this->endpoints[$endpoint])) {
			return $this->base;
		}

		// NOTE: $type will be 'gifs' or 'stickers' only
		$url = $this->base . $type . $this->endpoints[$endpoint];

		return $url;
	}

	/**
	 * Search Endpoint
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function search($query)
	{
		$options = [];
		$options['api_key'] = $this->key;
		$options['q'] = $query;
		$options['limit'] = $this->limit;

		return $options;
	}

	/**
	 * Trending Endpoint
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function trending()
	{
		$options = [];
		$options['api_key'] = $this->key;
		$options['limit'] = $this->limit;

		return $options;
	}

	/**
	 * Determine whether is it enabled or not
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isEnabled()
	{
		// Do not need to proceed if the key is not provided
		if (!$this->key) {
			return false;
		}

		if ($this->config->get('giphy_enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Check for a valid GIPHY URL
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isValidUrl($url)
	{
		$pattern = "/(^|\\s)(https?:\\/\\/)?(([a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.([a-z]{2,6}))|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(:[0-9]{1,5})?(\\/.*)?/uism";

		$match = preg_match($pattern, $url);

		if (!$match) {
			return false;
		}

		// If it contains single or double quote, just return false
		if (stristr($url, '"') != false || stristr($url, "'") != false) {
			return false;
		}

		if (stristr($url, 'giphy.com') == false) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve the embedded item of GIPHY
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getItem($url)
	{
		$item = $this->fd->html('giphy.item', $url);

		return $item;
	}
}