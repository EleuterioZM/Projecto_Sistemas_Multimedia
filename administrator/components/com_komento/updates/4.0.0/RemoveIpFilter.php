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

class KomentoMaintenanceScriptRemoveIpFilter extends KomentoMaintenanceScript
{
	public static $title = "Remove IPFilter table";
	public static $description = 'To remove unused komento tables from the site';

	public function main()
	{
		$db = KT::db();

		// now we need to drop #__komento_themes_overrides
		$query = "DROP TABLE IF EXISTS `#__komento_ipfilter`";
		$db->setQuery($query);
		$db->query();
	}
}