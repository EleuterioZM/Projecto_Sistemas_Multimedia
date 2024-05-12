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

class VideoParserVimeo extends VideoParserBase
{
	private function getCode($url)
	{
		preg_match('/vimeo.com\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}
		
		return false;
	}

	/**
	 * Renders the HTML for Vimeo embedded videos
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function getHtml($url, $amp = false)
	{
		$code = $this->getCode($url);

		if (!$code) {
			return false;
		}

		if ($amp) {
			return '<amp-vimeo data-videoid="' . $code . '" layout="responsive" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '"></amp-vimeo>';
		}
		$html = '<div class="o-aspect-ratio" style="--aspect-ratio: 16/9; --max-width:' . $this->getWidth() . ';">';
		$html .= '<iframe title="Vimeo video player" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" src="https://player.vimeo.com/video/' . $code . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		$html .= '</div>';

		return $html;
	}
}