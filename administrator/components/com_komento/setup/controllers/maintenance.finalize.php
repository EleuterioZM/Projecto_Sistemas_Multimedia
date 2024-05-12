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

class KomentoControllerMaintenanceFinalize extends KomentoSetupController
{
	public function execute()
	{
		$this->engine();

		$version = $this->getInstalledVersion();

		// Update the version in the database to the latest now
		$config = KT::table('Configs');
		$config->load(array('name' => 'scriptversion'));

		$config->name = 'scriptversion';
		$config->params = $version;

		// Save the new config
		$config->store($config->name);

		// Remove any folders in the temporary folder.
		$this->cleanup(SI_TMP);

		// Remove helpers folder and constants.php if this is an upgrade from 3.x to 5.x
		$this->removeFrontendUnusedFolders();
		$this->removeUnusedFolders();
		$this->removeUnusedFiles();
		$this->removeConstantsFile();

		// Remove installation temporary file
		JFile::delete(JPATH_ROOT . '/tmp/komento.installation');

		$result = $this->getResultObj(JText::sprintf('COM_KOMENTO_INSTALLATION_MAINTENANCE_UPDATED_MAINTENANCE_VERSION', $version), 1, 'success');

		return $this->output($result);
	}

	/**
	 * Perform system wide cleanups after the installation is completed.
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function cleanup($path)
	{
		$folders = JFolder::folders($path, '.', false, true);
		$files = JFolder::files($path, '.', false, true);

		if ($folders) {
			foreach ($folders as $folder) {
				JFolder::delete($folder);
			}
		}

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}

		// Cleanup javascript files
		$this->removeOldJavascripts();
	}

	/**
	 * Remove all old javascript files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeOldJavascripts()
	{
		// Get the current installed version
		$version = $this->getInstalledVersion();

		// Ignored files
		$ignored = array('.svn', 'CVS', '.DS_Store', '__MACOSX');

		$ignored[] = 'admin-' . $version . '-basic.min.js';
		$ignored[] = 'admin-' . $version . '-basic.js';
		$ignored[] = 'admin-' . $version . '.min.js';
		$ignored[] = 'admin-' . $version . '.js';
		$ignored[] = 'site-' . $version . '-basic.min.js';
		$ignored[] = 'site-' . $version . '-basic.js';
		$ignored[] = 'site-' . $version . '.min.js';
		$ignored[] = 'site-' . $version . '.js';
		$ignored[] = 'bootloader.js';
		$ignored[] = 'template.php';

		$files = JFolder::files(JPATH_ROOT . '/media/com_komento/scripts', '.', false, true, $ignored);

		if ($files) {
			foreach ($files as $file) {
				JFile::delete($file);
			}
		}
	}

	public function removeUnusedFolders()
	{
		$path = JPATH_ROOT . '/media/foundry/3.1';

		// Only remove this folder if there is no Easyblog/Easydiscuss 3.x installed on the site
		// Since those still using foundry 3.1
		$is3xInstalled = $this->is3xInstalled();

		if (JFolder::exists($path) && !$is3xInstalled) {
			JFolder::delete($path);
		}

		// resources
		$path = JPATH_ROOT . '/media/com_komento/resources';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}

	public function removeUnusedFiles()
	{
		$paths = [
			JPATH_ADMINISTRATOR . '/components/com_komento/themes/default/settings/integrations/aup.php',
			JPATH_ROOT . '/components/com_komento/themes/wireframe/emails/template.html'
		];

		foreach ($paths as $file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}
	}

	public function removeConstantsFile()
	{
		// old constants.php location.
		$file = JPATH_ROOT . '/components/com_komento/constants.php';

		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Determine if EB or ED 3.x is installed
	 *
	 * @since   3.0.6
	 * @access  public
	 */
	public function is3xInstalled()
	{
		$extensions = array('easyblog', 'easydiscuss');

		foreach ($extensions as $ext) {
			$path = JPATH_ADMINISTRATOR . '/components/com_' . $ext . '/' . $ext . '.xml';

			if (!JFile::exists($path)) {
				return false;
			}

			$contents = file_get_contents($path);

			$parser = simplexml_load_string($contents);

			$version = $parser->xpath('version');
			$version = (string) $version[0];

			// We assume if EB and ED are in 3.x version, don't remove foundry 3.1 folder
			$version = explode('.', $version);

			if ($version[0] == '3') {
				return true;
			}
		}

		return false;
	}

	public function removeFrontendUnusedFolders()
	{
		// models
		$path = JPATH_ROOT . '/components/com_komento/models';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		// helpers
		$path = JPATH_ROOT . '/components/com_komento/helpers';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}

		// profile views since we are no longer use this view.
		$path = JPATH_ROOT . '/components/com_komento/views/profile';
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}
}
