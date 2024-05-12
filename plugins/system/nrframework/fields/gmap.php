<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access to this file
defined('_JEXEC') or die;

use NRFramework\HTML;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNR_Gmap extends NRFormField
{
	/**
	 *  The default Google Maps API Key
	 *
	 *  @var  string
	 */
	public $defaultAPIKey = 'AIzaSyAPgVu1A9L7_q0gtYToFeJiUHDSCCXYZKI';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		// Setup properties
		$this->width    = $this->get('width', '500px');
		$this->height   = $this->get('height', '400px');
		$this->zoom     = $this->get('zoom', '10');
		$this->margin   = $this->get('margin', '0 0 10px 0');
		$this->readonly = $this->get('readonly', false) ? 'readonly' : '';
		$this->value    = $this->checkCoordinates($this->value, null) ? $this->value : $this->get('default', '36.892587, 27.287793');
		$this->hint     = $this->prepareText($this->get('hint', 'NR_ENTER_COORDINATES'));

		// Add scripts to DOM
		JHtml::_('jquery.framework');
		JFactory::getDocument()->addScript('//maps.googleapis.com/maps/api/js?key=' . $this->getAPIKey());
		HTML::script('plg_system_nrframework/field.gmap.js');
		Jtext::script('NR_WRONG_COORDINATES');

		// Add styles to DOM
		$this->doc->addStyleDeclaration('
			#' . $this->id . '_map { 
				height: ' . $this->height . ';
				width:  ' . $this->width  . ';
				margin: ' . $this->margin . ';
			}
		');

		return '<div id="' . $this->id . '_map"></div><input type="text" name="' . $this->name . '" class="form-control ' . $this->class . ' nr_gmap" id="' . $this->id . '" value="' . $this->value . '" placeholder="' . $this->hint . '" data-zoom="' . $this->zoom . '" ' . $this->readonly . '/>';
	}

	/**
	 * Checks the validity of the coordinates
	 */
	private function checkCoordinates($coordinates)
	{
		return (preg_match("/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/", $coordinates));
	}

	/**
	 *  Get the Google Maps API Key. 
	 *  If no key is found in the framework options then the default API Key will be used instead.
	 *  
	 *  We should update the default API Key once a while.
	 *
	 *  @return  string
	 */
	private function getAPIKey()
	{
		if (!$framework = JPluginHelper::getPlugin('system', 'nrframework'))
		{
			return $this->defaultAPIKey;
		}
		
		// Get plugin params
		$params = new JRegistry($framework->params);
		return $params->get('gmapkey', $this->defaultAPIKey);
	}
}