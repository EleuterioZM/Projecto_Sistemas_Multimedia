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

class KomentoControllerInstallFoundry extends KomentoSetupController
{
	/**
	 * Post installation process
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function execute()
	{
		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_KOMENTO_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// There should be a queries.zip archive in the archive.
		$tmpFoundryPath = $tmpPath . '/pkg_foundry.zip';

		// Debug
		if (!JFile::exists($tmpFoundryPath)) {
			$result = $this->getResultObj(JText::_('Foundry Package installed on the site'), true);
			return $this->output($result);
		}

		$package = JInstallerHelper::unpack($tmpFoundryPath);
		$xmlFile = $package['dir'] . '/pkg_foundry.xml';

		$contents = file_get_contents($xmlFile);
		$parser = simplexml_load_string($contents);

		$version = $parser->xpath('version');
		$version = (string) $version[0];

		// need to check if foundry in package is latest
		if (!$this->isLatestFoundry($version)) {
			$result = $this->getResultObj(JText::_('Foundry Package installed on the site'), true);
			return $this->output($result);
		}

		$installer = JInstaller::getInstance();
		$state = $installer->update($package['dir']);

		if (!$state) {
			$this->setInfo('Sorry, there was some errors when trying to install the Foundry Package file', false);
			return $this->output();
		}

		// Clean up the installer
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		$result = $this->getResultObj(JText::_('Foundry Package installed on the site'), true);

		return $this->output($result);
	}

	/**
	 * Determine which foundry is latest
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function isLatestFoundry($version)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT ' . $db->quoteName('manifest_cache') . ' FROM ' . $db->quoteName('#__extensions') . ' WHERE ' . $db->quoteName('element') . '=' . $db->Quote('pkg_foundry');
		$db->setQuery($query);

		$manifestString = $db->loadResult();

		if (!$manifestString) {
			return true;
		}

		$manifestData = json_decode($manifestString);

		return version_compare($manifestData->version, $version) < 1;
	}
}
