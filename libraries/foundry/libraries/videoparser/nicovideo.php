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

class VideoParserNicoVideo extends VideoParserBase
{
	private function getCode($url)
	{
		preg_match('/nicovideo.jp\/watch\/(.*)/is', $url, $matches);

		if (!empty($matches)) {
			return $matches[1];
		}
		
		return false;
	}

	/**
	 * Renders the HTML for Nicovideo embedded videos
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

		return '<div class="o-aspect-ratio" style="--aspect-ratio: 16/9; --max-width: ' . $this->getWidth() . '"><script type="text/javascript" src="https://ext.nicovideo.jp/thumb_watch/' . $code . '?w=490&h=307&n=1"></script><noscript>Javascript is required to load the player</noscript></div>';
	}
}