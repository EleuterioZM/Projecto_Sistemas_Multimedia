<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

use NRFramework\Mimes;

defined( '_JEXEC' ) or die( 'Restricted access' );

class File 
{
	/**
	 * Upload file
	 *
	 * @param	array	$file				The request file as posted by form
	 * @param	string	$upload_folder		The upload folder where the file must be uploaded
	 * @param	string	$allowed_file_types	A comma separated list of allowed file types like: .jpg, .gif, .png
	 * @param	bool	$allow_unsafe		Allow the upload of unsafe files. See JFilterInput::isSafeFile() method.
	 * @param	bool	$random_prefix		If is set to true, the filename will get a random unique prefix
	 *
	 * @return	mixed	String on success, Null on failure
	 */
	public static function upload($file, $upload_folder = null, $allowed_file_types = [], $allow_unsafe = false, $random_prefix = null)
	{
		// Make sure we have a valid file array
		if (!isset($file['name']) || !isset($file['tmp_name']))
		{
			throw new \Exception(\JText::sprintf('NR_UPLOAD_ERROR_CANNOT_UPLOAD_FILE', $file['name']));
		}

		// Check file type
		self::checkMimeOrDie($allowed_file_types, $file);

		/**
		 * Try transiterating the file name using the native php function
		 * 
		 * This is used in 4.0 version of makeSafe but not in 3.X.
		 * 
		 * If the given filename is non-latin, then all characters will be removed from the filename via makeSafe and thus
		 * we wont be able to upload the file.
		 * 
		 * @see https://github.com/joomla/joomla-cms/pull/27974
		 */
		if (!defined('nrJ4') && function_exists('transliterator_transliterate') && function_exists('iconv'))
		{
			// Using iconv to ignore characters that can't be transliterated
			$file['name'] = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $file['name']));
		}
		
		// Sanitize filename
		$filename = \JFile::makeSafe($file['name']);

		if (!is_null($random_prefix))
		{
			$filename = uniqid($random_prefix) . '_' . $filename;
		}

		$filename = str_replace(' ', '_', $filename);

		// Setup the full file name
		$upload_folder = is_null($upload_folder) ? self::getTempFolder() : $upload_folder;
		$destination_file = implode(DIRECTORY_SEPARATOR, [$upload_folder, $filename]);

		// If file exists, rename to copy_X
		self::uniquefy($destination_file);

		$destination_file = \JPath::clean($destination_file);

		if (!\JFile::upload($file['tmp_name'], $destination_file, false, $allow_unsafe))
		{
			throw new \Exception(\JText::sprintf('NR_UPLOAD_ERROR_CANNOT_UPLOAD_FILE', $file['name']));
		}

		return $destination_file;
	}

	/**
	 * Moves a file from one directory to another. Destination directories will be created if they are not exist.
	 *
	 * @param	[type]	$source_file		The source file path
	 * @param	[type]	$destination_file	The destination file path
	 * @param	bool	$replace_existing	Replace same files names, otherwise create a copy in the format copy_X
	 *
	 * @return	mixed	String on success
	 */
	public static function move($source_file, $destination_file, $replace_existing = false)
	{
		$destination_folder = dirname($destination_file);

		// Create destination folders recursively
		if (!self::createDirs($destination_folder))
		{
			throw new \Exception(\JText::sprintf('NR_CANNOT_CREATE_FOLDER', $destination_folder));
		}

		// Don't replace files with the same name. Instead, append copy_x to this one.
		if (!$replace_existing)
		{
			self::uniquefy($destination_file);
		}

		// Move file to the destination folder
		if (!\JFile::move($source_file, $destination_file))
		{
			throw new \Exception(\JText::sprintf('NR_CANNOT_MOVE_FILE', $destination_file));
		}

		return \JPath::clean($destination_file);
	}

	/**
	 * Reads (and checks) the temp Joomla folder
	 *
	 * @return string
	 */
	public static function getTempFolder()
	{
		$ds = DIRECTORY_SEPARATOR;

		$tmpdir = \JFactory::getConfig()->get('tmp_path');

		if (realpath($tmpdir) == $ds . 'tmp')
		{
			$tmpdir = JPATH_SITE . $ds . 'tmp';
		}
		
		elseif (!\JFolder::exists($tmpdir))
		{
			$tmpdir = JPATH_SITE . $ds . 'tmp';
		}

		return \JPath::clean(trim($tmpdir) . $ds);
	}

	/**
	 * Checks if the path exists. If not creates the folders as well as subfolders.
	 * 
	 * @param   string  $path	 The folder path
	 * @param   string  $protect If set to true, each folder will be protected by disabling PHP engine and preventing folder browsing
	 * 
	 * @return  bool
	 */
	public static function createDirs($path, $protect = true)
	{
		if (!\JFolder::exists($path))
		{
			mkdir($path, 0755, true);

			// New folder created. Let's protect it.
			if ($protect)
			{
				self::writeHtaccessFile($path);
				self::writeIndexHtmlFile($path);
			}
		}

		// Make sure the folder is writable
		return @is_writable($path);
	}

	/**
	 * Checks whether a file type is in an allowed list
	 *
	 * @param	mixed	$allowed_types	Array or a comma separated list of allowed file extensions or mime types. Eg: .jpg, .png, applicaton/pdf
	 * @param	string	$file_object	The uploaded file as appears in the $_FILES array
	 *
	 * @return	bool
	 */
	public static function checkMimeOrDie($allowed_types, $file_object)
	{
		$file_path = $file_object['tmp_name'];
		$file_name = isset($file_object['name']) ? $file_object['name'] : basename($file_path);

		// Do we have a mime type detected?
		if (!$mime_type = Mimes::detectFileType($file_path))
		{
			throw new \Exception(\JText::sprintf('NR_UPLOAD_NO_MIME_TYPE', $file_name));
		}

		if (!Mimes::check($allowed_types, $mime_type))
		{
			throw new \Exception(\JText::sprintf('NR_UPLOAD_INVALID_FILE_TYPE', $file_name, $mime_type, $allowed_types));
		}
	}

	/**
	 * Add an .htaccess file to the folder in order to disable PHP engine entirely 
	 *
	 * @param  string $path	The path where to write the file
	 *
	 * @return void
	 */
	public static function writeHtaccessFile($path)
	{
		$content = '
			# Block direct PHP access
			<Files *.php>
				deny from all
			</Files>
		';

		\JFile::write($path . '/.htaccess', $content);
	}

	/**
	 * Creates an empty index.html file to prevent directory listing 
	 *
	 * @param  string $path	The path where to write the file
	 *
	 * @return void
	 */
	public static function writeIndexHtmlFile($path)
	{
		\JFile::write($path . '/index.html', '<!DOCTYPE html><title></title>');	
	}

	/**
	 * Generates a unique filename in case the give name already exists by appending copy_X suffix to filename.
	 *
	 * @param  strimg $path  
	 *
	 * @return void
	 */
	public static function uniquefy(&$path)
	{
		$path_parts = self::pathinfo($path);

		$dir = $path_parts['dirname'];
		$ext = $path_parts['extension'];
		$actual_name = $path_parts['filename'];
		
		$original_name = $actual_name;

		$i = 1;

		while(\JFile::exists($dir . '/' . $actual_name . '.' . $ext))
		{           
			$actual_name = (string) $original_name . '_copy_' . $i;
			$path = $dir . '/' . $actual_name . '.' . $ext;
			$i++;
		}
	}

	/**
	 * Returns information about a file path with multi-byte support
	 *
	 * @param  string $path   The path to be parsed.
	 *
	 * @return array 
	 */
	public static function pathinfo($path)
	{
		// Store temporary the currenty locale
		$currentLocale = setlocale(LC_ALL, 0);

		setlocale(LC_ALL, 'C.UTF-8');
		$pathinfo = pathinfo($path);

		// Set back to previus value
		setlocale(LC_ALL, $currentLocale);

		return $pathinfo;
	}
}