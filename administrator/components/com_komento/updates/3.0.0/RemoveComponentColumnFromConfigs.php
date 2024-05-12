<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptRemoveComponentColumnFromConfigs extends KomentoMaintenanceScript
{
    public static $title = 'Remove component column from configs table';
    public static $description = 'Remove unused component column from configs table.';

    public function main()
    {
        $db = KT::db();
        $sql = $db->sql();
        $state = true;

        $table = '#__komento_configs';

        $columns = $db->getColumns($table);

        if (in_array('component', $columns)) {
            $sql->raw('ALTER TABLE `#__komento_configs` DROP COLUMN `component`');
            $db->setQuery($sql);
            $state = $db->query();
        }

        return $state;
    }
}
