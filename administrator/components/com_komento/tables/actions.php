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

class KomentoTableActions extends KomentoTable
{
	public $id = null;
	public $type = null;
	public $comment_id = null;
	public $action_by = null;
	public $actioned = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_actions' , 'id' , $db);
	}
}
