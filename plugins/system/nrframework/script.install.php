<?php
/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemNRFrameworkInstallerScript extends PlgSystemNRFrameworkInstallerScriptHelper
{
	public $name = 'NOVARAIN_FRAMEWORK';
	public $alias = 'nrframework';
	public $extension_type = 'plugin';

	public function onBeforeInstall()
	{
		if (!$this->isNewer())
		{
			return false;
		}
	}
}
