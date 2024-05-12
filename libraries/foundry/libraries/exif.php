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

use Foundry\Libraries\Exif;
use Foundry\Libraries\Date;

class Exif
{
	private $reader = null;
	private $exif = null;

	public function __construct()
	{
		$this->reader = new Reader();
	}

	/**
	 * Determines if exif is available on the site.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function available()
	{
		$state = function_exists('exif_read_data');

		return $state;
	}

	/**
	 * Reads the exif information from a given path
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function load($file)
	{
		$this->exif = $this->reader->getExifFromFile($file);

		return true;
	}

	/**
	 * Maps back the call method functions to the exif library.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function __call($method, $args)
	{
		$refArray = array();

		if ($args) {
			foreach ($args as &$arg) {
				$refArray[] =& $arg;
			}
		}

		return call_user_func_array([$this->exif, $method], $refArray);
	}
}



/**
 * PHP Exif Reader: Reads EXIF metadata from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

class Reader
{
	const INCLUDE_THUMBNAIL = true;
	const NO_THUMBNAIL = false;

	const SECTIONS_AS_ARRAYS = true;
	const SECTIONS_FLAT = false;

	/**
	 * List of EXIF sections
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Include the thumbnail in the EXIF data?
	 *
	 * @var boolean
	 */
	protected $includeThumbnail = self::NO_THUMBNAIL;

	/**
	 * Parse the sections as arrays?
	 *
	 * @var boolean
	 */
	protected $sectionsAsArrays = self::SECTIONS_FLAT;

	/**
	 * Contains the mapping of names to IPTC field numbers
	 *
	 * @var array
	 */
	protected $iptcMapping  = array(
		'title'     => '2#005',
		'keywords'  => '2#025',
		'copyright' => '2#116',
		'caption'   => '2#120',
	);


	/**
	 * Getter for the EXIF sections
	 *
	 * @return array
	 */
	public function getRequiredSections()
	{
		return $this->sections;
	}

	/**
	 * Setter for the EXIF sections
	 *
	 * @param array $sections List of EXIF sections
	 * @return \PHPExif\Reader Current instance for chaining
	 */
	public function setRequiredSections(array $sections)
	{
		$this->sections = $sections;

		return $this;
	}

	/**
	 * Adds an EXIF section to the list
	 *
	 * @param string $section
	 * @return \PHPExif\Reader Current instance for chaining
	 */
	public function addRequiredSection($section)
	{
		if (!in_array($section, $this->sections)) {
			array_push($this->sections, $section);
		}

		return $this;
	}

	/**
	 * Define if the thumbnail should be included into the EXIF data or not
	 *
	 * @param boolean $value
	 * @return \PHPExif\Reader Current instance for chaining
	 */
	public function setIncludeThumbnail($value)
	{
		$this->includeThumbnail = $value;

		return $this;
	}

	/**
	 * Reads & parses the EXIF data from given file
	 *
	 * @param string $file
	 * @return \PHPExif\Exif Instance of Exif object with data
	 * @throws \RuntimeException If the EXIF data could not be read
	 */
	public function getExifFromFile($file)
	{
		$sections   = $this->getRequiredSections();
		$sections   = implode(',', $sections);
		$sections   = (empty($sections)) ? null : $sections;

		$data       = @exif_read_data($file, $sections, $this->sectionsAsArrays, $this->includeThumbnail);

		$xmpData = $this->getIptcData($file);
		$data = array_merge($data, [Data::SECTION_IPTC => $xmpData]);

		$exif = new Data($data);

		return $exif;
	}

	/**
	 * Returns an array of IPTC data
	 *
	 * @param string $file The file to read the IPTC data from
	 * @return array
	 */
	public function getIptcData($file)
	{
		$size = getimagesize($file, $info);
		$arrData = array();
		if(isset($info['APP13'])) {
			$iptc = iptcparse($info['APP13']);

			foreach ($this->iptcMapping as $name => $field) {
				if (!isset($iptc[$field])) {
					continue;
				}

				if (count($iptc[$field]) === 1) {
					$arrData[$name] = reset($iptc[$field]);
				} else {
					$arrData[$name] = $iptc[$field];
				}
			}
		}

		return $arrData;
	}
}


