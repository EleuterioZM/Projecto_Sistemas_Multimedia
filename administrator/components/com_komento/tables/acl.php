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

class KomentoTableAcl extends KomentoTable
{
	public $id = null;
	public $cid = null;
	public $type = null;
	public $rules = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_acl', 'id', $db);
	}

	public function compositeLoad($cid, $type, $reset = true)
	{
		if ($reset) {
			$this->reset();
		}

		$sql = KT::sql();

		$sql->select('#__komento_acl')
			->where('type', $type)
			->where('cid', $cid);

		$result = $sql->loadObject();

		if (empty($result)) {
			$this->cid = $cid;
			$this->type = $type;
		} else {
			$this->bind($result);
		}

		return $this;
	}
}
