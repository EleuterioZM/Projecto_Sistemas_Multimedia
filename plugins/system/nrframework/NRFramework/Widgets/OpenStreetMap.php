<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

/**
 *  OpenStreetMap
 */
class OpenStreetMap extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		/**
		 * The value of the widget.
		 * Format: latitude,longitude
		 * 
		 * i.e. 36.891319,27.283480
		 */
		'value' => '',
		
		// Default map width
		'width' => '500px',

		// Default map height
		'height' => '400px',

		// Default map zoon
		'zoom' => 4,

		// Map scale. Values: metric, imperial, false
		'scale' => false,

		// View mode of the map. Values: road, aerial.
		'view' => 'road',

		/**
		 * Address input above map
		 */
		// Whether to show the address input above the map
		'showAddressInput' => false,

		/**
		 * Map Marker
		 */
		// Whether to show the marker
		'showMarker' => true,

		// Marker image relative to Joomla installation
		'markerImage' => 'media/plg_system_nrframework/img/marker.png',

		// Allows marker to be dragged
		'allowMarkerDrag' => false,

		// Allows map to be clicked and thus allows us to select a new location
		'allowMapClick' => false,

		// Whether to show the marker tooltip
		'showMarkerTooltip' => false,

		// Set whether to display the marker tooltip textarea.
		'showMarkerTooltipInput' => false,

		// Marker Tooltip Textarea Name. If a value is given, then the tooltip textarea field appears below the map
		'markerTooltipName' => '',

		// Marker tooltip value
		'markerTooltipValue' => '',
		
		/**
		 * Coordinates input below map
		 */
		// Whether to show the coordinates input
		'showCoordsInput' => false,

		// Coordinates input name. If a value is given, then the coordinates input field appears below the map
		'coordsInputName' => ''
	];

	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->options['markerImage'] = \JURI::root() . ltrim($this->options['markerImage'], DIRECTORY_SEPARATOR);
	}

	/**
	 * Renders the widget
	 * 
	 * @return  string
	 */
	public function render()
	{
		self::loadMedia();

		return parent::render();
	}

	/**
	 * Loads media files
	 * 
	 * @return  void
	 */
	private function loadMedia()
	{
		\JHtml::stylesheet('https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.9.0/css/ol.css');
		\JHtml::script('https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.9.0/build/ol.js');
		
		$this->load_geocoder();

		if ($this->options['load_stylesheet'])
		{
			\JHtml::stylesheet('plg_system_nrframework/widgets/openstreetmap.css', ['relative' => true, 'version' => 'auto']);
		}

		\JHtml::script('plg_system_nrframework/widgets/openstreetmap.js', ['relative' => true, 'version' => 'auto']);
	}

	/**
	 * Checks whether geocoder is enabled and loads it.
	 * 
	 * @return  void
	 */
	private function load_geocoder()
	{
		if (!$this->options['showAddressInput'])
		{
			return;
		}

		$lang = \JFactory::getLanguage();
		$lang_tag = $lang->getTag();
		$doc = \JFactory::getDocument();
		$doc->addScriptOptions('nrf_osm_settings', [
			'lang_tag' => $lang_tag
		]);
		\JText::script('NR_OSM_ADDRESS_DESC');
		
		\JHtml::stylesheet('https://unpkg.com/ol-geocoder/dist/ol-geocoder.min.css');
		\JHtml::script('https://unpkg.com/ol-geocoder');
	
		$this->options['css_class'] .= ' geocoder';
	}
}