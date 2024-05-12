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

class KomentoControllerInstallSync extends KomentoSetupController
{
	/**
	 * Synchronizes database tables
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function execute()
	{
		// Load foundry
		$this->engine();

		// Get this installation version
		$version = $this->getInstalledVersion();

		// Get previous version installed
		$previous = $this->getPreviousVersion('dbversion');

		$affected = '';

		// If this is upgrade, we need to sync the db
		if ($previous !== false) {

			// lets run the db scripts sync if needed.
			$affected = KT::sync($previous);
		}

		// Update the version in the database to the latest now
		$config = KT::table('Configs');
		$config->load(array('name' => 'dbversion'));
		$config->name = 'dbversion';
		$config->params = $version;

		// Save the configuration
		$config->store($config->name);

		// If the previous version is empty, we can skip this altogether as we know this is a fresh installation
		if (!empty($affected)) {
			$this->setInfo(JText::sprintf('COM_KOMENTO_INSTALLATION_MAINTENANCE_DB_SYNCED', $version));
		} else {
			$this->setInfo(JText::sprintf('COM_KOMENTO_INSTALLATION_MAINTENANCE_DB_NOTHING_TO_SYNC', $version));
		}

		return $this->output();
	}
}
