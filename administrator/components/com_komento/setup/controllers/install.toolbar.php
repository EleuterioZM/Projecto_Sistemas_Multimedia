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

class KomentoControllerInstallToolbar extends KomentoSetupController
{
	/**
	 * Perform installation of Toolbar Package
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function execute()
	{
		// If on development mode, skip this
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_KOMENTO_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Get the temporary path from the server.
		$tmpPath = $this->input->get('path', '', 'default');

		// There should be a queries.zip archive in the archive.
		$tmpToolbarPath = $tmpPath . '/pkg_toolbar.zip';

		$package = JInstallerHelper::unpack($tmpToolbarPath);

		$installer = JInstaller::getInstance();
		$installer->setOverwrite(true);

		$state = $installer->install($package['dir']);

		if (!$state) {
			$this->setInfo('Sorry, there was some errors when trying to install the Stackideas Toolbar Package file', false);
			return $this->output();
		}

		// Clean up the installer
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		$result = $this->getResultObj(JText::_('Stackideas Toolbar Package installed on the site'), true);

		return $this->output($result);
	}
}
