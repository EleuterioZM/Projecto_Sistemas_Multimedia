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
use \NRFramework\Extension;

class Expired extends Notice
{
	protected $notice_payload = [
		'type' => 'error',
		'class' => 'expired',
		'expired_at' => '',
		'plan' => ''
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);
		
		$this->payload['tooltip'] = Text::_('NR_NOTICE_EXPIRED_TOOLTIP');
		$this->payload['expired_at'] = isset($payload['expired_at']) ? $payload['expired_at'] : false;
		$this->payload['plan'] = isset($payload['plan']) ? $payload['plan'] : false;
	}

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return sprintf(Text::_('NR_SUBSCRIPTION_EXPIRED'), $this->extension_name);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		$title = strtolower($this->payload['plan']) === 'bundle' ? $this->payload['plan'] : $this->extension_name . ' ' . $this->payload['plan'];
		
		return sprintf(Text::_('NR_SUBSCRIPTION_EXPIRED_DESC'), $title, Functions::applySiteTimezoneToDate($this->payload['expired_at'], 'd M o'));
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = 'https://tassos.gr/subscriptions';
		
		return '<a href="' . Functions::getUTMURL($url, 'UserNotice', 'SubscriptionExpired') . '" target="_blank" class="tf-notice-btn info">' . sprintf(Text::_('NR_RENEW_X_PERCENT_OFF'), 20) . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, it's already hidden
		if ($this->factory->getCookie('tfNoticeHideExpiredNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		// The date the extension expired.
		if (!$this->payload['expired_at'])
		{
			return false;
		}

		return true;
	}
}