<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\DateHelper;

class Date
{
	private $date = null;

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->date, $method), $args);
	}

	public function __construct($current = '', $withTimezone = null)
	{
		$this->date = \JFactory::getDate($current);

		if ($withTimezone) {
			$this->setTimezone();
		}
	}
	
	/**
	 * Handle our own format behavior
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function format($format = 'Y-m-d H:i:s', $local = true, $translate = true, $withJText = true)
	{
		if ($withJText) {
			$format = \JText::_($format);
		}

		// Detect for legacy string formats
		// Legacy strings that uses % format, we need to fix it accordingly
		$legacy	= stristr($format, '%') !== false;

		if ($legacy) {
			$format = DateHelper::updateLegacyFormat($format);
		}

		return $this->date->format($format, $local, $translate);
	}

	/**
	 * Retrieves the timezone currently being used in Joomla
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getTimezone()
	{
		$timezone = new \DateTimeZone(DateHelper::getOffset());

		return $timezone;
	}

	/**
	 * Support backwards compatibility
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOffset($numberOnly = false)
	{
		return \DateHelper::getOffset($numberOnly);
	}

	/**
	 * Return the jdate with the correct specified timezone offset
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function dateWithOffSet($str = '')
	{
		$date = new \Date($str, true);

		return $date;
	}

	public function getDate($str = '')
	{
		return self::dateWithOffSet($str);
	}

	/**
	 * Set the timezone on the date
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setTimezone($timezone = null)
	{
		// If timezone isn't specified, we should generate one based on Joomla's timezone
		if (is_null($timezone)) {
			$timezone = $this->getTimezone();
		}

		return $this->date->setTimezone($timezone);
	}

	/**
	 * Backward compatibility purposes only. Should not be used at all
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function toMySQL($local = false)
	{
		return $this->toSql($local);
	}

	/**
	 * Returns the lapsed time since the current time
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function toLapsed()
	{
		$now = new Date();
		$time = $now->date->toUnix(true) - $this->date->toUnix(true);

		$tokens = [
			31536000 => 'FD_LAPSED_YEAR',
			2592000 => 'FD_LAPSED_MONTH',
			604800 => 'FD_LAPSED_WEEK',
			86400 => 'FD_LAPSED_DAY',
			3600 => 'FD_LAPSED_HOUR',
			60 => 'FD_LAPSED_MINUTE',
			1 => 'FD_LAPSED_SECOND'
		];
		
		if ($time == 0) {
			return \JText::_('FD_LAPSED_NOW');
		}

		foreach ($tokens as $unit => $key) {
			
			if ($time < $unit) {
				continue;
			}

			$units = floor($time / $unit);

			$string = $units > 1 ?  $key . '_PLURAL' : $key;

			$text = \JText::sprintf(strtoupper($string), $units);
			
			return $text;
		}

		return \JText::_('FD_LAPSED_A_SECOND_AGO');
	}

}
