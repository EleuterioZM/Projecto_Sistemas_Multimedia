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

KT::import('admin:/includes/location/providers');

class KomentoLocationProvidersMaps extends KomentoLocationProviders
{
	protected $queries = [
		'latlng' => '',
		'address' => '',
		'key' => ''
	];

	public $url = 'https://maps.googleapis.com/maps/api/geocode/json';

	public function __construct()
	{
		parent::__construct();

		$this->setApiKey();
	}

	/**
	 * Get api key
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getApiKey()
	{
		return $this->config->get('location_key');
	}

	/**
	 * Set Api Keys
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setApiKey()
	{
		$key = $this->getApiKey();

		if ($key) {
			$this->setQuery('key', $key);
		}
	}

	/**
	 * Determines if the settings is complete
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isSettingsComplete()
	{
		if (!$this->config->get('location_service_provider') == 'maps') {
			return false;
		}

		return true;
	}

	/**
	 * Set the coordinates
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function setCoordinates($lat, $lng)
	{
		return $this->setQuery('latlng', $lat . ',' . $lng);
	}

	/**
	 * Set the search queries
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function setSearch($search = '')
	{
		return $this->setQuery('address', $search);
	}

	/**
	 * Get locations results
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getResult($queries = [])
	{
		$this->setQueries($queries);

		$options = [];

		if (!empty($this->queries['key'])) {
			$options['key'] = $this->queries['key'];
		}

		if (!empty($this->queries['address'])) {
			$options['address'] = $this->queries['address'];
		} else {
			$options['latlng'] = $this->queries['latlng'];
		}
		
		$mapLang = 'en';

		if ($mapLang) {
			$options['language'] = $mapLang;
		}

		$connector = FH::connector($this->url . '?' . http_build_query($options));
		$connector->setReferer(JURI::root());
		$result = $connector->execute()->getResult();

		$result = json_decode($result);

		if (!isset($result->status) || $result->status != 'OK') {
			$error = isset($result->error_message) ? $result->error_message : JText::_('COM_KT_LOCATION_PROVIDERS_MAPS_UNKNOWN_ERROR');

			$this->setError($error);
			return [];
		}

		$venues = [];

		foreach ($result->results as $row) {
			$obj = new KomentoLocationData;
			$obj->latitude = $row->geometry->location->lat;
			$obj->longitude = $row->geometry->location->lng;
			$obj->name = $row->formatted_address;
			$obj->address = '';

			$venues[] = $obj;
		}

		return $venues;
	}
}
