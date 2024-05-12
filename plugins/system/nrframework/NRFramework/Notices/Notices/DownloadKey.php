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
use \NRFramework\Functions;

class DownloadKey extends Notice
{
	protected $notice_payload = [
		'type' => 'error',
		'class' => 'download-key',
		'dismissible' => false,
		'download_key' => null,
		'state' => null
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);
	
		$this->payload['download_key'] = Functions::getDownloadKey();
		$this->payload['state'] = isset($this->payload['license_data']['state']) ? $this->payload['license_data']['state'] : null;
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		$text = !empty($this->payload['download_key']) || ($this->payload['state'] && in_array($this->payload['state'], ['invalid_key'])) ? TEXT::_('NR_IS_INVALID') : Text::_('NR_IS_MISSING');
		return sprintf(Text::_('NR_DOWNLOAD_KEY_TEXT'), $text);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		$text = !empty($this->payload['download_key']) || ($this->payload['state'] && in_array($this->payload['state'], ['invalid_key'])) ? TEXT::_('NR_A_VALID') : Text::_('NR_YOUR');
		return sprintf(Text::_('NR_DOWNLOAD_KEY_MISSING_DESC'), $this->extension_name, $text);
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = 'https://www.tassos.gr/kb/general/how-to-activate-your-pro-version';
		
		return '<input type="text" class="tf-notice-download-key" value="' . htmlspecialchars($this->payload['download_key']) . '" placeholder="' . Text::_('NR_ENTER_YOUR_DOWNLOAD_KEY') . '" />
					<a href="#" class="tf-notice-download-key-btn tf-notice-btn info">' . Text::_('JAPPLY') . '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15" height="15" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
					<circle cx="50" cy="50" fill="none" stroke="currentColor" stroke-width="8" r="38" stroke-dasharray="179.0707812546182 61.690260418206066">
					  <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
					</circle>
					</svg></a>
					<a href="' . Functions::getUTMURL($url, 'UserNotice', 'DownloadKey') . '" target="_blank">' . Text::_('JHELP') . '</a>';
	}
	
	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// Ensure customer is using the Pro version
		if (!\NRFramework\Extension::isPro($this->payload['ext_xml']))
		{
			return false;
		}

		// If user is Pro but has no license details, show it
		if (!$details = \NRFramework\Notices\Helper::getExtensionDetails($this->payload['license_data'], $this->payload['ext_element']))
		{
			return true;
		}

		// If state exists and key is invalid/or no subscriptions exist, return true
		if ($this->payload['state'] && in_array($this->payload['state'], ['missing_key', 'invalid_key']))
		{
			return true;
		}
		
		if (!empty($this->payload['download_key']))
		{
			return false;
		}

		return true;
	}
}