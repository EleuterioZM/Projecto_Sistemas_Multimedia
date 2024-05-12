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

\FH::autoload();

use Intervention\Image\ImageManager;
use Foundry\Libraries\Exif;

class Image
{
	private $image = null;
	private $adapter = null;
	private $meta = null;
	private $original = null;

	public function __construct($driver = 'gd')
	{
		// Enforce gd if there is no imagemagick extension available
		if (!\FH::isImagickEnabled()) {
			$driver = 'gd';
		}

		$this->adapter = new ImageManager([
			'driver' => $driver
		]);
	}

	/**
	 * Loads an image resource given the path to the file
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function load($path, $name = '')
	{
		$this->meta = (object) [
			'path' => $path,
			'info' => getimagesize($path),
			'name' => basename($path)
		];

		if (!empty($name)) {
			$this->meta->name = $name;
		}

		// Set the image resource.
		try {
			$this->image = $this->adapter->make($path);
		} catch (Exception $e) {

		}

		$this->updateOrientation();

		// Set the original image resource.
		$this->original	= $this->image;
	}

	/**
	 * Update the orientation of the image if necessary
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function updateOrientation()
	{
		$exif = new Exif();

		// Exif methods must exist so we can read the exif data
		if (!$exif->available()) {
			return false;
		}

		// Get the mime type for this image
		$mime = $this->getMime();

		// Only image with jpeg are supported.
		if ($mime != 'image/jpeg') {
			return false;
		}

		// Load exif data.
		$exif->load($this->meta->path);

		$orientation = $exif->getOrientation();

		// Flip image horizontally since it's at top right
		if ($orientation === 2) {
			$this->image->flip('h');
		}

		// Rotate image 180 degrees left since it's at bottom right
		if ($orientation === 3) {
			$this->image->rotate(180);
		}

		// Flip image vertically because it's at bottom left
		if ($orientation === 4) {
			$this->image->flip('v');
		}

		// Flip vertically, then totate image 90 degrees right.
		if ($orientation === 5) {
			$this->image->flip('v');

			$this->image->rotate(90);
		}

		// Rotate image 90 degrees right
		if ($orientation === 6) {
			$this->image->rotate(-90);
		}

		// Flip image horizontally
		if ($orientation === 7) {
			$this->image->flip('h');
			$this->image->rotate(-90);
		}

		// Rotate image 90 degrees left
		if ($orientation === 8) {
			$this->image->rotate(90);
		}
	}

	/**
	 * Rotates an image resource
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function rotate($angle)
	{
		try {
			$this->image->rotate($angle);
		} catch (Exception $e) {
		}

		return $this;
	}

	/**
	 * Retrieves the mime of the current image.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getMime()
	{
		if (!$this->image) {
			return false;
		}

		if (!isset($this->meta->info['mime'])) {
			return false;
		}

		return $this->meta->info['mime'];
	}

	/**
	 * Gets the width of the image
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getWidth()
	{
		$width = $this->meta->info[0];

		return $width;
	}

	/**
	 * Gets the width of the image
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getHeight()
	{
		$height = $this->meta->info[1];

		return $height;
	}

	/**
	 * Resizes the image
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function resize($width = null, $height = null, $respectAspectRatio = true, $preventUpsize = true)
	{
		try {
			$this->image->resize($width, $height, function($constraint) use ($respectAspectRatio, $preventUpsize) {
				if ($respectAspectRatio) {
					$constraint->aspectRatio();
				}

				if ($preventUpsize) {
					$constraint->upsize();
				}
			});

		} catch (Exception $e) {

		}

		return $this;
	}

	/**
	 * Inserts watermark queue into the image
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function insert($resource, $position = 'center')
	{
		try {
			$this->image->insert($resource, $position);
		} catch (Exception $e) {

		}
	}

	/**
	 * Saves the image
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function save($target, $quality = 80)
	{
		$state = true;

		try {
			// Ensure the parent folder always exists or image library will throw an exception
			$folder = dirname($target);
			$exists = \JFolder::exists($folder);

			if (!$exists) {
				\JFolder::create($folder);
			}

			$result = $this->image->save($target, $quality);

		} catch (Exception $e) {
			$state = false;
		}

		return $state;
	}

	/**
	 * Outputs the image resource
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function output($type = null)
	{
		$type = is_null($type) ? $this->type : $type;

		if ($type == IMAGETYPE_JPEG) {
			imagejpeg($this->resource);
		}

		if ($type == IMAGETYPE_GIF) {
			imagegif($this->resource);
		}

		if ($type == IMAGETYPE_PNG) {
			imagepng($this->resource);
		}

		if (defined('IMAGETYPE_WEBP') && $type == IMAGETYPE_WEBP) {
			imagewebp($this->resource);
		}
	}
}
