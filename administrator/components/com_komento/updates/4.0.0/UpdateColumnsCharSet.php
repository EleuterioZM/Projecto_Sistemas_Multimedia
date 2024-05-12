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

class KomentoMaintenanceScriptUpdateColumnsCharSet extends KomentoMaintenanceScript
{
	public static $title = "Update Database Columns Charset";
	public static $description = "Update database columns charset to support utf8mb4 if database server supported this charset.";

	public function main()
	{
		$db = KT::db();

		$jConfig = JFactory::getConfig();
		$dbType = $jConfig->get('dbtype');

		if (($dbType == 'mysql' || $dbType == 'mysqli') && $db->hasUTF8mb4Support()) {
			$queries = [];

			$query = "ALTER TABLE `#__komento_comments`";
			$query .= " MODIFY `comment` Text CHARACTER SET utf8mb4 NOT NULL,";
			$query .= " MODIFY `preview` Text CHARACTER SET utf8mb4 NOT NULL";
			$queries[] = $query;

			foreach ($queries as $query) {
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}
}