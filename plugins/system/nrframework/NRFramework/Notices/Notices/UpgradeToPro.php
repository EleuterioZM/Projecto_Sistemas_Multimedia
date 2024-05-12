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

class UpgradeToPro extends Notice
{
	/**
	 * Define how old (in days) the extension needs to be since the installation date
	 * in order to display this notice.
	 * 
	 * @var  int
	 */
	private $upgrade_to_pro_notice_days_old = 30;

	protected $notice_payload = [
		'type' => 'success',
		'class' => 'upgradeToPro'
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);

		$this->payload['tooltip'] = Text::_('NR_NOTICE_UPGRADE_TO_PRO_TOOLTIP');
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return sprintf(Text::_('NR_UPGRADE_TO_PRO_X_OFF'), 20);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return sprintf(Text::_('NR_UPGRADE_TO_PRO_NOTICE_DESC'), $this->extension_name);
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = Extension::getTassosExtensionUpgradeURL($this->payload['ext_xml'], 'FREE2PRO', false);
		
		return '<a href="' . \NRFramework\Functions::getUTMURL($url, 'UserNotice', 'UpgradeToPro') . '" target="_blank" class="tf-notice-btn success">' . Text::_('NR_UPGRADE_NOW') . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, its been hidden
		if ((new \NRFramework\Factory())->getCookie('tfNoticeHideUpgradeToProNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		// If its already Pro, abort
		if (Extension::isPro($this->payload['ext_xml']))
		{
			return false;
		}

		// Get extension installation date
		if (!$install_date = Extension::getInstallationDate($this->payload['ext_element']))
		{
			return false;
		}

		// If the extension is not old enough, do not show the rate notice
		if ($this->getDaysDifference(time(), strtotime($install_date)) < $this->upgrade_to_pro_notice_days_old)
		{
			return false;
		}

		return true;
	}
}