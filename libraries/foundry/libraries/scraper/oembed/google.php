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

class OembedAdapterGoogle extends OembedAdapter
{
	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'drive.google.com') === false) {
			return false;
		}

		return true;
	}
	
	public function process(&$result)
	{
		$oembed = $this->getOembed();

		// If we can't get any oembed data, We try to find embed content instead
		if (!$oembed) {
			$oembed = $this->getEmbedContent();
		}

		if (!$oembed) {
			return;
		}

		// Fix http url in https issue
		$oembed = $this->fixOembedUrl($oembed);

		$result->oembed = $oembed;
	}

	/**
	 * Get the embed from the content
	 *
	 * @since	3.2
	 * @access	public
	 */
	public function getEmbedContent()
	{
		$oembed = new stdClass();
		$oembed->html = '';

		preg_match('/^(https:\/\/drive\.google\.com\/)file\/d\/([^\/]+)\/.*$/i', $this->url, $matches);

		if (!empty($matches)) {
			$code = $matches[2];

			if ($code) {
				$oembed->html = '<iframe width="640" height="360" src="https://drive.google.com/file/d/' . $code . '/preview' . '" frameborder="0" allowfullscreen></iframe>';
			}
		}

		// Try get the thumbnail
		$items = $this->parser->find('meta[property=og:image]');

		foreach ($items as $meta) {

			if (!$meta->content) {
				continue;
			}

			$url = $meta->content;

			if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
				$url = 'http://' . $url;
			}

			$oembed->thumbnail = $url;
		}

		return $oembed;
	}
}
