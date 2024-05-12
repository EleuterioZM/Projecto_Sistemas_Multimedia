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
namespace Foundry\Models;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Models\Base;

class Mail extends Base
{
	/**
	 * Retrieves a list of excluded email template files
	 * 
	 * By default is empty, need to use child class to override this method if there is for the extension
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getExcludedFiles()
	{
		return [];
	}

	/**
	 * Retrieves a list of email template files
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getFiles($options = [])
	{
		$filter = \FH::normalize($options, 'filter', '');

		$base = '';
		$templates = '';

		if (!$filter || $filter === 'base') {
			// Retrieve the base files from Foundry
			$folder = $this->getFolder(true);
			$base = \JFolder::files($folder, '.', true, true);
		}

		if (!$filter || $filter === 'templates') {
			// Retrieve the list of templates files from EB
			$folder = $this->getFolder();
			$templates = \JFolder::files($folder, '.', true, true);
		}

		$layers = [
			'base' => $base,
			'templates' => $templates
		];

		$files = [];

		// Get the current site template
		$currentTemplate = \FH::getCurrentTemplate();

		foreach ($layers as $type => $rows) {
			if (empty($rows)) {
				continue;
			}

			foreach ($rows as $row) {
				$row = \FH::normalizeSeparator($row);
				$fileName = basename($row);

				$excludedFiles = $this->getExcludedFiles();

				if (in_array(\JFile::stripExt($fileName), $excludedFiles) || $fileName === 'index.html' || stristr($fileName, '.orig') !== false) {
					continue;
				}

				// Get the file object
				$file = $this->getFileObject($row, false, (string) $type === 'base');
				$files[] = $file;
			}
		}

		return $files;
	}

	/**
	 * Generates the path to an email template
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFolder($base = false)
	{
		$folder = JPATH_ROOT . '/components/' . $this->extension . '/themes/wireframe/emails';

		if ($base) {
			$folder = rtrim($this->getBaseEmailTemplatesFolder(), '/');
		}

		$folder = \FH::normalizeSeparator($folder);

		return $folder;
	}

	/**
	 * Retrieves a file object
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getFileObject($absolutePath, $contents = false, $isBase = false)
	{
		$file = new \stdClass();
		$file->name = basename($absolutePath);

		$nameForLanguageConstant = str_ireplace('.php', '', $file->name);
		$nameForLanguageConstant = strtoupper(str_ireplace(['.', '-'], '_', $nameForLanguageConstant));

		$extension = $isBase ? 'FD' : strtoupper($this->extension);
		$file->desc = \JText::_($extension . '_EMAILS_' . $nameForLanguageConstant);

		$file->path = $absolutePath;
		$file->relative = str_ireplace($this->getFolder($isBase), '', $file->path);

		// Determine if this is coming from the Foundry itself which is the base
		$file->base = $isBase;

		// Determine if the email template file has already been overriden.
		$overridePath = $this->getOverrideFolder($file->relative, $isBase);

		$file->override = (bool) \JFile::exists($overridePath);
		$file->overridePath = $overridePath;
		$file->contents = '';

		if ($contents) {
			if ($file->override) {
				$file->contents = file_get_contents($file->overridePath);
			}

			if (!$file->override) {
				$file->contents = file_get_contents($file->path);
			}
		}

		return $file;
	}

	/**
	 * Generates the path to the overriden folder
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function getOverrideFolder($file, $isBase = false)
	{
		$file = ltrim($file, '/');
		$path = JPATH_ROOT . '/templates/' . \FH::getCurrentTemplate() . '/html/' . $this->extension . '/emails/' . $file;

		if ($isBase) {
			$path = $this->getBaseEmailTemplatesFolder(true) . $file;
		}

		return $path;
	}

	/**
	 * Reset the emails template file to it's original condition
	 *
	 * @since	1.1.5
	 * @access	public
	 */
	public function reset($file)
	{
		$file = base64_decode($file);
		$path = $this->getOverrideFolder($file);

		if (\JFile::exists($path)) {
			\JFile::delete($path);
		}

		// This is coming from Foundry
		$path = $this->getOverrideFolder($file, true);

		if (\JFile::exists($path)) {
			\JFile::delete($path);
		}
	}

	/**
	 * Retrieve the folder path of the base email templates
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public static function getBaseEmailTemplatesFolder($override = false)
	{
		$folder = FD_THEMES . '/html/email/';

		if ($override) {
			$folder = JPATH_ROOT . '/templates/stackideas/foundry/html/email/';
		}

		return $folder;
	}

	/**
	 * Saves contents
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function write($path, $contents)
	{
		$state = \JFile::write($path, $contents);

		return $state;
	}
}