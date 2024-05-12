<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class KomentoControllerInstallCopy extends KomentoSetupController
{
	/**
	 * Responsible to copy the necessary files over.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function execute()
	{
		// Get which type of data we should be copying
		$type = $this->input->get('type', '');

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// Get the path to the zip file
		$archivePath = $tmpPath . '/' . $type . '.zip';

		// Where the extracted items should reside
		$path = $tmpPath . '/' . $type;

		// For development mode, we want to skip all this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_KOMENTO_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Extract the admin folder
		$state = $this->ktExtract($archivePath, $path);

		if (!$state) {
			$this->setInfo(JText::sprintf('COM_KOMENTO_INSTALLATION_COPY_ERROR_UNABLE_EXTRACT', $type), false);
			return $this->output();
		}

		// Look for files in this path
		$files = JFolder::files($path, '.', false, true);

		// Look for folders in this path
		$folders = JFolder::folders($path, '.', false, true);

		// Construct the target path first.
		if ($type == 'admin') {

			// clean up admin folder
			$this->cleanupAdmin();
			$target = JPATH_ADMINISTRATOR . '/components/com_komento';
		}

		if ($type == 'site') {
			// Cleanup site folder
			$this->cleanupSite();
			
			$target = JPATH_ROOT . '/components/com_komento';
		}

		// There could be instances where the user did not upload the launcher and just used the update feature.
		if ($type == 'languages') {

			// Copy the admin language file
			$adminFile = $path . '/admin/en-GB.com_komento.ini';
			JFile::copy($adminFile, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_komento.ini');

			// Copy the admin system language file
			$adminFileSys = $path . '/admin/en-GB.com_komento.sys.ini';
			JFile::copy($adminFileSys, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_komento.sys.ini');

			// Copy the site language file
			$siteFile = $path . '/site/en-GB.com_komento.ini';
			JFile::copy($siteFile, JPATH_ROOT . '/language/en-GB/en-GB.com_komento.ini');


			$this->setInfo('COM_KOMENTO_INSTALLATION_LANGUAGES_UPDATED', true);
			return $this->output();
		}

		if ($type == 'media') {
			$target = JPATH_ROOT . '/media/com_komento';
		}

		// Ensure that the target folder exists
		if (!JFolder::exists($target)) {
			JFolder::create($target);
		}

		// Scan for files in the folder
		$totalFiles = 0;
		$totalFolders = 0;

		foreach ($files as $file) {
			$name = basename($file);

			$targetFile = $target . '/' . $name;

			// Copy the file
			JFile::copy($file, $targetFile);

			$totalFiles++;
		}

		// Scan for folders in this folder
		foreach ($folders as $folder) {
			$name = basename($folder);
			$targetFolder = $target . '/' . $name;

			// Copy the folder across
			JFolder::copy($folder, $targetFolder, '', true);

			$totalFolders++;
		}


		$result = $this->getResultObj(JText::sprintf('COM_KOMENTO_INSTALLATION_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);

		return $this->output($result);
	}

	/**
	 * Cleanup admin folder
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function cleanupSite()
	{
		$path = JPATH_ROOT . '/components/com_komento';
		$exists = JFolder::exists($path);

		if ($exists) {

			// Look for files in this path
			$files = JFolder::files($path, '.', false, true);

			// Look for folders in this path
			$folders = JFolder::folders($path, '.', false, true);

			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		return;
	}

	/**
	 * Cleanup admin folder
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function cleanupAdmin()
	{
		$folders = [
			'/components/com_komento/controllers',
			'/components/com_komento/elements',
			'/components/com_komento/includes',
			'/components/com_komento/models',
			'/components/com_komento/tables',
			'/components/com_komento/themes',
			'/components/com_komento/views',
			'/components/com_komento/defaults'
		];

		foreach ($folders as $folder) {
			$path = JPATH_ADMINISTRATOR . $folder;
			$exists = JFolder::exists($path);
			
			if ($exists) {
				JFolder::delete($path);
			}
		}

		return;
	}
}
