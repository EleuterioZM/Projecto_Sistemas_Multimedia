<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Notices\Notices;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use \NRFramework\Extension;

class Geolocation extends Notice
{
	protected $notice_payload = [
		'type' => 'warning',
		'class' => 'geolocation'
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);

		\NRFramework\Functions::loadLanguage('plg_system_tgeoip');
	}
	
	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return Text::_('PLG_SYSTEM_TGEOIP_MAINTENANCE');
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return sprintf(Text::_('NR_NOTICE_GEO_MAINTENANCE_DESC'), $this->extension_name);
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = \JURI::base() . 'index.php?option=com_ajax&format=raw&plugin=tgeoip&task=update-red&' . \JSession::getFormToken() . '=1&return=' . base64_encode($this->payload['current_url']);

		return '<a href="' . $url . '" class="tf-notice-btn info">' . Text::_('NR_UPDATE_NOW') . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, its been hidden
		if ($this->factory->getCookie('tfNoticeHideGeolocationNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		if (!Extension::geoPluginNeedsUpdate())
		{
			return false;
		}

		return true;
	}
}