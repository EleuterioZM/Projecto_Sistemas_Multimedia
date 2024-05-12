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

class OembedAdapterPastebin extends OembedAdapter
{
	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'pastebin.com') === false) {
			return false;
		}

		return true;
	}

	public function process(&$result)
	{
		// Check if the url should be processed here.
		if (stristr($this->url, 'pastebin.com') === false) {
			return;
		}

		$oembed = $this->getOembedData();

		if (!$oembed) {
			$url = str_ireplace(['http://pastebin.com/', 'https://pastebin.com/'], '', $this->url);

			$oembed = (object) [
				'html' => '<iframe src="https://pastebin.com/embed_iframe.php?i=' . $url . '" style="border:none;width:100%"></iframe>'
			];
		}

		$result->oembed = $oembed;
	}
}
