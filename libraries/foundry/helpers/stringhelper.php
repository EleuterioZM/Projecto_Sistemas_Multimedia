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
namespace Foundry\Helpers;

defined('_JEXEC') or die('Unauthorized Access');

class StringHelper
{
	/**
	 * Generates a random word
	 *
	 * @since	1.1
	 * @access	public
	 */
	public static function generateRandomWord($length)
	{
		$string = '';
		$vowels = ["a","e","i","o","u"];

		$consonants = [
			'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
			'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
		];

		// Seed it
		srand((double) microtime() * 1000000);

		$max = $length / 2;

		for ($i = 1; $i <= $max; $i++) {
			$string .= $consonants[rand(0,19)];
			$string .= $vowels[rand(0,4)];
		}

		return $string;
	}

	/**
	 * Force a set of string to be UTF8
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function forceUTF8($text)
	{
		if (is_array($text)) {

			foreach($text as $k => $v) {
				$text[$k] = $this->forceUTF8($v);
			}

			return $text;
		}

		$max = strlen($text);
		$buf = "";

		for ($i = 0; $i < $max; $i++) {
			$c1 = $text[$i];
			if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
			  $c2 = $i+1 >= $max? "\x00" : $text[$i+1];
			  $c3 = $i+2 >= $max? "\x00" : $text[$i+2];
			  $c4 = $i+3 >= $max? "\x00" : $text[$i+3];
				if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2;
						$i++;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
					if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
						$buf .= $c1 . $c2 . $c3;
						$i = $i + 2;
					} else { //not valid UTF8.  Convert it.
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$buf .= $cc1 . $cc2;
					}
				} else { //doesn't look like UTF8, but should be converted
						$cc1 = (chr(ord($c1) / 64) | "\xc0");
						$cc2 = (($c1 & "\x3f") | "\x80");
						$buf .= $cc1 . $cc2;
				}
			} elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
					$cc1 = (chr(ord($c1) / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$buf .= $cc1 . $cc2;
			} else { // it doesn't need convesion
				$buf .= $c1;
			}
		}
		return $buf;
	}

	/**
	 * Determines if a given string is in ascii format
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public static function isAscii($text)
	{
		return (preg_match('/(?:[^\x00-\x7F])/', $text) !== 1);
	}

	/**
	 * Determines if the content provided contains any blocked word
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function hasBlockedWords($blockedWords, $content)
	{
		if (empty($blockedWords) || !$blockedWords) {
			return false;
		}

		if (is_string($blockedWords)) {
			$blockedWords = explode(',', $blockedWords);
		}

		foreach ($blockedWords as $word) {
			if (preg_match('/\b'.$word.'\b/i', $content)) {
				// Stop the checking and return the matched blocked word since we now know that there's at least 1 in the content
				return $word;
			}
		}

		return false;
	}
}