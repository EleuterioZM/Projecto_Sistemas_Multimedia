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

class KomentoMaintenanceScriptMigrateThemeOverrideNotes extends KomentoMaintenanceScript
{
	public static $title = "Migrate themes override notes";
	public static $description = 'To migrate and store the themes override notes into configs table.';

	public function main()
	{
		$db = KT::db();

		$query = 'SELECT `params` FROM `#__komento_configs` WHERE `name` = ' . $db->Quote('themeoverride');
		$db->setQuery($query);

		$params = $db->loadResult();

		if (!$params) {
			// there this is first time migration.
			$data = [];

			// get all the
			$query = "select `file_id`, `notes` from `#__komento_themes_overrides`";
			$db->setQuery($query);

			$items = $db->loadObjectList();
			if ($items) {
				foreach ($items as $item) {

					// get the file id based on relative path
					$segments = explode('/html/com_komento/', $item->file_id);

					if ($segments && isset($segments[1])) {

						$relativePath = '/' . ltrim($segments[1], '/');

						$id = base64_encode($relativePath);

						$obj = new stdClass();
						$obj->file_id = $item->file_id;
						$obj->notes = $item->notes;

						$data[$id] = $obj;
					}

				}
			}

			// save into config table.
			$themeConfig = KT::table('Configs');
			$themeConfig->params = json_encode($data);
			$state = $themeConfig->store('themeoverride');

			if ($state) {
				// now we need to drop #__komento_themes_overrides
				$query = "DROP TABLE IF EXISTS `#__komento_themes_overrides`";
				$db->setQuery($query);
				$db->query();
			}

		}

		return true;
	}
}