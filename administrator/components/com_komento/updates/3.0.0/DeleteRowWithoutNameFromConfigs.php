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

class KomentoMaintenanceScriptDeleteRowWithoutNameFromConfigs extends KomentoMaintenanceScript
{
    public static $title = 'Delete row without value from configs table';
    public static $description = 'Delete row that has no value in name column from configs table.';

    public function main()
    {
        $db = KT::db();
        $sql = $db->sql();
        $state = true;

        $sql->raw('DELETE FROM `#__komento_configs` WHERE `name` = ' . $db->Quote(''));
        $db->setQuery($sql);
        $state = $db->query();

        return $state;
    }
}
