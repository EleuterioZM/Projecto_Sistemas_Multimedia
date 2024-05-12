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

class DateHelper
{
	/**
	 * Converts legacy date (strftime) format into the new (date) format
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function updateLegacyFormat($format)
	{
		$mapping = [
			// day
			'%a' => 'D', // 00, Sun through Sat
			'%A' => 'l', // 01, Sunday through Saturday
			'%d' => 'd', // 02, 01 through 31
			'%e' => 'j', // 03, 1 through 31
			'%j' => 'z', // 04, 001 through 366
			'%u' => 'N', // 05, 1 for Monday through 7 for Sunday
			'%w' => 'w', // 06, 1 for Sunday through 7 for Saturday

			// week
			'%U' => 'W', // 07, Week number of the year with Sunday as the start of the week
			'%V' => 'W', // 08, ISO-8601:1988 week number of the year with Monday as the start of the week, with at least 4 weekdays as the first week
			'%W' => 'W', // 09, Week number of the year with Monday as the start of the week

			// month
			'%b' => 'M', // 10, Jan through Dec
			'%B' => 'F', // 11, January through December
			'%h' => 'M', // 12, Jan through Dec, alias of %b
			'%m' => 'm', // 13, 01 for January through 12 for December

			// year
			'%C' => '', // 14, 2 digit of the century, year divided by 100, truncated to an integer, 19 for 20th Century
			'%g' => 'y', // 15, 2 digit of the year going by ISO-8601:1988 (%V), 09 for 2009
			'%G' => 'o', // 16, 4 digit version of %g
			'%y' => 'y', // 17, 2 digit of the year
			'%Y' => 'Y', // 18, 4 digit version of %y

			// time
			'%H' => 'H', // 19, hour, 00 through 23
			'%I' => 'h', // 20, hour, 01 through 12
			'%l' => 'g', // 21, hour, 1 through 12
			'%M' => 'i', // 22, minute, 00 through 59
			'%p' => 'A', // 23, AM or PM
			'%P' => 'a', // 24, am or pm
			'%r' => 'h:i:s A', // 25, = %I:%M:%S %p, 09:34:17 PM
			'%R' => 'H:i', // 26, = %H:%M, 21:34
			'%S' => 's', // 27, second, 00 through 59
			'%T' => 'H:i:s', // 28, = %H:%M:%S, 21:34:17
			'%X' => 'H:i:s', // 29, Based on locale without date
			'%z' => 'O', // 30, Either the time zone offset from UTC or the abbreviation (depends on operating system)
			'%Z' => 'T', // 31, The time zone offset/abbreviation option NOT given by %z (depends on operating system)

			// date stamps
			'%c' => 'Y-m-d H:i:s', // 32, Date and time stamps based on locale
			'%D' => 'm/d/y', // 33, = %m/%d/%y, 02/05/09
			'%F' => 'Y-m-d', // 34, = %Y-%m-%d, 2009-02-05
			'%s' => '', // 35, Unix timestamp, same as time()
			'%x' => 'Y-m-d', // 36, Date stamps based on locale

			// misc
			'%n' => '\n', // 37, New line character \n
			'%t' => '\t', // 38, Tab character \t
			'%%' => '%'  // 39, Literal percentage character %
		];

		foreach ($mapping as $index => $value) {
			$format = str_replace($index, $value, $format);
		}

		return $format;
	}

	/**
	 * Retrieve the current timezone's offset
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getOffset($numberOnly = false)
	{
		jimport('joomla.form.formfield');

		$user = \JFactory::getUser();
		$timezone = null;

		// Try to determine the offset based on the user
		if ($user->id != 0) {
			$timezone = $user->getParam('timezone');
		}

		// If user did not set any timezone, we try to retrieve it from Joomla
		if (!$timezone) {
			$timezone= \FH::jconfig()->get('offset');
		}

		// Timezone in string
		if (!$numberOnly) {
			return $timezone;
		}

		$timezone = new \DateTimeZone($timezone);
		$now = new \DateTime('now', $timezone);

		$offset = ($timezone->getOffset($now) / 60 / 60);

		return $offset;
	}
}
