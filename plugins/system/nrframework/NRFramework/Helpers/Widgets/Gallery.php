<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Widgets;

defined('_JEXEC') or die;

use NRFramework\Mimes;

class Gallery
{
	/**
	 * Stores all gallery parsed directories info txt file `*.gallery_info.txt` data in format:
	 * GALLERY DIRECTORY => ARRAY OF `*.gallery_info.txt` file data
	 * 
	 * @var  array
	 */
	static $gallery_directories_info_file = [];

	/**
	 * Stores all galleries info file names in format:
	 * 
	 * GALLERY DIRECTORY => INFO FILE NAME
	 * 
	 * @var  array
	 */
	static $gallery_directories_info_file_names = [];
	
	/**
	 * The directory information file holding all gallery item details.
	 * 
	 * @var  string
	 */
	const directory_gallery_info_file = 'gallery_info.txt';
	
	/**
	 * Parses the given gallery items.
	 * 
	 * @param   mixed  	$input     			 	A string to a directory/path/URL or an array of a URL item containing its information.
	 * @param   array   $allowed_file_types	 	The allowed file types.
	 * 
	 * @return  mixed
	 */
	public static function parseGalleryItems($input, $allowed_file_types = [])
	{
		if (is_string($input))
		{
			$fullpath_input = JPATH_ROOT . DIRECTORY_SEPARATOR . ltrim($input, DIRECTORY_SEPARATOR);

			// Parse Directory
			if (is_dir($fullpath_input))
			{
				return self::parseDirectory($fullpath_input, $allowed_file_types);
			}

			// Skip invalid URLs
			if ($url = self::parseURL($input))
			{
				return [$url];
			}

			// Parse Image
			if ($image_data = self::parseImage($fullpath_input, $allowed_file_types))
			{
				return [$image_data];
			}
		}

		return [self::parseURL($input)];
	}

	/**
	 * Parse the directory by finding all of its images and their information.
	 * 
	 * @param   string  $dir
	 * @param   array   $allowed_file_types
	 * 
	 * @return  mixed
	 */
	public static function parseDirectory($dir, $allowed_file_types = [])
	{
		if (!is_string($dir) || !is_dir($dir) || empty($allowed_file_types))
		{
			return;
		}

		$items = [];

		// Get images
		$files = array_diff(scandir($dir), ['.', '..', '.DS_Store']);

		foreach ($files as $key => $filename)
		{
			// Skip directories
			if (is_dir($dir . DIRECTORY_SEPARATOR . $filename))
			{
				continue;
			}

			$image_path = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
			
			if (!$image_data = self::parseImage($image_path, $allowed_file_types))
			{
				continue;
			}

			$items[] = $image_data;
		}

		return $items;
	}

	/**
	 * Parse the directory image and return its information.
	 * 
	 * @param   string  $image_path
	 * @param   string  $allowed_file_types
	 * 
	 * @return  mixed
	 */
	public static function parseImage($image_path, $allowed_file_types = null)
	{
		if (!is_string($image_path))
		{
			return;
		}

		$data = [
			'path' => $image_path,
			'url' => self::directoryImageToURL($image_path)
		];

		if (!is_file($image_path))
		{
			return array_merge($data, [
				'invalid' => true
			]);
		}

		// Skip not allowed file types
		if (!is_null($allowed_file_types) && !Mimes::check($allowed_file_types, Mimes::detectFileType($image_path)))
		{
			return;
		}

		// Check if there is a `*.gallery_info.txt` helper file and get any information about the image
		$gallery_info_file_data = self::getGalleryInfoFileData(dirname($image_path));
		if (!$gallery_info_file_data)
		{
			return $data;
		}

		$image_filename = pathinfo($image_path, PATHINFO_BASENAME);

		// If no information from the text field about this image was found, stop
		if (!isset($gallery_info_file_data[$image_filename]))
		{
			return $data;
		}

		$image_data = $gallery_info_file_data[$image_filename];

		return array_merge($data, [
			'caption' => isset($image_data['caption']) ? $image_data['caption'] : ''
		]);
	}

