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

class OembedAdapterDailyMotion extends OembedAdapter
{
	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'dailymotion.com') === false && stristr($url, 'dai.ly') === false) {
			return false;
		}

		return true;
	}

	public function process(&$result)
	{
		$oembed = $this->getOembedData();

		if (!$oembed) {
			$result->oembedError = $this->error;
			return $result;
		}

		// Fix http url in https issue
		$oembed = $this->fixOembedUrl($oembed);

		// If we can't get any oembed data from youtube, we will then simulate this.
		if (!$oembed) {
			return;
		}

		// Try to get the duration from the contents
		$duration = $this->getDuration();

		if ($duration) {
			$oembed->duration = $duration;
		}

		$result->oembed = $oembed;
	}

	/**
	 * Override parent's implementation
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembedData()
	{
		$serviceUrl = 'https://www.dailymotion.com/services/oembed?url=' . $this->url;

		$connector = FH::connector($serviceUrl);
		$contents = $connector->execute()->getResult();

		$object = json_decode($contents);

		if (!$object || is_null($object)) {
			$this->error = JText::_('Unable to connect to Dailymotion service');
			return false;
		}

		if (isset($object->thumbnail_url)) {
			$object->thumbnail = $object->thumbnail_url;
		}

		$object->isWordpress = false;

		return $object;
	}

	/**
	 * Obtain the duration of the dailymotion video
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getDuration()
	{
		// Get the video id
		$pattern = '/\/video\/(.*)/is';
		preg_match_all($pattern, $this->url, $matches);

		$parts = explode('_', $matches[1][0]);

		$videoId = $parts[0];

		$url = 'https://api.dailymotion.com/video/' . $videoId . '?fields=duration';

		$connector = FH::connector($url);
		$contents = $connector->execute()->getResult();

		$obj = json_decode($contents);

		$duration = '';

		if (isset($obj->duration) && $obj->duration) {
			$duration = (int) $obj->duration;
		}

		return $duration;
	}
}
