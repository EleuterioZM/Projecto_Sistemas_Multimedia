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

jimport('joomla.filesystem.file');

class com_KomentoInstallerScript
{
	/** 
	 * Triggered after the installation is completed
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function postflight()
	{
		ob_start();
		include(__DIR__ . '/setup.html');

		$contents = ob_get_contents();
		ob_end_clean();

		echo $contents;
	}

	/**
	 * Triggered before the installation is complete
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function preflight()
	{
		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/komento.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		if ($this->isUpgradeFrom2x()) {

			// Remove old constant file
			$this->removeConstantFile();

			// Remove old class files
			$this->removeOldClasses();

			// remove older helper files
			$this->removeOldHelpers();

			// remove older model files
			$this->removeOldModels();
		}

		// now let check the eb config
		$this->checkKTVersionConfig();
	}

	/**
	 * Responsible to remove old constant.php file to avoid redefine of same constant error
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeConstantFile()
	{
		$file = JPATH_ROOT. '/components/com_komento/constants.php';
		
		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Responsible to remove old helper files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeOldHelpers()
	{
		// helpers
		$path = JPATH_ROOT . '/components/com_komento/helpers';

		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}

	/**
	 * Responsible to remove old model files
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeOldModels()
	{
		// models
		$path = JPATH_ROOT . '/components/com_komento/models';
		
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}

	/**
	 * Responsible to remove old class files
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function removeOldClasses()
	{
		// classes
		$path = JPATH_ROOT . '/components/com_komento/classes';
		
		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}


	/**
	 * Responsible to check eb configs db version
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function checkKTVersionConfig()
	{
		// If there is the config table but no dbversion, we know this upgrade is coming from pior 3.0. lets add on dbversion into config table.
		if ($this->isUpgradeFrom2x()) {
			// Get current installed kt version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_komento/komento.xml';

			// Set this to version prior 2.0 so that it will execute the db script
			// This upgrade is from very old version.
			$version = '1.0.0';

			if (JFile::exists($xmlfile)) {
				$contents = file_get_contents($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// First, we need to add new 'name' column if this is upgrade from 2.0
			$query = 'ALTER TABLE ' . $db->quoteName('#__komento_configs') . ' ADD COLUMN `name` varchar(255) NOT NULL FIRST';

			$db->setQuery($query);
			$db->execute();

			$columns = $this->getColumns('#__komento_configs');

			// Remove component column from configs table first
			if (in_array('component', $columns)) {
				$query = 'ALTER TABLE `#__komento_configs` DROP COLUMN `component`';

				$db->setQuery($query);
				$db->execute();
			}

			// Ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__komento_configs') . ' (`name`, `params`) VALUES';
			$query .= ' (' . $db->Quote('dbversion') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('scriptversion') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Retrieve table columns
	 *
	 * @since   4.0.4
	 * @access  private
	 */
	public function getColumns($tableName)
	{
		$db = JFactory::getDBO();
		$query  = 'SHOW FIELDS FROM ' . $db->quoteName($tableName);

		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$fields = array();

		foreach ($rows as $row) {
			$fields[] = $row->Field;
		}

		return $fields;
	}

	private function isUpgradeFrom2x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '%" . $prefix . "komento_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 2.x or not.
				$query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__komento_configs') . ' LIKE ' . $db->Quote('name');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Responsible to perform the uninstallation
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function uninstall()
	{
	}

	/**
	 * Responsible to perform component updates
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function update()
	{
	}
}