	/**
	 * Parses a single URL either as a String or as an Array.
	 * 
	 * @param   mixed  $item
	 * 
	 * @return  mixed
	 */
	public static function parseURL($item)
	{
		// URL is a string
		if (is_string($item))
		{
			if (!filter_var($item, FILTER_VALIDATE_URL))
			{
				return;
			}

			return [
				'url' => $item
			];
		}
		
		// URL is an array
		if (!is_array($item) || !count($item))
		{
			return;
		}

		// If a thumbnail URL is given but no URL, use it as the full image URL
		if (isset($item['thumbnail_url']) && !isset($item['url']))
		{
			$item['url'] = $item['thumbnail_url'];
		}

		if (!isset($item['url']))
		{
			return;
		}

		if (!filter_var($item['url'], FILTER_VALIDATE_URL))
		{
			return;
		}
		
		return $item;
	}

	/**
	 * Loads a module by its ID.
	 * 
	 * @param   string  $id
	 * 
	 * @return  string
	 */
	public static function loadModule($id)
	{
		$module = \JModuleHelper::getModuleById($id);
		$params = ['style' => 'none'];

		return $module->id > 0 ? \JFactory::getDocument()->loadRenderer('module')->render($module, $params) : '';
	}

	/**
	 * Read the `*.gallery_info.txt` file for the given directory.
	 * 
	 * @param   string  $dir
	 * 
	 * @return  mixed
	 */
	public static function getGalleryInfoFileData($dir)
	{
		if (isset(self::$gallery_directories_info_file[$dir]) && !empty(self::$gallery_directories_info_file[$dir]))
		{
			return self::$gallery_directories_info_file[$dir];
		}

		if (!$file = self::findGalleryInfoFile($dir))
		{
			return [];
		}

		// Read file
		if (!$handle = fopen($file, 'r'))
		{
			return [];
		}

		$data = [];

		$line_defaults = ['', '', ''];

		// Loop each line
		while (($line = fgets($handle)) !== false)
		{
			list($filename, $caption, $hash) = explode('|', $line) + $line_defaults;

			// If no filename is given, continue
			if (!$filename)
			{
				continue;
			}

			$data[$filename] = [
				'filename' => $filename,
				'caption' => trim($caption),
				'hash' => trim($hash)
			];
		}
		
		// Close file
		fclose($handle);

		self::$gallery_directories_info_file[$dir] = $data;

		return $data;
	}

	/**
	 * Finds the source image and whether it has been edited.
	 * 
	 * @param   string  $source
	 * @param   string  $destination_folder
	 * 
	 * @return  mixed
	 */
	public static function findSourceImageDetails($source, $destination_folder)
	{
		$source_filename = pathinfo($source, PATHINFO_BASENAME);

		$data = self::getGalleryInfoFileData(dirname($source));

		$image_data = isset($data[$source_filename]) ? $data[$source_filename] : false;
		
		if (!$image_data)
		{
			return false;
		}
		
		if (empty($image_data['hash']))
		{
			return false;
		}

		$sourceHash = self::calculateFileHash($source);

		return [
			'path' => $destination_folder . $image_data['filename'],
			'edited' => $image_data['hash'] !== $sourceHash
		];
	}

	/**
	 * Updates or Inserts the given image information from the gallery info file.
	 * 
	 * @param   string   $source
	 * @param   array    $image_data
	 * 
	 * @return  mixed
	 */
	public static function updateImageDataInGalleryInfoFile($source, $image_data)
	{
		// Source directory
		$source_directory = dirname($source);

		// Check whether the gallery info file exists, if not, create it
		if (!$file = self::findGalleryInfoFile($source_directory))
		{
			$file = self::createGalleryInfoFile($source_directory);
		}

		// Open files
		$reading = fopen($file, 'r');
		$writing = fopen($file . '.tmp', 'w');
	
		$replaced = false;
	
		while (!feof($reading))
		{
			// Get each file line
			$line = fgets($reading);

			// Remove new line at the end
			$line = trim(preg_replace('/\s\s+/', ' ', $line));

			// Skip empty lines
			if (empty($line))
			{
				continue;
			}

			list($filename, $caption, $hash) = explode('|', $line) + ['', '', ''];

			// We need to manipulate current file
			if (strtolower($filename) !== strtolower(basename($image_data['path'])))
			{
				fputs($writing, $line . "\n");
				continue;
			}

			$replaced = true;

			$line = $filename . '|' . $caption . '|' . self::calculateFileHash($source) . "\n";;

			// Write changed line
			fputs($writing, $line);
		}

		// Close files
		fclose($reading);
		fclose($writing);

		// If we replaced a line, update the text file
		if ($replaced) 
		{
			rename($file . '.tmp', $file);
		}
		// No line was replaced, append image details
		else
		{
			unlink($file . '.tmp');
			
			self::appendImageDataToGalleryInfoFile($file, $source, $image_data);
		}
	}

