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
namespace Foundry\Libraries;

defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/videoparser/base.php');

class VideoParser
{
	static $patterns = [
		'youtube.com' => 'youtube',
		'youtu.be' => 'youtube',
		'vimeo.com' => 'vimeo',
		'dailymotion.com' => 'dailymotion',
		'nicovideo.jp' => 'nicovideo',
		'smule.com' => 'smule',
		'facebook.com' => 'facebook',
		'fb.watch' => 'facebook'
	];

	static $code = '/\[video\](.*?)\[\/video\]/ms';

	/**
	 * Retrieves the video provider object
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function getAdapter($url, $width, $height, $fullWidth = false)
	{
		$provider = self::getProvider($url);

		$file = __DIR__ . '/videoparser/' . $provider . '.php';

		require_once($file);

		$url = strtolower($url);

		$class = 'VideoParser' . ucfirst(self::$patterns[$url]);

		if (class_exists($class)) {
			$adapter = new $class($width, $height, $fullWidth);

			return $adapter;
		}

		return false;
	}

	/**
	 * Retrieves the video provider
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function getProvider($url)
	{
		$provider = strtolower(self::$patterns[$url]);

		return $provider;
	}

	/**
	 * Replace bbcode [video] contents 
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function replace($contents, $width, $height, $fullWidth = false, $amp = false)
	{
		preg_match_all(self::$code, $contents, $matches);

		$videos	= $matches[0];

		if (!$videos) {
			return $contents;
		}

		foreach ($videos as $video) {

			preg_match(self::$code, $video, $matches);

			$matchUrl = $matches[1];

			// make sure the content has no htm tags.
			$rawUrl = strip_tags(html_entity_decode($matchUrl));

			if (stristr($rawUrl, 'http://') === false && stristr($rawUrl, 'https://') === false) {
				$rawUrl = 'http://' . $rawUrl;
			}

			$url = parse_url( $rawUrl );
			$url = explode( '.' , $url['host']);

			// Not a valid domain name.
			if (count($url) == 1) {
				return;
			}

			// Last two parts will always be the domain name.
			$url = $url[count($url) - 2] . '.' . $url[count($url) - 1];

			if (!empty($url) && array_key_exists($url, self::$patterns)) {
				$adapter = self::getAdapter($url, $width, $height, $fullWidth);

				$html = $adapter->getHtml($rawUrl, $amp);

				$contents = str_ireplace($video, $html, $contents);
			}
		}

		return $contents;
	}

	/**
	 * Removes the matched bbcode pattern from the content
	 *
	 * @since	1.1.4
	 * @access	public
	 */
	public static function strip($content)
	{
		$content = preg_replace(self::$code, '', $content);

		return $content;
	}
}
