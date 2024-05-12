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

class UpgradeToBundle extends Notice
{
	/**
	 * Define how old (in days) the extension needs to be since the installation date
	 * in order to display this notice.
	 * 
	 * @var  int
	 */
	private $upgrade_to_bundle_notice_days_old = 60;

	protected $notice_payload = [
		'type' => 'success',
		'class' => 'upgradeToBundle'
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);

		$this->payload['tooltip'] = Text::_('NR_NOTICE_UPGRADE_TO_BUNDLE_TOOLTIP');
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return Text::_('NR_UPGRADE_TO_BUNDLE');
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return Text::_('NR_UPGRADE_TO_BUNDLE_NOTICE_DESC');
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = 'https://www.tassos.gr/subscriptions';

		return '<a href="' . \NRFramework\Functions::getUTMURL($url, 'UserNotice', 'UpgradeToBundle') . '" target="_blank" class="tf-notice-btn success">' . Text::_('NR_UPGRADE_NOW') . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, its been hidden
		if ((new \NRFramework\Factory())->getCookie('tfNoticeHideUpgradeToBundleNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		// Get license details for this extension
		if ($details = \NRFramework\Notices\Helper::getExtensionDetails($this->payload['license_data'], $this->payload['ext_element']))
		{
			// If we already have an active bundle plan, abort
			if (isset($details['active']) && isset($details['plan']) && $details['active'] && strtolower($details['plan']) === 'bundle')
			{
				return false;
			}
		}

		// The user must have at least 2 installed tassos.gr extensions
		if (Extension::getTotalInstalledExtensions() < 2)
		{
			return false;
		}

		// User must have at least 1 paid subscription
		if (Extension::getUserTotalPaidPlans($this->payload['license_data']) < 1)
		{
			return false;
		}

		// Get extension installation date
		if (!$install_date = Extension::getInstallationDate($this->payload['ext_element']))
		{
			return false;
		}

		// If the extension is not old enough, do not show the rate notice
		if ($this->getDaysDifference(time(), strtotime($install_date)) < $this->upgrade_to_bundle_notice_days_old)
		{
			return false;
		}

		return true;
	}
}