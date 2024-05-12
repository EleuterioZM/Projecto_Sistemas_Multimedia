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

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptUpdateStrictSQLMode extends KomentoMaintenanceScript
{
    public static $title = "Update strict SQL MODE for Joomla 4.";
    public static $description = 'Making the necessary changes for the tables for Joomla 4\'s strict SQL mode';

    public function main()
    {
        $db = KT::db();

        $queries = [];
        $queries[] = "ALTER TABLE `#__komento_mailq` MODIFY `body` LONGTEXT";

        foreach ($queries as $query) {
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }
}