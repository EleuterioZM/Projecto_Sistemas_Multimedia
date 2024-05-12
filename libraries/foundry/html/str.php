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
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\String\StringHelper;
use Foundry\Libraries\Date;
use Foundry\Libraries\Scripts;

class Str extends Base
{
	/**
	 * Formats a given date string with a given date format
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function date($timestamp, $format = 'DATE_FORMAT_LC2', $withOffset = true)
	{
		$date = new Date($timestamp, $withOffset);

		$string = $date->format(\JText::_($format));

		return $string;
	}

	/**
	 * Escapes a string
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function escape($str)
	{
		return \FH::escape($str);
	}

	/**
	 * Truncates a string to a specific length specified by caller
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function truncate($text, $max = 250, $ellipses = '...', $options = [])
	{
		Scripts::load('shared');

		$stripTags = \FH::normalize($options, 'stripTags', false);
		$exact = \FH::normalize($options, 'exact', false);
		$showMore = \FH::normalize($options, 'showMore', true);
		$overrideReadmore = \FH::normalize($options, 'overrideReadmore', false);
		$readMoreText = \FH::normalize($options, 'readMoreText', 'FD_TRUNCATE_MORE');

		// If the plain text is shorter than the maximum length, return the whole text
		if ((StringHelper::strlen(preg_replace('/<.*?>/', '', $text)) <= $max) || !$max) {
			return $text;
		}

		// Truncate the string natively without retaining the original format.
		if ($stripTags) {
			$truncate = StringHelper::trim(strip_tags($text));
			$truncate = StringHelper::substr($truncate, 0, $max) . $ellipses;
		}

		if (!$stripTags) {
			$truncate = \FH::truncateWithHtml($text, $max, $ellipses, $exact);
		}

		$theme = $this->getTemplate();
		$theme->set('readMoreText', $readMoreText);
		$theme->set('truncated', $truncate);
		$theme->set('original', $text);
		$theme->set('showMore', $showMore);
		$theme->set('overrideReadmore', $overrideReadmore);

		$output = $theme->output('html/string/truncate');

		return $output;
	}
}
