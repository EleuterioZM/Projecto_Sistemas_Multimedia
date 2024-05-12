<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

abstract class KomentoLocationProviders extends KomentoBase
{
	protected $queries = array();

	protected $url = '';

	protected $errors = array();

	public function setQuery($key, $value)
	{
		$this->queries[$key] = $value;

		return $this;
	}

	public function setQueries($iteratable)
	{
		if (is_array($iteratable) || is_object($iteratable)) {
			foreach ($iteratable as $key => $value) {
				$this->setQuery($key, $value);
			}
		}

		return $this;
	}

	// Custom/nonexistent methods should fall here and just silently return $this;
	public function __call($method, $arguments)
	{
		return $this;
	}

	public function buildUrl()
	{
		$url = $this->url;

		// Check if the string already have ?, if no then we add 1 at the end
		if (!strstr($url, '?')) {
			$url .= '?';
		}

		// If the last character is ?, then we just append the query string
		if (substr($url, -1) == '?') {
			$url .= http_build_query($this->queries);

			return $url;
		}

		// At this point, this means that there is a ? in the string but not at the end, hence we just need to append '&' and the query string
		if (substr($url, -1) != '&') {
			$url .= '&';
		}

		$url .= http_build_query($this->queries);
		return $url;
	}

	public function setError($msg)
	{
		$this->errors[] = $msg;
	}

	public function getError()
	{
		if (!$this->hasErrors()) {
			return null;
		}

		// Return the last possible error
		return $this->errors[count($this->errors) - 1];
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	// Result must be array of KomentoLocationData
	abstract public function getResult($queries = array());

	abstract public function setCoordinates($lat, $lng);

	abstract public function setSearch($search = '');
}

class KomentoLocationData
{
	public $latitude;
	public $longitude;
	public $name;
	public $address;
	public $formatted_address = '';
}
