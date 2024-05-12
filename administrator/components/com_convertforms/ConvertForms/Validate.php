<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

// No direct access to this file
defined('_JEXEC') or die;

class Validate
{
	/**
	 *  Check if given email address is valid
	 *
	 *  @param   String  $email  The email address to check
	 *
	 *  @return  Boolean         Return true if the email address is valid
	 */
	public static function email($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 *  Check DNS records corresponding to a given email address
	 *
	 *  @param   String  $email  The email address to check
	 *
	 *  @return  Boolean         Return true if the email address has valid MX records
	 */
	public static function emaildns($email)
	{
		// Check if it's an email address format
		if (!self::email($email))
		{
			return false;
		}

		list($user, $domain) = explode('@', $email, 2);
		
		// checkdnsrr for PHP < 5.3.0
		if (!function_exists('checkdnsrr') && function_exists('exec') && is_callable('exec'))
		{
			@exec('nslookup -type=MX ' . escapeshellcmd($domain), $output);

			foreach($output as $line)
			{
				if (preg_match('/^' . preg_quote($domain) . '/', $line))
				{
					return true;
				}
			}
			
			return false;
		}

		// fallback method...
		if (!function_exists('checkdnsrr') || !is_callable('checkdnsrr'))
		{
			return true;
		}

		return checkdnsrr($domain, substr(PHP_OS, 0, 3) == 'WIN' ? 'A' : 'MX');
	}

	/**
	 *  Validates the given date. 
	 * 
	 *  Note: This method is limited to validate only english-based dates 
	 *  as the Date PHP class doesn't respect current locale without using hacks
	 *
	 *  @param   String  $date    The date string
	 *  @param   String  $format  The date format to be used
	 *
	 *  @return  boolean
	 */
	public static function dateFormat($date, $format = null)
	{
		return \JDate::createFromFormat($format, trim($date));
	}

	/**
	 * Validates URL
	 *
	 * @param	string	$url
	 *
	 * @return	void
	 */
	public static function url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}
}