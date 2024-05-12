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

class KomentoTableComments extends KomentoTable
{
	public $id = null;
	public $component = null;
	public $cid = null;
	public $comment = null;
	public $preview = null;
	public $name = null;
	public $title = '';
	public $email = null;
	public $url = null;
	public $ip = null;
	public $created_by = null;
	public $created = null;
	public $modified_by = null;
	public $modified = null;
	public $deleted_by = null;
	public $deleted = null;
	public $flag = null;
	public $published = null;
	public $publish_up = null;
	public $publish_down = null;
	public $sticked = null;
	public $sent = null;
	public $parent_id = null;
	public $depth = null;
	public $lft = null;
	public $rgt = null;
	public $latitude = null;
	public $longitude = null;
	public $address = null;
	public $params = null;
	public $ratings = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_comments' , 'id' , $db);
	}

	public function load($keys = null, $reset = true)
	{
		$state = parent::load($keys, $reset);

		if (!empty($this->params) && is_string($this->params)) {
			$this->params = json_decode($this->params);
		}

		if (!is_object($this->params)) {
			$this->params = new stdClass();
		}

		return $state;
	}

	public function store($updateNulls = false)
	{
		$paramsEncoded = false;

		if (is_object($this->params)) {
			$this->params = json_encode($this->params);

			if (empty($this->params)) {
				$this->params = '{}';
			}

			$paramsEncoded = true;
		}

		$state = parent::store($updateNulls);

		if ($paramsEncoded) {
			$this->params = json_decode($this->params);
		}

		return $state;
	}

	public function initRepliesCount()
	{
		static $_cache = array();

		if (! isset($_cache[$this->id])) {
			$model = KT::model('Comments');
			$_cache[$this->id] = $model->getRepliesCount($this);
		}

		return $_cache[$this->id];
	}
}
