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

class KomentoMaintenanceScriptUpdateCidColumnType extends KomentoMaintenanceScript
{
    public static $title = 'Update CID column type in commnents table';
    public static $description = 'Update CID column type in commnents table';

    public function main()
    {
        $columnExist = true;
        $db = KT::db();
        $sql = $db->sql();

        $columnExist = $db->isColumnExists('#__komento_comments', 'dummy');

        if (!$columnExist) {

            $query = "ALTER TABLE `#__komento_comments` MODIFY COLUMN `cid` varchar(20) NOT NULL;";

            $sql->raw($query);
            $db->setQuery($sql);
            $db->query();

            $query = "ALTER TABLE `#__komento_comments` ADD COLUMN `dummy` tinyint(1) NULL default '1' AFTER `params`;";

            $sql->raw($query);
            $db->setQuery($sql);
            $db->query();
        }

        return true;
    }
}
