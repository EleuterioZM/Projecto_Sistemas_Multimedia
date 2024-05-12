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

class Rate extends Notice
{
	/**
	 * Define how old (in days) the extension needs to be since the installation date
	 * in order to display this notice.
	 * 
	 * @var  int
	 */
	private $rate_notice_days_old = 10;

	protected $notice_payload = [
		'type' => 'info',
		'class' => 'rate'
	];

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return sprintf(Text::_('NR_RATE'), $this->extension_name);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return sprintf(Text::_('NR_RATE_NOTICE_EXTENSION_DESC'), $this->extension_name);
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		return '<a href="#" class="tf-rate-already-rated">' . Text::_('NR_I_ALREADY_DID') . '</a>
					<a href="' . Extension::getExtensionJEDURL($this->payload['ext_xml']) . '#reviews" target="_blank" class="tf-notice-btn info">' . Text::_('NR_WRITE_A_REVIEW') . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, it's already hidden
		if ($this->factory->getCookie('tfNoticeHideRateNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		// Get extension installation date
		if (!$install_date = Extension::getInstallationDate($this->payload['ext_element']))
		{
			return false;
		}

		// If the extension is not old enough, do not show the rate notice
		if ($this->getDaysDifference(time(), strtotime($install_date)) < $this->rate_notice_days_old)
		{
			return false;
		}

		return true;
	}
}