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
defined('_JEXEC') or die('Unauthorized Access');

class VideoParserSmule extends VideoParserBase
{
	/**
	 * Renders the HTML for Facebook embedded videos
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function getHtml($url, $amp = false)
	{
		$html = '<div class="o-aspect-ratio" style="--aspect-ratio: 1/1; --max-width:' . $this->getWidth() . ';">';
		$html .= '<iframe title="Smule Video Player" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" src="' . $url . '/frame/box" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		$html .= '</div>';

		if ($amp) {
			$html = '<amp-iframe src="' . $url . '" width="300" height="300" frameborder="0" layout="responsive" sandbox="allow-scripts allow-same-origin"></amp-iframe>';
		}

		return $html;
	}
}