	/**
	 * Removes the image from the gallery info file.
	 * 
	 * @param   string   $source
	 * 
	 * @return  boolean
	 */
	public static function removeImageFromGalleryInfoFile($source)
	{
		// Get the gallery info file from destination folder
		if (!$file = self::findGalleryInfoFile(dirname($source)))
		{
			return false;
		}
		
		// Open files
		$reading = fopen($file, 'r');
		$writing = fopen($file . '.tmp', 'w');
	
		$found = false;
	
		while (!feof($reading))
		{
			// Get each file line
			$line = fgets($reading);

			// Remove new line at the end
			$line = trim(preg_replace('/\s\s+/', ' ', $line));

			// Skip empty lines
			if (empty($line))
			{
				continue;
			}

			list($filename, $caption, $hash) = explode('|', $line) + ['', '', ''];

			// We need to manipulate current file
			if ($filename !== pathinfo($source, PATHINFO_BASENAME))
			{
				$found = true;
				fputs($writing, $line . "\n");
				continue;
			}
		}

		// Close files
		fclose($reading);
		fclose($writing);

		if (!$found)
		{
			return false;
		}

		// Save the changes
		rename($file . '.tmp', $file);

		return true;
	}

	/**
	 * Appends the image data into the info file.
	 * 
	 * @param   string  $dir
	 * 
	 * @return  void
	 */
	public static function createGalleryInfoFile($dir)
	{
		$file = self::getLanguageInfoFileName($dir);
		
		file_put_contents($file, '');

		return $file;
	}

	/**
	 * Appends the image data into the info file.
	 * 
	 * @param   string  $file
	 * @param   string  $source
	 * @param   object  $image_data
	 * 
	 * @return  void
	 */
	public static function appendImageDataToGalleryInfoFile($file, $source, $image_data)
	{
		$caption = isset($image_data['caption']) ? $image_data['caption'] : '';
		
		$hash = self::calculateFileHash($source);
		
		$line = pathinfo($source, PATHINFO_BASENAME) . '|' . $caption . '|' . $hash . "\n";

		file_put_contents($file, $line, FILE_APPEND);
	}

	/**
	 * Finds the `*.gallery_info.txt` file if it exists in the given directory.
	 * 
	 * @param   string  $dir
	 * 
	 * @return  mixed
	 */
	public static function findGalleryInfoFile($dir)
	{
		if (isset(self::$gallery_directories_info_file_names[$dir]))
		{
			return self::$gallery_directories_info_file_names[$dir];
		}
		
		// Method 1: With language prefix
		$file = self::getLanguageInfoFileName($dir);

		// Check if the file exists
		if (file_exists($file))
		{
			self::$gallery_directories_info_file_names[$dir] = $file;
			
			return $file;
		}

		// Method 2: Without the language prefix
		$file = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::directory_gallery_info_file;

		// Check if the file exists
		if (file_exists($file))
		{
			self::$gallery_directories_info_file_names[$dir] = $file;

			return $file;
		}

		return false;
	}

	/**
	 * Returns the info file with the language prefix.
	 * 
	 * @param   string  $dir
	 * 
	 * @return  string
	 */
	public static function getLanguageInfoFileName($dir)
	{
		return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . \JFactory::getLanguage()->getTag() . '.' . self::directory_gallery_info_file;
	}

	/**
	 * Calculates the file hash of a file.
	 * 
	 * Hash = md5(file path + last modified date of file)
	 * 
	 * @param   string  $file_path
	 * 
	 * @return  string
	 */
	public static function calculateFileHash($file_path)
	{
		return md5($file_path . filemtime($file_path));
	}

	/**
	 * Transforms an image path to a URL.
	 * 
	 * @param   string  $image_path
	 * 
	 * @return  string
	 */
	public static function directoryImageToURL($image_path)
	{
		return rtrim(\JURI::root(), DIRECTORY_SEPARATOR) . mb_substr($image_path, strlen(JPATH_BASE), null);;
	}
}