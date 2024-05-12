<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

class JFormFieldGeoDBChecker extends JFormField
{
    /**
     * Whether TGeoIP is disabled or not.
     * 
     * @var  boolean
     */
    private $tgeoip_plugin_disabled = false;
    
    protected function getLabel()
    {
        return;
    }

	/**
	 * Renders the field.
	 *
	 * @param   array   $options
	 *
	 * @return  string
	 */
	public function renderField($options = [])
	{
        // Check if TGeoIP plugin is enabled
        if (!\NRFramework\Extension::pluginIsEnabled('tgeoip'))
        {
            $this->tgeoip_plugin_disabled = true;
            return parent::renderField($options);
        }
        
        // Do not render the field if the database is up-to-date
        if (!\NRFramework\Extension::geoPluginNeedsUpdate())
        {
            return;
        }

		return parent::renderField($options);
	}

    /**
     * Shows a warning message when the Geolocation plugin is disabled.
     * 
     * @return  string
     */
    private function disabledPluginWarning()
    {
        return '<div class="alert alert-warning geo-db-checker" style="margin-top: 0; margin-bottom: 0;">' .
            '<h3 style="margin-top:0;">' . JText::sprintf('NR_GEO_PLUGIN_DISABLED') . '</h3>' .
            '<p>' . JText::sprintf('NR_GEO_PLUGIN_DISABLED_DESC', '<a href="' . JRoute::_('index.php?option=com_plugins&view=plugins&filter' . (defined('nrJ4') ? '[search]' : '_search') . '=System - Tassos.gr GeoIP Plugin') . '" target="_blank">', '</a>') . '</p>' .
        '</div>';
    }

    /**
     * If the geolocation database is missing or its outdated, then display a helpful message
     * to the usser notifying them that they need to update.
     *
     * @return  string
     */
    protected function getInput()
    {
        if ($this->tgeoip_plugin_disabled)
        {
            return $this->disabledPluginWarning();
        }
        
        return '<div class="alert alert-info" style="margin: 0;">' .
            '<h3 style="margin-top:0;">' . JText::sprintf('NR_GEO_MAINTENANCE') . '</h3>' .
            '<p>' . JText::sprintf('NR_GEO_MAINTENANCE_DESC') . '</p>' .
            '<a class="btn btn-success" data-toggle="modal" data-bs-toggle="modal" href="#tf-geodbchecker-modal"><span class="icon-refresh"></span> Update Database</a>' .
        '</div>';
    }
}