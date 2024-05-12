<?php
/**
 * Installer Script Helper
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2016 Tassos Marinos All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PlgSystemNrframeworkInstallerScriptHelper
{
	public $name = '';
	public $alias = '';
	public $extname = '';
	public $extension_type = '';
	public $plugin_folder = 'system';
	public $module_position = 'status';
	public $client_id = 1;
	public $install_type = 'install';
	public $show_message = true;
	public $autopublish = true;
	public $db = null;
	public $app = null;
	public $installedVersion;

	public function __construct(&$params)
	{
		$this->extname = $this->extname ?: $this->alias;
		$this->db = JFactory::getDbo();
		$this->app = JFactory::getApplication();
		$this->installedVersion = $this->getVersion($this->getInstalledXMLFile());
	}

	/**
	 *  Preflight event
	 *
	 *  @param   string            
	 *  @param   JAdapterInstance
	 *
	 *  @return  boolean                      
	 */
	public function preflight($route, $adapter)
	{
		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		JFactory::getLanguage()->load('plg_system_novaraininstaller', JPATH_PLUGINS . '/system/novaraininstaller');

		if ($this->show_message && $this->isInstalled())
		{
			$this->install_type = 'update';
		}

		if ($this->onBeforeInstall() === false)
		{
			return false;
		}
	}

	/**
	 *  Preflight event
	 *
	 *  @param   string            
	 *  @param   JAdapterInstance
	 *
	 *  @return  boolean                      
	 */
	public function postflight($route, $adapter)
	{
		JFactory::getLanguage()->load($this->getPrefix() . '_' . $this->extname, $this->getMainFolder());

		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		if ($this->onAfterInstall() === false)
		{
			return false;
		}

		if ($route == 'install' && $this->autopublish)
		{
			$this->publishExtension();
		}

		if ($this->show_message)
		{
			$this->addInstalledMessage();
		}

		JFactory::getCache()->clean('com_plugins');
		JFactory::getCache()->clean('_system');
	}

	public function isInstalled()
	{
		if (!is_file($this->getInstalledXMLFile()))
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select('extension_id')
			->from('#__extensions')
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote($this->extension_type))
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote($this->getElementName()));
		$this->db->setQuery($query, 0, 1);
		$result = $this->db->loadResult();

		return empty($result) ? false : true;
	}

	public function getMainFolder()
	{
		switch ($this->extension_type)
		{
			case 'plugin' :
				return JPATH_SITE . '/plugins/' . $this->plugin_folder . '/' . $this->extname;

			case 'component' :
				return JPATH_ADMINISTRATOR . '/components/com_' . $this->extname;

			case 'module' :
				return JPATH_ADMINISTRATOR . '/modules/mod_' . $this->extname;

			case 'library' :
				return JPATH_SITE . '/libraries/' . $this->extname;
		}
	}

	public function getInstalledXMLFile()
	{
		return $this->getXMLFile($this->getMainFolder());
	}

	public function getCurrentXMLFile()
	{
		return $this->getXMLFile(__DIR__);
	}

	public function getXMLFile($folder)
	{
		switch ($this->extension_type)
		{
			case 'module' :
				return $folder . '/mod_' . $this->extname . '.xml';
			default :
				return $folder . '/' . $this->extname . '.xml';
		}
	}

	public function foldersExist($folders = array())
	{
		foreach ($folders as $folder)
		{
			if (is_dir($folder))
			{
				return true;
			}
		}

		return false;
	}

	public function publishExtension()
	{
		switch ($this->extension_type)
		{
			case 'plugin' :
				$this->publishPlugin();

			case 'module' :
				$this->publishModule();
		}
	}

	public function publishPlugin()
	{
		$query = $this->db->getQuery(true)
			->update('#__extensions')
			->set($this->db->quoteName('enabled') . ' = 1')
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote($this->extname))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote($this->plugin_folder));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function publishModule()
	{
		// Get module id
		$query = $this->db->getQuery(true)
			->select('id')
			->from('#__modules')
			->where($this->db->quoteName('module') . ' = ' . $this->db->quote('mod_' . $this->extname))
			->where($this->db->quoteName('client_id') . ' = ' . (int) $this->client_id);
		$this->db->setQuery($query, 0, 1);
		$id = $this->db->loadResult();

		if (!$id)
		{
			return;
		}

		// check if module is already in the modules_menu table (meaning is is already saved)
		$query->clear()
			->select('moduleid')
			->from('#__modules_menu')
			->where($this->db->quoteName('moduleid') . ' = ' . (int) $id);
		$this->db->setQuery($query, 0, 1);
		$exists = $this->db->loadResult();

		if ($exists)
		{
			return;
		}

		// Get highest ordering number in position
		$query->clear()
			->select('ordering')
			->from('#__modules')
			->where($this->db->quoteName('position') . ' = ' . $this->db->quote($this->module_position))
			->where($this->db->quoteName('client_id') . ' = ' . (int) $this->client_id)
			->order('ordering DESC');
		$this->db->setQuery($query, 0, 1);
		$ordering = $this->db->loadResult();
		$ordering++;

		// publish module and set ordering number
		$query->clear()
			->update('#__modules')
			->set($this->db->quoteName('published') . ' = 1')
			->set($this->db->quoteName('ordering') . ' = ' . (int) $ordering)
			->set($this->db->quoteName('position') . ' = ' . $this->db->quote($this->module_position))
			->where($this->db->quoteName('id') . ' = ' . (int) $id);
		$this->db->setQuery($query);
		$this->db->execute();

		// add module to the modules_menu table
		$query->clear()
			->insert('#__modules_menu')
			->columns(array($this->db->quoteName('moduleid'), $this->db->quoteName('menuid')))
			->values((int) $id . ', 0');
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function addInstalledMessage()
	{
		JFactory::getApplication()->enqueueMessage(
			JText::sprintf(
				JText::_($this->install_type == 'update' ? 'NRI_THE_EXTENSION_HAS_BEEN_UPDATED_SUCCESSFULLY' : 'NRI_THE_EXTENSION_HAS_BEEN_INSTALLED_SUCCESSFULLY'),
				'<strong>' . JText::_($this->name) . '</strong>',
				'<strong>' . $this->getVersion() . '</strong>',
				$this->getFullType()
			)
		);
	}

	public function getPrefix()
	{
		switch ($this->extension_type)
		{
			case 'plugin';
				return JText::_('plg_' . strtolower($this->plugin_folder));

			case 'component':
				return JText::_('com');

			case 'module':
				return JText::_('mod');

			case 'library':
				return JText::_('lib');

			default:
				return $this->extension_type;
		}
	}

	public function getElementName($type = null, $extname = null)
	{
		$type = is_null($type) ? $this->extension_type : $type;
		$extname = is_null($extname) ? $this->extname : $extname;

		switch ($type)
		{
			case 'component' :
				return 'com_' . $extname;

			case 'module' :
				return 'mod_' . $extname;

			case 'plugin' :
			default:
				return $extname;
		}
	}

	public function getFullType()
	{
		return JText::_('NRI_' . strtoupper($this->getPrefix()));
	}

	public function isPro()
	{
		$versionFile = __DIR__ . "/version.php";

		// If version file does not exist we assume a PRO version
		if (!JFile::exists($versionFile))
		{
			return true;
		}

		// Load version file
		require_once $versionFile;
		return (bool) $NR_PRO;
	}

	public function getVersion($file = '')
	{
		$file = $file ?: $this->getCurrentXMLFile();

		if (!is_file($file))
		{
			return '';
		}

		$xml = JInstaller::parseXMLInstallFile($file);

		if (!$xml || !isset($xml['version']))
		{
			return '';
		}

		return $xml['version'];
	}

	/**
	 *  Checks wether the extension can be installed or not
	 *
	 *  @return  boolean  
	 */
	public function canInstall()
	{

		// The extension is not installed yet. Accept Install.
		if (!$installed_version = $this->getVersion($this->getInstalledXMLFile()))
		{
			return true;
		}

		// Path to extension's version file
		$versionFile = $this->getMainFolder() . "/version.php";
		$NR_PRO = true;

		// If version file does not exist we assume we have a PRO version installed
		if (file_exists($versionFile))
		{
			require_once($versionFile);
		}

		// The free version is installed. Accept install.
		if (!(bool)$NR_PRO)
		{
			return true;
		}

		// Current package is a PRO version. Accept install.
		if ($this->isPro())
		{
			return true;
		}

		// User is trying to update from PRO version to FREE. Do not accept install.
		JFactory::getLanguage()->load($this->getPrefix() . '_' . $this->extname, __DIR__);

		JFactory::getApplication()->enqueueMessage(
			JText::_('NRI_ERROR_PRO_TO_FREE'), 'error'
		);

		JFactory::getApplication()->enqueueMessage(
			html_entity_decode(
				JText::sprintf(
					'NRI_ERROR_UNINSTALL_FIRST',
					'<a href="http://www.tassos.gr/joomla-extensions/' . $this->alias . '" target="_blank">',
					'</a>',
					JText::_($this->name)
				)
			), 'error'
		);	

		return false;	
	}

	/**
	 *  Checks if current version is newer than the installed one
	 *  Used for Novarain Framework
	 *
	 *  @return  boolean  [description]
	 */
	public function isNewer()
	{
		if (!$installed_version = $this->getVersion($this->getInstalledXMLFile()))
		{
			return true;
		}

		$package_version = $this->getVersion();

		return version_compare($installed_version, $package_version, '<=');
	}

	/**
	 *  Helper method triggered before installation
	 *
	 *  @return  bool
	 */
	public function onBeforeInstall()
	{
		if (!$this->canInstall())
		{
			return false;
		}
	}

	/**
	 *  Helper method triggered after installation
	 */
	public function onAfterInstall()
	{

	}

	/**
	 *  Delete files
	 *
	 *  @param   array   $folders
	 */
	public function deleteFiles($files = array())
	{
	    foreach ($files as $key => $file)
        {
            JFile::delete($file);
        }
	}

	/**
	 *  Deletes folders
	 *
	 *  @param   array   $folders  
	 */
	public function deleteFolders($folders = array())
	{
		foreach ($folders as $folder)
		{
			if (!is_dir($folder))
			{
				continue;
			}

			JFolder::delete($folder);
		}
	}

	public function dropIndex($table, $index)
	{	
		$db = $this->db;

		// Check if index exists first
		$query = 'SHOW INDEX FROM ' . $db->quoteName('#__' . $table) . ' WHERE KEY_NAME = ' . $db->quote($index);
        $db->setQuery($query);
        $db->execute();

        if (!$db->loadResult())
        {
        	return;
        }

        // Remove index
        $query = 'ALTER TABLE ' . $db->quoteName('#__' . $table) . ' DROP INDEX ' . $db->quoteName($index);
        $db->setQuery($query);
        $db->execute(); 
	}

    public function dropUnwantedTables($tables) {

        if (!$tables) {
            return;
        }

        foreach ($tables as $table) {
            $query = "DROP TABLE IF EXISTS #__".$this->db->escape($table);
            $this->db->setQuery($query);
            $this->db->execute();
        }
    }

	public function dropUnwantedColumns($table, $columns) {

        if (!$columns || !$table) {
            return;
        }

        $db = $this->db;

        // Check if columns exists in database
        function qt($n) {
            return(JFactory::getDBO()->quote($n));
        }
        
        $query = 'SHOW COLUMNS FROM #__'.$table.' WHERE Field IN ('.implode(",", array_map("qt", $columns)).')';
        $db->setQuery($query);
        $rows = $db->loadColumn(0);

        // Abort if we don't have any rows
        if (!$rows) {
            return;
        }

        // Let's remove the columns
        $q = "";
        foreach ($rows as $key => $column) {
            $comma = (($key+1) < count($rows)) ? "," : "";
            $q .= "drop ".$this->db->escape($column).$comma;
        }

        $query = "alter table #__".$table." $q";

        $db->setQuery($query);
        $db->execute();
    }

  	public function fetch($table, $columns = "*", $where = null, $singlerow = false) {
        if (!$table) {
            return;
        }

        $db = $this->db;
        $query = $db->getQuery(true);

        $query
            ->select($columns)
            ->from("#__$table");
        
        if (isset($where)) {
            $query->where("$where");
        }
        
        $db->setQuery($query);
 
        return ($singlerow) ? $db->loadObject() : $db->loadObjectList();
    }

    /**
     *  Load the Novarain Framework
     *
     *  @return  boolean
     */
	public function loadFramework()
	{
		if (is_file(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
		{
			include_once JPATH_PLUGINS . '/system/nrframework/autoload.php';
		}
	}

	/**
	 *  Re-orders plugin after passed array of plugins
	 *
	 *  @param   string  $plugin            Plugin element name
	 *  @param   array   $lowerPluginOrder  Array of plugin element names
	 *
	 *  @return  boolean
	 */
	public function pluginOrderAfter($lowerPluginOrder)
    {

        if (!is_array($lowerPluginOrder) || !count($lowerPluginOrder))
        {
            return;
        }
        
        $db = $this->db;

        // Get plugins max order
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName('b.ordering'))
            ->from($db->quoteName('#__extensions', 'b'))
            ->where($db->quoteName('b.element') . ' IN ("'.implode("\",\"",$lowerPluginOrder).'")')
            ->order('b.ordering desc');

        $db->setQuery($query);
        $maxOrder = $db->loadResult();

        if (is_null($maxOrder))
        {
            return;
        }

        // Get plugin details
        $query
            ->clear()
            ->select(array($db->quoteName('extension_id'), $db->quoteName('ordering')))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->alias));

        $db->setQuery($query);
        $pluginInfo = $db->loadObject();

        if (!isset($pluginInfo->ordering) || $pluginInfo->ordering > $maxOrder)
        {
            return;
        }

        // Update the new plugin order
        $object = new stdClass();
        $object->extension_id = $pluginInfo->extension_id;
        $object->ordering = ($maxOrder + 1);

		try {
			$db->updateObject('#__extensions', $object, 'extension_id');
		} catch (Exception $e) {
			return $e->getMessage();
		}
    }
}
