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

use Foundry\Helpers\StringHelper;

defined('_JEXEC') or die('Unauthorized Access');

class Textavatar
{
	private $font = null;
	private $storage = null;
	private $colors = [];
	private $fontColor = null;

	public function __construct($options = [])
	{
		$colors = \FH::normalize($options, 'colors', ['#FF9800', '#FFEB3B', '#4CAF50', '#13a4f2']);
		$fontColor = \FH::normalize($options, 'fontColor', '#ffffff');

		$this->storage = str_replace(JPATH_ROOT, '', FD_MEDIA . '/images/avatar');
		$this->font = FD_MEDIA . '/fonts/opensans-regular.ttf';

		// Prepare the list of colors
		$this->colors = $this->getColors($colors);
		$this->fontColor = trim(str_ireplace('#', '', $fontColor));
	}

	/**
	 * Determines if the initials file exists
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists($initials)
	{
		static $cache = [];

		// We do not want to stat too many times, we cache it on page load so we only check per initials once
		$index = $initials;

		if (!isset($cache[$index])) {
			$path = $this->getFilePath($initials);
			$exists = \JFile::exists($path);

			if ($exists) {
				$cache[$index] = true;

				return true;
			}

			$cache[$index] = false;
		}

		return $cache[$index];
	}

	/**
	 * Generates the text based avatar
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function generate($initials)
	{
		$folder = JPATH_ROOT . $this->storage;

		// Ensure the avatar folder exists
		if (!\JFolder::exists($folder)) {
			\JFolder::create($folder);
		}

		$width = 200;
		$height = 200;
		$image = imagecreatetruecolor($width, $height);

		$color = $this->getRandomColor($initials);
		$backgroundRgb = $this->getRGB($color);
		$backgroundColor = imagecolorallocate($image, $backgroundRgb['r'], $backgroundRgb['g'], $backgroundRgb['b']);

		$fontSize = 64;
		$fontColorRgb = $this->getRGB($this->fontColor);
		$fontColor = imagecolorallocate($image, $fontColorRgb['r'], $fontColorRgb['g'], $fontColorRgb['b']);

		$box = imagettfbbox($fontSize, 0, $this->font, $initials);

		$textWidth = $box[2] - $box[0];
		$textHeight = $box[7] - $box[1];

		$x = ($width / 2) - ($textWidth / 2);
		$y = ($width / 2) - ($textHeight / 2);

		imagefill($image, 0, 0, $backgroundColor);
		imagettftext($image, $fontSize, 0, $x, $y, $fontColor, $this->font, $initials);

		$file = $this->getFilePath($initials);

		imagesavealpha($image, true);
		imagealphablending($image, false);

		// // For debugging only
		// header('Content-type: image/png');
		// imagepng($image);
		// exit;

		imagepng($image, $file, 9);

		// Free up resources
		imagedestroy($image);

		return true;
	}

	/**
	 * Generates the avatar
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAvatar($name, $characters = 2)
	{
		static $cache = [];

		$initials = $this->getInitials($name, $characters);

		if (!isset($cache[$initials])) {
			$exists = $this->exists($initials);

			if (!$exists) {
				$state = $this->generate($initials);
			}

			$cache[$initials] = $this->getFilePath($initials, true);
		}

		return $cache[$initials];
	}

	/**
	 * Converts color code into RGB
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRGB($code)
	{
		$rgb = [];
		$rgb['r'] = hexdec(substr($code, 0, 2));
		$rgb['g'] = hexdec(substr($code, 2, 2));
		$rgb['b'] = hexdec(substr($code, 4, 2));

		return $rgb;
	}

	/**
	 * Generate the unique file name for a particular initials
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFileName($initials)
	{
		$file = md5($initials) . '.png';

		return $file;
	}

	/**
	 * Retrieves the file path for a specific initials
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFilePath($initials, $uri = false)
	{
		$file = $this->getFileName($initials);
		$base = $uri ? \JURI::root() : JPATH_ROOT;
		$path = $base . $this->storage . '/' . $file;

		return $path;
	}

	/**
	 * Initializes the colors available on the system
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getColors($colors)
	{
		$colors = explode(',', $colors);

		foreach ($colors as &$color) {
			$color = trim(str_ireplace('#', '', $color));
		}

		return $colors;
	}

	/**
	 * Given a particular name, retrieve the initials
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getInitials($name, $characters = 2)
	{
		static $cache = [];

		$index = $name . $characters;

		if (!isset($cache[$index])) {
			$text = substr($name, 0, 1);
			$segments = explode(' ', $name);

			if (count($segments) >= $characters) {
				$tmp = [];
				$tmp[] = substr($segments[0], 0, 1);
				$tmp[] = substr($segments[count($segments) - 1], 0, 1);

				$text = implode('', $tmp);
			}

			$text = strtoupper($text);

			$isAscii = StringHelper::isAscii($text);

			// If the initials is not ascii, we generate other initials
			if (!$isAscii) {
				$name = strtoupper(preg_replace('/[0-9_\/]+/', '', base64_encode(sha1($name))));
				$text = \FCJString::substr($name, 0, 1);
			}

			$cache[$index] = $text;
		}

		return $cache[$index];
	}

	/**
	 * Retrieves a random color
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRandomColor($initials)
	{
		$count = rand(0, count($this->colors) - 1);
		$color = $this->colors[$count];

		return $color;
	}
}