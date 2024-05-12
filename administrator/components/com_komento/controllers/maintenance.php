<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoControllerMaintenance extends KomentoController
{
    public $ajax = null;

    public function __construct()
    {
        parent::__construct();

        $this->ajax = KT::ajax();
    }

    public function runscript()
    {
        // Check for request forgeries
        FH::checkToken();

        // Get the key
        $key = $this->input->get('key', '', 'default');

        // Get the model
        $model = KT::model('Maintenance');
        $script = $model->getItemByKey($key);

        if (!$script) {
            return $this->ajax->reject(JText::_('COM_KOMENTO_MAINTENANCE_SCRIPT_NOT_FOUND'));
        }

        $classname = $script->classname;

        if (!class_exists($classname)) {
            return $this->ajax->reject(JText::_('COM_KOMENTO_MAINTENANCE_CLASS_NOT_FOUND'));
        }

        $class = new $classname;

        try {
            $class->main();
        } catch (Exception $e) {
            return $this->ajax->reject($e->getMessage());
        }

        return $this->ajax->resolve();
    }

    public function getDatabaseStats()
    {
        $path = KOMENTO_ADMIN_UPDATES;

        jimport('joomla.filesystem.file');

        $files = JFolder::files($path, '.json$', true, true);

        $versions = [];

        foreach ($files as $file) {
            $segments = explode('/', $file);

            $version = $segments[count($segments) - 2];

            // we do not want to execute db scripts prior to 3.0.0
            if (!in_array($version, $versions) && version_compare($version, '2.0.11') > 0) {
                $versions[] = $version;
            }
        }

        return $this->ajax->resolve($versions);
    }

    public function synchronizeDatabase()
    {
        $version = $this->input->getString('version');

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        // Explicitly check for 1.0.0 since it is a flag to execute table creation
        if ($version === '1.0.0') {
            $path = KOMENTO_ADMIN_ROOT . '/queries';

            if (!JFolder::exists($path)) {
                return $this->ajax->resolve();;
            }

            $files = JFolder::files($path, '.sql$', true, true);

            $result = [];

            $db = KT::db();

            foreach ($files as $file) {
                $contents = file_get_contents($file);

                $queries = JInstallerHelper::splitSql($contents);

                foreach ($queries as $query) {
                    $query = trim($query);

                    if (!empty($query)) {
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }

            return $this->ajax->resolve();
        }

        $path = KOMENTO_ADMIN_UPDATES . '/' . $version;

        $files = JFolder::files($path, '.json$', true, true);

        $result = [];

        foreach ($files as $file) {
            $contents = json_decode(file_get_contents($file));

            if (!is_array($contents)) {
                // @TODO: Error handling
                return;
            }

            $result = array_merge($result, $contents);
        }

        $tables = [];
        $indexes = [];
        $affected = 0;

        $db = KT::db();

        foreach ($result as $row) {
            $columnExist = true;
            $indexExist = true;

            if (isset($row->column)) {
                // Store the list of tables that needs to be queried
                if (!isset($tables[$row->table])) {
                    $tables[$row->table] = $db->getColumns($row->table);
                }

                // Check if the column is in the fields or not
                $columnExist = in_array($row->column , $tables[$row->table]);
            }

            if (isset($row->index)) {
                if (!isset($indexes[$row->table])) {
                    $indexes[$row->table] = $db->getIndexes($row->table);
                }

                $indexExist = in_array($row->index, $indexes[$row->table]);
            }

            if (!$columnExist || !$indexExist) {
                $db->setQuery($row->query);
                try {
                    $db->query();
                } catch (Exception $e) {
                    $this->ajax->reject($e->getMessage());
                }

                $affected += 1;
            }
        }

        return $this->ajax->resolve();
    }
}
