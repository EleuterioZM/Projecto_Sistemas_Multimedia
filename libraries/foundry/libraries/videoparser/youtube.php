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

class VideoParserYoutube extends VideoParserBase
{
	private function getCode($url)
	{
		// Check if the url should be processed here.
		if (stristr($url, 'youtube.com') === false && stristr($url, 'youtu.be') === false) {
			return false;
		}

		// if this URL is youtu.be
		preg_match('/youtu.be\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}

		// only process this if the URL is youtube.com
		parse_str(parse_url($url, PHP_URL_QUERY), $data);

		if (!$data) {
			return false;
		}

		return $data;
	}

	/**
	 * Renders the HTML for youtube embedded videos
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public function getHtml($url, $amp = false)
	{
		$html = false;
		$listId = '';

		// Contain a list of video parameter query string
		$data = $this->getCode($url);

		$videoId = $data;

		if ($data && (isset($data['v']) && $data['v'])) {
			$videoId = $data['v'];

			// Some of the YouTube video contain the list parameter query string
			$list = FH::normalize($data, 'list', '');

			if ($list) {
				$listId = '/' . $list;
			}
		}

		if ($videoId) {
			$html = '<div class="o-aspect-ratio" style="--aspect-ratio: 16/9; --max-width:' . $this->getWidth() . ';">';
			$html .= '<iframe title="YouTube video player" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" src="//www.youtube.com/embed/' . $videoId . $listId . '?wmode=transparent" frameborder="0" allowfullscreen></iframe>';
			$html .= '</div>';
		}

		if ($videoId && $amp) {
			$html = '<amp-youtube data-videoid="' . $videoId . '" layout="responsive" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '"></amp-youtube>';
		}

		// this video do not have a code. so include the url directly.
		if (!$videoId) {
			$html = '<div class="o-aspect-ratio" style="--aspect-ratio: 16/9; --max-width:' . $this->getWidth() . ';">';
			$html .= '<iframe title="YouTube video player" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" src="' . $url . '&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
			$html .= '</div>';
		}

		if (!$videoId && $amp) {
			$html = '<amp-iframe src="' . $url . '" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" frameborder="0" layout="responsive" sandbox="allow-scripts allow-same-origin"></amp-iframe>';
		}

		return $html;
	}
}
