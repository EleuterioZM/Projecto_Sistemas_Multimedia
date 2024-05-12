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

class KomentoLocationProvidersOsm extends KomentoLocationProviders
{
	protected $queries = [
		'lat' => '',
		'lon' => '',
		'q' => ''
	];

	public $url = 'https://nominatim.openstreetmap.org';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Determines if the settings is complete
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isSettingsComplete()
	{
		if ($this->config->get('location_service_provider') != 'osm') {
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
		return $this->setQuery('lat', $lat) && $this->setQuery('lon', $lng);
	}

	/**
	 * Set the search queries
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function setSearch($search = '')
	{
		return $this->setQuery('q', $search);
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

		$type = 'reverse';

		if (!empty($this->queries['q'])) {
			$options['q'] = $this->queries['q'];
			$type = 'search';
		} else {
			$options['lat'] = $this->queries['lat'];
			$options['lon'] = $this->queries['lon'];
		}
		
		$connector = FH::connector($this->url . '/' . $type . '?format=json&addressdetails=1&' . http_build_query($options));
		$result = $connector->execute()->getResult();

		$result = json_decode($result);

		if (empty($result) || isset($result->error)) {
			$error = isset($result->message) ? $result->message : JText::_('COM_KT_LOCATION_PROVIDERS_MAPS_UNKNOWN_ERROR');

			$this->setError($error);
			return [];
		}
		
		$result = is_array($result) ? $result : array($result);
		$venues = [];

		foreach ($result as $row) {
			$obj = new KomentoLocationData;
			$obj->latitude = $row->lat;
			$obj->longitude = $row->lon;
			$obj->name = $row->display_name;
			$obj->address = $row->address;
			$obj->formatted_address =$row->display_name;

			$venues[] = $obj;
		}

		return $venues;
	}
}