class Data
{
	const SECTION_FILE      = 'FILE';
	const SECTION_COMPUTED  = 'COMPUTED';
	const SECTION_IFD0      = 'IFD0';
	const SECTION_THUMBNAIL = 'THUMBNAIL';
	const SECTION_COMMENT   = 'COMMENT';
	const SECTION_EXIF      = 'EXIF';
	const SECTION_ALL       = 'ANY_TAG';
	const SECTION_IPTC      = 'IPTC';


	// Orientations
	const ORIENTATION_TOP_LEFT 	= 1;
	const ORIENTATION_TOP_RIGHT = 2;
	const ORIENTATION_BOTTOM_RIGHT = 3;
	const ORIENTATION_BOTTOM_LEFT = 4;
	const ORIENTATION_LEFT_TOP = 5;
	const ORIENTATION_RIGHT_TOP = 6;
	const ORIENTATION_RIGHT_BOTTOM = 7;
	const ORIENTATION_LEFT_BOTTOM = 8;

	/**
	 * The EXIF data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Class constructor
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		$this->setRawData($data);
	}

	/**
	 * Sets the EXIF data
	 *
	 * @param array $data The data to set
	 * @return \PHPExif\Exif Current instance for chaining
	 */
	public function setRawData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Returns all EXIF data in the raw original format
	 *
	 * @return array
	 */
	public function getRawData()
	{
		return $this->data;
	}

	/**
	 * Returns the Aperture F-number
	 *
	 * @return string|boolean
	 */
	public function getAperture()
	{
		if (!isset($this->data[self::SECTION_COMPUTED]['ApertureFNumber'])) {
			return false;
		}

		return $this->data[self::SECTION_COMPUTED]['ApertureFNumber'];
	}

	/**
	 * Returns the ISO speed
	 *
	 * @return int|boolean
	 */
	public function getIso()
	{
		if (!isset($this->data['ISOSpeedRatings'])) {
			return false;
		}

		return $this->data['ISOSpeedRatings'];
	}

	/**
	 * Returns the Exposure
	 *
	 * @return string|boolean
	 */
	public function getExposure()
	{
		if (!isset($this->data['ExposureTime'])) {
			return false;
		}

		return $this->data['ExposureTime'];
	}

	/**
	 * Returns the Exposure
	 *
	 * @return float|boolean
	 */
	public function getExposureMilliseconds()
	{
		if (!isset($this->data['ExposureTime'])) {
			return false;
		}

		$exposureParts  = explode('/', $this->data['ExposureTime']);

		return (int)reset($exposureParts) / (int)end($exposureParts);
	}

	/**
	 * Returns the focus distance, if it exists
	 *
	 * @return string|boolean
	 */
	public function getFocusDistance()
	{
		if (!isset($this->data[self::SECTION_COMPUTED]['FocusDistance'])) {
			return false;
		}

		return $this->data[self::SECTION_COMPUTED]['FocusDistance'];
	}

	/**
	 * Returns the width in pixels, if it exists
	 *
	 * @return int|boolean
	 */
	public function getWidth()
	{
		if (!isset($this->data[self::SECTION_COMPUTED]['Width'])) {
			return false;
		}

		return $this->data[self::SECTION_COMPUTED]['Width'];
	}

	/**
	 * Returns the height in pixels, if it exists
	 *
	 * @return int|boolean
	 */
	public function getHeight()
	{
		if (!isset($this->data[self::SECTION_COMPUTED]['Height'])) {
			return false;
		}

		return $this->data[self::SECTION_COMPUTED]['Height'];
	}

	/**
	 * Returns the title, if it exists
	 *
	 * @return string|boolean
	 */
	public function getTitle()
	{
		if (!isset($this->data[self::SECTION_IPTC]['title'])) {
			return false;
		}

		return $this->data[self::SECTION_IPTC]['title'];
	}

