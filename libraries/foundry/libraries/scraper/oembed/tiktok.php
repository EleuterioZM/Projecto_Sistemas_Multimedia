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

class OembedAdapterTikTok extends OembedAdapter
{
	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'tiktok.com') === false) {
			return false;
		}

		return true;
	}

	public function process(&$result)
	{
		$oembed = $this->getOembedData();

		// Fix http url in https issue
		$oembed = $this->fixOembedUrl($oembed);

		// If we can't get any oembed data from youtube, we will then simulate this.
		if (!$oembed) {
			return;
		}

		$result->oembed = $oembed;
	}

	public function getOembedData()
	{
		$serviceUrl = 'https://www.tiktok.com/oembed?url=' . $this->url;

		$connector = FH::connector($serviceUrl);
		$contents = $connector->execute()->getResult();

		$object = json_decode($contents);

		// something went wrong if the object contain this key
		if (isset($object->status_msg)) {

			// one of the known issue is if the user copy the link from the TikTok mobile app
			// then the URL will become this shortener URL e.g. https://vm.tiktok.com/ZMeyVq5Ub/
			// Try to retrieve the redirect URL and crawl again
			$finalRedirectionURL = $this->getFinalRedirectionUrl($this->url);

			if ($finalRedirectionURL) {

				$serviceUrl = 'https://www.tiktok.com/oembed?url=' . $finalRedirectionURL;

				$connector = FH::connector($serviceUrl);
				$contents = $connector->execute()->getResult();

				$object = json_decode($contents);
			}
		}

		if (isset($object->thumbnail_url)) {
			$object->thumbnail = $object->thumbnail_url;
		}

		$object->isWordpress = false;

		return $object;
	}

	/**
	 * Try to retrieve the final redirection url
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getFinalRedirectionUrl($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_exec($ch);

		$target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);

		if ($target) {
			return $target;
		}

		return false;
	}
}
