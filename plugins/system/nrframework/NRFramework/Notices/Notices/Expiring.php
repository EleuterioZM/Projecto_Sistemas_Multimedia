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

class Expiring extends Notice
{
	protected $notice_payload = [
		'type' => 'warning',
		'class' => 'expiring',
		'expires_in' => '',
		'plan' => ''
	];

	public function __construct($payload = [])
	{
		parent::__construct($payload);

		$this->payload['tooltip'] = Text::_('NR_NOTICE_EXPIRING_TOOLTIP');
		$this->payload['expires_in'] = isset($this->payload['expires_in']) ? $this->payload['expires_in'] : false;
		$this->payload['plan'] = isset($payload['plan']) ? $payload['plan'] : false;
	}

	/**
	 * Define the remaining days the subscription must have to display the expiring subscription notice.
	 * 
	 * @var  int
	 */
	private $expiring_notice_days = 30;

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return sprintf(Text::_('NR_SUBSCRIPTION_EXPIRING'), $this->extension_name);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		$title = strtolower($this->payload['plan']) === 'bundle' ? $this->payload['plan'] : $this->extension_name . ' ' . $this->payload['plan'];

		return sprintf(Text::_('NR_SUBSCRIPTION_EXPIRING_DESC'), $title, Functions::applySiteTimezoneToDate($this->payload['expires_in'], 'd M o'));
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		$url = 'https://tassos.gr/subscriptions';
		
		return '<a href="' . Functions::getUTMURL($url, 'UserNotice', 'SubscriptionExpiring') . '" target="_blank" class="tf-notice-btn info">' . sprintf(Text::_('NR_RENEW_X_PERCENT_OFF'), 30) . '</a>';
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, it's already hidden
		if ($this->factory->getCookie('tfNoticeHideExpiringNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		// The date the extension expires.
		if (!$this->payload['expires_in'])
		{
			return false;
		}
		
		// The days difference criteria must be met
		if ($this->getDaysDifference(strtotime($this->payload['expires_in']), time()) > $this->expiring_notice_days)
		{
			return false;
		}

		return true;
	}
}