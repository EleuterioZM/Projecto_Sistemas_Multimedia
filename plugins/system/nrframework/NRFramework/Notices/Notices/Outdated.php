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

class Outdated extends Notice
{
	/**
	 * How old the extension needs to be to be defined as "outdated".
	 * 
	 * @var  int
	 */
	private $oudated_notice_days_old = 120;

	protected $notice_payload = [
		'type' => 'warning',
		'class' => 'outdated'
	];

	/**
	 * Notice title.
	 * 
	 * @return  string
	 */
	protected function getTitle()
	{
		return sprintf(Text::_('NR_EXTENSION_IS_OUTDATED'), $this->extension_name);
	}

	/**
	 * Notice description.
	 * 
	 * @return  string
	 */
	protected function getDescription()
	{
		return sprintf(Text::_('NR_OUTDATED_EXTENSION'), $this->extension_name, $this->oudated_notice_days_old);
	}
	
	/**
	 * Notice actions.
	 * 
	 * @return  string
	 */
	protected function getActions()
	{
		return '<a href="' . \JURI::base() . 'index.php?option=com_installer&task=update.find&' . \JSession::getFormToken() . '=1" class="tf-notice-btn info">' . Text::_('NR_UPDATE_NOW') . '</a>';
	}
	
	/**
	 * Whether the notice can run.
	 * 
	 * @return  string
	 */
	protected function canRun()
	{
		// If cookie exists, its been hidden
		if ($this->factory->getCookie('tfNoticeHideOutdatedNotice_' . $this->payload['ext_element']) === 'true')
		{
			return false;
		}

		if (!Extension::isOutdated($this->payload['ext_element'], $this->oudated_notice_days_old))
		{
			return false;
		}

		return true;
	}
}