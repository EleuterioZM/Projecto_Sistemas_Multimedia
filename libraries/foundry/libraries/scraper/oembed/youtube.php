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

class OembedAdapterYoutube extends OembedAdapter
{
	// We need to figure out how to implement this when we port EasySocial to use this library
	// /**
	//  * Process youtube via API v3
	//  *
	//  * @since	1.0.0
	//  * @access	public
	//  */
	// public function api()
	// {
	// 	$config = ES::config();
	// 	$key = trim($config->get('youtube.api.key'));
	// 	$enabled = $config->get('youtube.api.enabled');

	// 	if (!$enabled || !$key) {
	// 		return false;
	// 	}

	// 	$videoObj = $this->getVideoId();

	// 	$parts = "&fields=items(id,snippet(title,description,thumbnails(standard)),contentDetails(duration))&part=snippet,contentDetails";

	// 	// Connect to youtube api.
	// 	$url = "https://www.googleapis.com/youtube/v3/videos?id=". $videoObj->id ."&key=" . $key . $parts;

	// 	$connector = ES::connector($url);
	// 	$contents = $connector
	// 					->setReferer(JURI::root())
	// 					->execute()
	// 					->getResult();

	// 	$obj = json_decode($contents);

	// 	// If connection failed, return to default oembed value.
	// 	if (!$obj || (isset($obj->items) && !$obj->items)) {
	// 		$this->error = JText::_('COM_EASYSOCIAL_VIDEO_LINK_EMBED_NOT_SUPPORTED');
	// 		return false;
	// 	}

	// 	// There are some errors when trying to validate the key
	// 	if (isset($obj->error)) {

	// 		// // Debug
	// 		// dump($obj->error);

	// 		return false;
	// 	}

	// 	$oembed = new stdClass();

	// 	// Assign oembed data
	// 	foreach ($obj->items as $item) {
	// 		$oembed->html = '<iframe width="480" height="270" src="https://www.youtube.com/embed/'. $item->id . $videoObj->parameter . '" frameborder="0" allowfullscreen></iframe>';
	// 		$oembed->width = 480;
	// 		$oembed->height = 270;

	// 		$snippet = isset($item->snippet) ? $item->snippet : null;

	// 		// bind the video snippet
	// 		if ($snippet) {
	// 			$oembed->title = $snippet->title;
	// 			$oembed->description = $snippet->description;
	// 			$oembed->thumbnail = 'https://img.youtube.com/vi/' . $videoObj->id . '/hqdefault.jpg';
	// 			$oembed->thumbnail_url = 'https://img.youtube.com/vi/' . $videoObj->id . '/hqdefault.jpg';

	// 			$thumbnails = isset($snippet->thumbnails) && isset($snippet->thumbnails->standard) ? $snippet->thumbnails : null;

	// 			// Use the provided thumbnails if exists.
	// 			if ($thumbnails) {
	// 				$oembed->thumbnail = $thumbnails->standard->url;
	// 				$oembed->thumbnail_url = $thumbnails->standard->url;
	// 			}
	// 		}

	// 		// Get duration
	// 		$oembed->duration = $item->contentDetails->duration;
	// 	}

	// 	$this->oembed = $oembed;

	// 	// Format the duration
	// 	$this->getDuration();

	// 	return true;
	// }

	/**
	 * Tests to see if the url is a valid url for this adapter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function isValid($url)
	{
		if (stristr($url, 'youtube.com') === false || strstr($url, 'results?search_query') || strstr($url, 'playlist?list=')) {
			return;
		}

		return true;
	}

	/**
	 * Determines if the current url is a live youtube feed
	 *
	 * @since	1.0.0
	 * @access	private
	 */
	private function isLiveUrl()
	{
		$tmp = explode('/', $this->url);
		$last = array_pop($tmp);

		if ($last === 'live') {
			return true;
		}

		return false;
	}

