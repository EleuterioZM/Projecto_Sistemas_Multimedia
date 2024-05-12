<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * To extend, your script class must in the format of:
 * KomentoMaintenanceScript[FileName]
 *
 * Scripts should be placed in /administrator/com_komento/updates/[version]
 */
abstract class KomentoMaintenanceScript
{
	/**
	 * The title of your script
	 * @var string
	 */
	public static $title;

	/**
	 * The description of your script
	 * @var string
	 */
	public static $description;

	public $error;

	/**
	 * The main function that is called by the maintenance library
	 * @return Boolean Result of the script execution
	 */
	abstract public function main();

	public function setError($msg)
	{
		$this->error = $msg;
	}

	public function hasError()
	{
		return !empty($this->error);
	}

	public function getError()
	{
		return $this->error;
	}
}