	/**
	 * Returns the caption, if it exists
	 *
	 * @return string|boolean
	 */
	public function getCaption()
	{
		if (!isset($this->data[self::SECTION_IPTC]['caption'])) {
			return false;
		}

		return $this->data[self::SECTION_IPTC]['caption'];
	}

	/**
	 * Returns the copyright, if it exists
	 *
	 * @return string|boolean
	 */
	public function getCopyright()
	{
		if (!isset($this->data[self::SECTION_IPTC]['copyright'])) {
			return false;
		}

		return $this->data[self::SECTION_IPTC]['copyright'];
	}

	/**
	 * Returns the keywords, if they exists
	 *
	 * @return array|boolean
	 */
	public function getKeywords()
	{
		if (!isset($this->data[self::SECTION_IPTC]['keywords'])) {
			return false;
		}

		return $this->data[self::SECTION_IPTC]['keywords'];
	}

	/**
	 * Returns the camera, if it exists
	 *
	 * @return string|boolean
	 */
	public function getCamera()
	{
		if (!isset($this->data['Model'])) {
			return false;
		}

		return $this->data['Model'];
	}

	/**
	 * Returns the orientation of the image
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getOrientation()
	{
		if (!isset($this->data['Orientation'])) {
			return false;
		}

		$orientation = $this->data['Orientation'];

		return $orientation;
	}

	/**
	 * Returns the horizontal resolution in DPI, if it exists
	 *
	 * @return int|boolean
	 */
	public function getHorizontalResolution()
	{
		if (!isset($this->data['XResolution'])) {
			return false;
		}

		$resolutionParts = explode('/', $this->data['XResolution']);
		return (int)reset($resolutionParts);
	}

	/**
	 * Returns the vertical resolution in DPI, if it exists
	 *
	 * @return int|boolean
	 */
	public function getVerticalResolution()
	{
		if (!isset($this->data['YResolution'])) {
			return false;
		}

		$resolutionParts = explode('/', $this->data['YResolution']);
		return (int)reset($resolutionParts);
	}

	/**
	 * Returns the software, if it exists
	 *
	 * @return string|boolean
	 */
	public function getSoftware()
	{
		if (!isset($this->data['Software'])) {
			return false;
		}

		return $this->data['Software'];
	}

	/**
	 * Returns the focal length in mm, if it exists
	 *
	 * @return float|boolean
	 */
	public function getFocalLength()
	{
		if (!isset($this->data['FocalLength'])) {
			return false;
		}

		$parts  = explode('/', $this->data['FocalLength']);
		return (int)reset($parts) / (int)end($parts);
	}

	/**
	 * Returns the creation datetime, if it exists
	 *
	 * @return \DateTime|boolean
	 */
	public function getCreationDate()
	{
		if (!isset($this->data['DateTimeOriginal'])) {
			return false;
		}

		$ts = strtotime($this->data['DateTimeOriginal']);

		if ($ts === false) {
			return false;
		}

		$date = new Date($this->data['DateTimeOriginal']);

		return $date->toSql();
	}

	/**
	 * Returns the latitude and longitude, if it exists
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function getLocation()
	{
		if (!isset($this->data['GPSLongitude']) || !isset($this->data['GPSLongitudeRef'])) {
			return false;
		}

		// Construct the new location data
		$location = new stdClass();

		$location->longitude = $this->toDecimal($this->data['GPSLongitude'], $this->data['GPSLongitudeRef']);
		$location->latitude = $this->toDecimal($this->data['GPSLatitude'], $this->data['GPSLatitudeRef']);

		return $location;
	}

	/**
	 * Converts a GPS coordinate to a decimal based value
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function toDecimal($exifCoord, $hemi)
	{

		$degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

	}

	public function gps2Num($coordPart)
	{
		$parts = explode('/', $coordPart);

		if (count($parts) <= 0)
			return 0;

		if (count($parts) == 1)
			return $parts[0];

		return floatval($parts[0]) / floatval($parts[1]);
	}
}