	/**
	 * Override the parent's behavior as we do not want to scrape video pages as the ip could be banned
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function scrape()
	{
		$result = (object) [];

		// Warn user that they need to copy the link from the live video share
		if ($this->isLiveUrl()) {
			$result->oembedError = \JText::_('FD_SCRAPER_YOUTUBE_LIVE');
			return $result;
		}

		// We need to handle our own method of processing
		parse_str(parse_url($this->url, PHP_URL_QUERY), $data);

		if (!$data) {
			return;
		}

		$oembed = $this->getOembed();

		if (!$oembed) {
			$result->oembedError = $this->error;
			return $result;
		}

		$result->oembed = $this->oembed;
		$result->url = $this->url;

		return $result;
	}

	/**
	 * Handler our own way of extracting oembed data
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getOembed()
	{
		$encodedURL = urlencode($this->url);
		$serviceUrl = 'https://www.youtube.com/oembed/?format=json&url=' . $encodedURL;

		$connector = FH::connector($serviceUrl);
		$contents = $connector->execute()->getResult();

		$object = json_decode($contents);

		if (!$object || is_null($object)) {
			$this->error = JText::_('COM_EASYSOCIAL_VIDEO_LINK_EMBED_NOT_SUPPORTED');
			return false;
		}

		$this->simulateOembedData($object);
		$this->getThumbnail();

		// Since we no longer crawl the YouTube video page directly to get the video data (because YouTube will block the website if keep crawl their video page directly)
		// so we can't retrieve the video duration time now. (for those user who want to get the duration, they need to use YouTube API key)

		return true;
	}

	/**
	 * Normalize the youTube video id
	 *
	 * @since	3.2.18
	 * @access	public
	 */
	public function getVideoId()
	{
		// some of the youtube link contain & instead of ? so we need to replace it from here
		// $videoURL = str_replace('&t=', '?t=', $this->url);
		$videoURL = $this->url;

		$startTime = '';

		// playlist
		if (strpos($videoURL, '&list=') !== false) {

			// check for the URL whether have contain video start time
			$url = explode('&list=', $videoURL);
			$playlist = '';

			// retrieve the video id
			parse_str(parse_url($url[0], PHP_URL_QUERY), $videoId);

			$videoId = $videoId['v'];

			// Ensure that url have start time URL query string
			if (isset($url[1]) && $url[1]) {
				$playlist = '?list=' . $url[1];
			}

			$normalizedVideoQueryString = $playlist;

		} else {

			// check for the URL whether have start time or without any extra parameter
			$url = explode('&t=', $videoURL);

			// retrieve the video id
			parse_str(parse_url($url[0], PHP_URL_QUERY), $videoId);

			$videoId = $videoId['v'];

			// Ensure that url have start time URL query string
			if (isset($url[1]) && $url[1]) {
				$startTime = '?start=' . $url[1];
			}

			$normalizedVideoQueryString = "?feature=oembed";

			// Ensure that only add this if the URL contain the video start time
			if ($startTime) {
				$normalizedVideoQueryString = $startTime . "&feature=oembed";
			}
		}

		$videoObj = new stdClass();
		$videoObj->id = $videoId;
		$videoObj->parameter = $normalizedVideoQueryString;

		return $videoObj;
	}

	/**
	 * Simulate oembed data
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function simulateOembedData($data)
	{
		$video = $this->getVideoId();
		
		$oembed = (object) [
			'author_name' => FH::normalize($data, 'author_name', ''),
			'author_url' => FH::normalize($data, 'author_url', ''),
			'title' => FH::normalize($data, 'title', ''),
			'type' => FH::normalize($data, 'type', ''),

			// It seems like now the YouTube oembed data no longer show description, in order to retrieve the description from the video
			// You need to setup YouTube API key since we no longer crawl the YouTube video page directly to avoid blocked from YouTube side.
			'description' => FH::normalize($data, 'description', ''),

			'width' => FH::normalize($data, 'width', 480),
			'height' => FH::normalize($data, 'height', 270),
			'html' => FH::normalize($data, 'html', '<iframe width="480" height="270" src="https://www.youtube.com/embed/'. $video->id . $video->parameter . '" frameborder="0" allowfullscreen></iframe>'),
			'thumbnail_width' => FH::normalize($data, 'thumbnail_width', 480),
			'thumbnail_height' => FH::normalize($data, 'thumbnail_height', 270),
			'thumbnail' => FH::normalize($data, 'thumbnail', 'https://img.youtube.com/vi/' . $video->id . '/sddefault.jpg'),
			'thumbnail_url' => FH::normalize($data, 'thumbnail_url', 'https://img.youtube.com/vi/' . $video->id .'/sddefault.jpg')
		];

		$oembed->html_nocookie = str_replace('youtube.com/', 'youtube-nocookie.com/', $oembed->html);

		$this->oembed = $oembed;
	}

	/**
	 * Get video thumbnails
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getThumbnail()
	{
		// We want to get the HD version of the thumbnail
		$thumbnail = str_ireplace('sddefault.jpg', 'hqdefault.jpg', $this->oembed->thumbnail);

		// Try to get the sd details
		$connector = FH::connector($thumbnail);

		try {
			$headers = $connector->useHeadersOnly()->execute()->getResult();
		} catch (Exception $e) {
			return;
		}

		$this->oembed->thumbnail = $thumbnail;
		$this->oembed->thumbnail_url = $thumbnail;
	}

	/**
	 * Convert video duration from  ISO 8601 format to seconds.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getDuration()
	{
		$duration = false;

		// Get the duration
		if (isset($this->oembed->duration) && $this->oembed->duration) {
			$duration = $this->oembed->duration;
		} else {
			$node = $this->parser->find('[itemprop=duration]');

			if ($node) {
				$node = $node[0];
				$duration = $node->attr['content'];
			}
		}

		if (!$duration) {
			$this->oembed->duration = 0;
			return;
		}

		// Match the duration
		$pattern = '/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/';
		preg_match_all($pattern, $duration, $matches);

		$seconds = 0;

		// Get the hour
		if (isset($matches[1]) && $matches[1]) {
			if ($matches[1][0] === "") {
				$matches[1][0] = 0;
			}

			$seconds = $matches[1][0] * 60 * 60;
		}

		// Minutes
		if (isset($matches[2]) && $matches[2]) {
			if ($matches[2][0] === "") {
				$matches[2][0] = 0;
			}

			$seconds = $seconds + ($matches[2][0] * 60);
		}

		// Seconds
		if (isset($matches[3]) && $matches[3]) {
			if ($matches[3][0] === "") {
				$matches[3][0] = 0;
			}

			$seconds = $seconds + $matches[3][0];
		}

		$this->oembed->duration = (int) $seconds;
	}
}
