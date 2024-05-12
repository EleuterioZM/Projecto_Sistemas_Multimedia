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

class KomentoControllerAddonsList extends KomentoSetupController
{
	public function execute()
	{
		$this->engine();

		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Construct the extraction path for the module
		$modulesExtractPath = SI_TMP . '/modules';
		$pluginsExtractPath = SI_TMP . '/plugins';

		// Get the modules list
		$modules = $this->getModulesList($path, $modulesExtractPath);

		// Get the plugins list
		$plugins = $this->getPluginsList($path, $pluginsExtractPath);


		$data = new stdClass();
		$data->modules = $modules;
		$data->plugins = $plugins;
		
		ob_start();
		include(dirname(__DIR__) . '/themes/steps/addons.list.php');
		$contents = ob_get_contents();
		ob_end_clean();

		$result = new stdClass();
		$result->html = $contents;
		$result->modulePath = $modulesExtractPath;
		$result->pluginPath = $pluginsExtractPath;
		
		// Since we combine maintenance page with this,
		// we need to get the scripts to execute as well
		$maintenance = $this->getMaintenanceScripts();

		$result->scripts = $maintenance['scripts'];
		$result->maintenanceMsg = $maintenance['message'];

		return $this->output($result);
	}

	private function getPluginsList($path, $tmp)
	{
		$zip = $path . '/plugins.zip';

		$state = $this->ktExtract($zip, $tmp);

		// @TODO: Return errors
		if (!$state) {
			return false;
		}

		// Get a list of plugin groups
		$groups = JFolder::folders($tmp, '.', false, true);

		$plugins = array();

		foreach ($groups as $group) {
			$groupTitle = basename($group);

			// Get a list of items in each groups
			$items = JFolder::folders($group, '.', false, true);
			
			foreach ($items as $item) {
				$element = basename($item);
				$manifest = $item . '/' . $element . '.xml';

				// Read the xml file
				$parser = KT::getXml($manifest);

				if (!$parser) {
					continue;
				}

				$plugin = new stdClass();
				$plugin->element = $element;
				$plugin->group = $groupTitle;
				$plugin->title = (string) $parser->name;
				$plugin->version = (string) $parser->version;
				$plugin->description = (string) $parser->description;
				$plugin->description = trim($plugin->description);
				$plugin->disabled = false; 

				if ($plugin->group == 'installer') {
					$plugin->disabled = true;
				}

				// Do not allow user to prevent system plugin from being upgraded or installed as it is a dependency since 3.1.3
				if ($plugin->group == 'system' && $plugin->element == 'komento') {
					$plugin->disabled = true;
				}

				$plugins[] = $plugin;
			}
		}

		return $plugins;
	}

	private function getModulesList($path, $tmp)
	{
		$zip = $path . '/modules.zip';
		$state = $this->ktExtract($zip, $tmp);

		if (!$state) {
			return false;
		}

		// Get a list of modules
		$items = JFolder::folders($tmp, '.', false, true);

		$modules = array();
		$installedModules = array();

		// Get previous version installed. 
		// If previous version exists, means this is an upgrade
		$isUpgrade = $this->getPreviousVersion('scriptversion');

		foreach ($items as $item) {
			$element = basename($item);
			$manifest = $item . '/' . $element . '.xml';

			// Read the xml file
			$parser = KT::getXml($manifest);

			$module = new stdClass();
			$module->title = (string) $parser->name;
			$module->version = (string) $parser->version;
			$module->description = (string) $parser->description;
			$module->description = trim($module->description);
			$module->element = $element;
			$module->disabled = false; 
			$module->checked = true;

			// we tick modules that are installed on the site
			if ($isUpgrade) {
				$module->checked = $this->isModuleInstalled($element);
			}

			// Check if the module already installed, put a flag
			// Disable this only if the module is checked.
			if (in_array($module->element, $installedModules)) {
				$module->disabled = true; 
			}

			$modules[] = $module;
		}

		return $modules;
	}

	/**
	 * Retrieves maintenance scripts
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function getMaintenanceScripts()
	{
		$maintenance = KT::maintenance();
		$previous = $this->getPreviousVersion('scriptversion');

		$files = $maintenance->getScriptFiles($previous);

		// Don't execute if no previous version is found
		// No previous version means this is a fresh installation, and this is not needed on fresh instlalation
		$msg = JText::sprintf('COM_KOMENTO_INSTALLATION_MAINTENANCE_NO_SCRIPTS_TO_EXECUTE');
		
		if ($files) {
			$msg = JText::sprintf('COM_KOMENTO_INSTALLATION_MAINTENANCE_TOTAL_FILES_TO_EXECUTE', count($files));
		}

		$result = array('message' => $msg, 'scripts' => $files);

		return $result;
	}

	/**
	 * Determines if the module is installed on the site.
	 *
	 * @since   3.1.0
	 * @access  public
	 */
	private function isModuleInstalled($module)
	{
		$db = KT::db();
		$query = array();
		$query[] = 'SELECT '. $db->quoteName('module') .' FROM ' . $db->quoteName('#__modules');
		$query[] = ' WHERE ' . $db->quoteName('module') . ' = ' . $db->Quote($module);
		
		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();
		
		if ($result) {
			return true;
		}

		return false;
	}
}
