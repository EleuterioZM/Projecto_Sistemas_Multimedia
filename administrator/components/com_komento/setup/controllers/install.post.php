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

class KomentoControllerInstallPost extends KomentoSetupController
{
	/**
	 * Post installation process
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function execute()
	{
		$results = array();

		// Get the api key so that we can store it
		$key = $this->input->get('apikey', '', 'default');

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_KOMENTO_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Update api key
		$this->updateConfig('main_apikey', SI_KEY);

		$results[] = $this->initConfig(); 

		// ACL rules needs to be created first before anything else
		$results[] = $this->initACL();

		$this->updateAdminMenu();

		// Purge captcha records
		$this->purgeCaptcha();

		// Update the manifest_cache in #__extensions table
		$this->updateManifestCache();

		// Delete the komento from the Updates table
		$this->deleteUpdateRecord();

		// Ensure the site MUST enable Komento system plugin now #384
		$this->activateSystemPlugin();

		$message = '';

		foreach ($results as $obj) {

			if ($obj === false) {
				continue;
			}

			$class = $obj->state ? 'success' : 'error';
			$message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		$this->setInfo($message, true);
		return $this->output();
	}

	/**
	 * Update the manifest cache
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function updateManifestCache()
	{	
		$db = JFactory::getDBO();
		$extensionId = $this->getKomentoComponentId();
		$manifest_details = JInstaller::parseXMLInstallFile(JPATH_ROOT. '/administrator/components/com_komento/komento.xml');
		$manifest = json_encode($manifest_details);

		// For some Joomla versions, there is no tables/Extension.php
		// Hence, the JTable::getInstance('Extension') will return null
		$table = JTable::getInstance('Extension');

		if ($table) {
			$exists = $table->load($extensionId);

			if (!$exists) {
				return false;
			}

			$table->manifest_cache = $manifest;
			$table->store();
		} else {
			$query	= 'UPDATE '. $db->quoteName('#__extensions')
					. ' SET ' . $db->quoteName('manifest_cache') . ' = ' . $db->Quote($manifest)
					. ' WHERE ' . $db->quoteName('extension_id') . ' = ' . $db->Quote($extensionId);
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Delete record in updates table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function deleteUpdateRecord()
	{
		$this->engine();

		$db = KT::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__updates') . ' WHERE ' . $db->quoteName('extension_id') . '=' . $db->Quote($this->getKomentoComponentId());
		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Update admin menu
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function updateAdminMenu()
	{	
		$this->engine();

		$db = KT::db();

		$komentoComponentId = $this->getKomentoComponentId();

		if ($komentoComponentId) {
			$query	= 'UPDATE '. $db->nameQuote('#__menu')
					. ' SET ' . $db->nameQuote('component_id') . ' = ' . $db->quote($komentoComponentId)
					. ' WHERE ' . $db->nameQuote('client_id') . ' = ' . $db->quote(1)
					. ' AND ' . $db->nameQuote('title') . ' LIKE ' . $db->quote('com_komento%')
					. ' AND ' . $db->nameQuote('component_id') . ' != ' . $komentoComponentId;
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Update components config
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function initConfig()
	{
		$this->engine();

		$db = KT::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__komento_configs')
				. ' WHERE ' . $db->nameQuote('name') . ' = ' . $db->quote('config');

		$db->setQuery($query);

		// If this is a fresh new installation OR upgrade from 2.x
		// Here we also need to check if this is upgrade from 2.x, we need to migrate the config over.
		if (!$db->loadResult()) {
			$file = JPATH_ADMINISTRATOR . '/components/com_komento/defaults/configuration.json';

			$content = file_get_contents($file);
			$registry = new JRegistry($content);

			$obj = new stdClass();
			$obj->name	= 'config';
			$obj->params = $registry->toString();

			$db->insertObject('#__komento_configs', $obj);
		}

		// Once the config is initialized, we need to update it based on any requirement
		// For Joomla2.5, the komento_jquery has to be enabled. 
		$joomlaVersion = $this->getJoomlaVersion();
		
		if ($joomlaVersion < '3.0') {
			$this->updateConfig('komento_jquery', '1');
		}

		return $this->getResultObj(JText::_('COM_KOMENTO_INSTALLATION_CONFIG_INITIALIZED'), true);
	}

	/**
	 * Update the ACL for Komento
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function initACL()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$db = KT::db();

		// Check if the table is already there
		$query = 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__komento_acl');
		$db->setQuery($query);

		if (!$db->loadResult()) {
			
			$usergroupsPath = JPATH_ADMINISTRATOR . '/components/com_komento/defaults/usergroupsacl.json';

			// If the file doesn't exist, quit.
			if (!JFile::exists($usergroupsPath)) {
				return false;
			}

			$contents = file_get_contents($usergroupsPath);

			$usergroups = json_decode($contents);

			$jversion = 'j30';

			foreach ($usergroups->rules as $usergroup => $rules) {
				if (isset($usergroups->mapping->$jversion->$usergroup)) {
					$gid = $usergroups->mapping->$jversion->$usergroup;

					$string = json_encode($rules);

					$query = 'INSERT INTO ' . $db->nameQuote('#__komento_acl') . ' VALUES (null, ' . $db->quote($gid) . ', ' . $db->quote('usergroup') . ', ' . $db->quote($string) . ')';

					$db->setQuery($query);
					$db->query();
				}
			}
		}

		return $this->getResultObj(JText::_('COM_KOMENTO_INSTALLATION_ACL_INITIALIZED'), true);
	}

	public function purgeCaptcha()
	{
		$this->engine();

		$db = KT::db();
		
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__komento_captcha');
		$db->setQuery($query);

		// If this is a NOT fresh installation
		if ($db->loadResult()) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__komento_captcha');

			$db->setQuery($query);
			$db->query();
		}
	}

	public function getJoomlaVersion()
	{
		$jVerArr = explode('.', JVERSION);
		$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public function getKomentoComponentId()
	{
		$this->engine();
		
		$db = KT::db();

		$query 	= 'SELECT ' . $db->nameQuote('extension_id')
			. ' FROM ' . $db->nameQuote('#__extensions')
			. ' WHERE `element`=' . $db->Quote('com_komento')
			. ' AND `type`=' . $db->Quote('component');

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Update the ACL for Komento
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public function activateSystemPlugin()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = KT::db();
		$query = array();

		$query[] = 'SELECT `enabled` FROM ' . $db->qn('#__extensions');
		$query[] = 'WHERE ' . $db->qn('folder') . '=' . $db->Quote('system');
		$query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote('komento');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$isEnabled = $db->loadResult();

		if ($isEnabled) {
			return false;
		}

		$query = array();

		$query[] = 'UPDATE ' . $db->qn('#__extensions') . ' SET ' . $db->qn('enabled') . '=' . $db->Quote('1');
		$query[] = 'WHERE ' . $db->qn('folder') . '=' . $db->Quote('system');
		$query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote('komento');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$state = $db->Query();

		return true;

	}	
}
