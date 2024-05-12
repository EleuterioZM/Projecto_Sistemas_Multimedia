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

class KomentoTableConfigs extends KomentoTable
{
	public $component = null;
	public $params = null;
	public $name = null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__komento_configs', 'name', $db);
	}

	public function store($key = 'config')
	{
		$sql = KT::sql();

		$sql->select('#__komento_configs')
			->column('1', '', 'count', true)
			->where('name', $key);

		$exists	= ($sql->loadResult() > 0) ? true : false;

		$data = new stdClass();
		$data->name = empty($this->name) ? $key : $this->name ;
		$data->params = trim($this->params);

		$database = KT::db();

		if ($exists) {
			return $database->updateObject('#__komento_configs', $data, 'name');
		}

		return $database->insertObject('#__komento_configs', $data);
	}
}
