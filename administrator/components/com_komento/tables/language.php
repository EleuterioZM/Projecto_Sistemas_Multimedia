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

class KomentoTableLanguage extends KomentoTable
{
	public $id = null;
	public $title = null;
	public $locale = null;
	public $updated = null;
	public $state = null;
	public $translator = null;
	public $progress = null;
	public $params = null;
	public $layouts = null;

	public function __construct(& $db)
	{
		parent::__construct('#__komento_languages' , 'id' , $db);
	}

	/**
	 * Determines if the language is installed
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isInstalled()
	{
		return $this->state == KOMENTO_LANGUAGES_INSTALLED;
	}

	/**
	 * Allows caller to uninstall a language
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function uninstall()
	{
		$locale = $this->locale;

		$paths = array(JPATH_ADMINISTRATOR . '/language/' . $locale, JPATH_ROOT . '/language/' . $locale);

		// Get the list of files on each folders
		foreach ($paths as $path) {

			$filter = 'komento';
			$files = JFolder::files($path, $filter, false, true);

			if (!$files) {
				continue;
			}

			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		$this->state = KOMENTO_LANGUAGES_NOT_INSTALLED;
		return $this->store();
	}

	/**
	 * Installs a language file
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function install()
	{
		$params = $this->getParams();

		// Get the api key
		$config = KT::config();
		$key = $config->get('main_apikey');

		// Get the download url
		$url = $params->get('download');

		if (!$url) {
			$this->setError(JText::_('COM_KOMENTO_LANGUAGES_DOWNLOAD_URL_EMPTY'));
			return false;
		}

		// it seems like redirection from non https to https cause the query string to be missing.
		// cheap fix. replace to https version.
		$url = str_replace('http://', 'https://', $url);

		// Download the language file
		$connector = FH::connector($url);
		$connector->setMethod('POST');
		$connector->addQuery('key', $key);
		$connector->execute();

		// Get the contents of the zip file
		$result = $connector->getResult();

		// Create a temporary storage for this file
		$md5 = md5(FH::date()->toSql());
		$storage = KOMENTO_TMP . '/' . $md5 . '.zip';
		$state = JFile::write($storage, $result);

		// Set the path for the extracted folder
		$extractedFolder = KOMENTO_TMP . '/' . $md5;

		jimport('joomla.filesystem.archive');

		// Extract the language's archive file
		$state = FCArchive::extract($storage, $extractedFolder);

		// Throw some errors when we are unable to extract the zip file.
		if (!$state) {
			return false;
		}

		$metaPath = $extractedFolder . '/meta.json';

		// Read the meta data file
		$obj = json_decode(file_get_contents($metaPath));

		// Get the resources
		$resources = $obj->resources;

		foreach ($resources as $file) {

			// Get the correct path based on the meta's path
			$languageFolder = $this->getPath($file->path);
			$languageFolder	= $languageFolder . '/language';

			// Construct the absolute path
			$path = $languageFolder . '/' . $this->locale;

			// If the folder does not exist, create it first
			if (!JFolder::exists($path)) {
				JFolder::create($path);
			}

			// Set the destination path
			$destFile = $path . '/' . $this->locale . '.' . $file->title;
			$sourceFile = $extractedFolder . '/' . $file->path . '/' . $this->locale . '.' . $file->title;

			// Try to copy the file
			$state = JFile::copy($sourceFile, $destFile);

			// if (!$state) {
			// 	$this->setError(JText::_('COM_KOMENTO_LANGUAGES_ERROR_COPYING_FILES'));
			// 	return false;
			// }
		}

		// After everything is copied, ensure that the extracted folder is deleted to avoid dirty filesystem
		JFile::delete($storage);
		JFolder::delete($extractedFolder);

		// Once the language files are copied accordingly, update the state
		$this->state = KOMENTO_LANGUAGES_INSTALLED;

		return $this->store();
	}

	public function getPath($metaPath)
	{
		switch($metaPath)
		{
			case 'site':
			case 'module':
				$path	= JPATH_ROOT;
			break;

			case 'admin':
			case 'fields':
			case 'plugins':
			case 'plugin':
			case 'menu':
			case 'apps':
				$path 	= JPATH_ROOT . '/administrator';
			break;
		}

		return $path;
	}
}
