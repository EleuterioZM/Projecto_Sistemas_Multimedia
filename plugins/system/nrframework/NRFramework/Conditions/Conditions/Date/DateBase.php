<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Date;

defined('_JEXEC') or die;

use NRFramework\Conditions\Condition;

class DateBase extends Condition
{
	/**
	 * Server's Timezone
	 *
	 * @var DateTimeZone
	 */
	protected $tz;

	/**
	 * If set to True, dates will be constructed with modified offset based on the passed timezone
	 *
	 * @var Boolean
	 */
	protected $modify_offset = true;

	/**
	 *  Class constructor
	 *
	 *  @param  object  $assignment
	 */
	public function __construct($assignment = null, $factory = null)
	{
		parent::__construct($assignment, $factory);

		// Set timezone
		if ($timezone = $this->params->get('timezone'))
		{
			$this->tz = new \DateTimeZone($timezone);
		}
		else
		{
			$this->tz = new \DateTimeZone($this->app->getCfg('offset', 'GMT'));
		}

		// Set modify offset switch
		$this->modify_offset = $this->params->get('modify_offset', true);

		// Set now date
		$now = $this->params->get('now', 'now');
		$this->date = $this->getDate($now);
	}

	/**
	 * Checks if the current datetime is between the specified range
	 *
	 * @param JDate &$up_date
	 * @param JDate &$down_date
	 * 
	 * @return bool
	 */
	protected function checkRange(&$up_date, &$down_date)
	{
        if (!$up_date && !$down_date)
        {
            return false;
		}
 
		$now = $this->date->getTimestamp();

		if (((bool)$up_date   && $up_date->getTimestamp() > $now) ||
			((bool)$down_date && $down_date->getTimestamp() < $now))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create a date object based on the given string and apply timezone.
	 *
	 * @param  String $date
	 *
	 * @return void
	 */
	protected function getDate($date = 'now')
	{
		// Fix the date string
		\NRFramework\Functions::fixDate($date);

		if ($this->modify_offset)
		{
			// Create date, set timezone and modify offset
			$date = $this->factory->getDate($date)->setTimeZone($this->tz);
		} else 
		{
			// Create date and set timezone without modifyig offset
			$date = $this->factory->getDate($date, $this->tz);
		}

		return $date;
	}
}