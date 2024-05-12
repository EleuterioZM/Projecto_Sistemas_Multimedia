<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldNR_Geo extends NRFormFieldList
{
	private $list;

	protected function getInput()
	{
		if ($this->get('detect_visitor_country', false) && empty($this->value) && $countryCode = $this->getVisitorCountryCode())
		{
			$this->value = $countryCode;
		}

		return parent::getInput();
	}

	protected function getOptions()
	{
		switch ($this->get('geo'))
		{
			case 'continents':
				$this->list = \NRFramework\Continents::getContinentsList();
				$selectLabel = 'NR_SELECT_CONTINENT';
				break;
            default:
				$this->list = \NRFramework\Countries::getCountriesList();
				$selectLabel = 'NR_SELECT_COUNTRY';
				break;
		}

		if ($this->get('use_label_as_value', false))
		{
			$this->list = array_combine($this->list, $this->list);
		}

		$options = array();

		if ($this->get("showselect", 'true') === 'true')
		{
			$options[] = JHTML::_('select.option', "", "- " . JText::_($selectLabel) . " -");
		}

		foreach ($this->list as $key => $value)
		{
			$options[] = JHTML::_('select.option', $key, $value);
		}

		return array_merge(parent::getOptions(), $options);
	}

    /**
     *  Detect visitor's country
     *
     *  @return  string   The visitor's country code (GR)
     */
    private function getVisitorCountryCode()
    {
    	$path = JPATH_PLUGINS . '/system/tgeoip/';

    	if (!\JFolder::exists($path))
    	{
    		return;
    	}

    	if (!class_exists('TGeoIP'))
    	{
        	@include_once $path . 'vendor/autoload.php';
        	@include_once $path . 'helper/tgeoip.php';
    	}

        $geo = new \TGeoIP();
        return $geo->getCountryCode();
    }
}