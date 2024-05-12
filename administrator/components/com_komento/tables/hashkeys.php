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

class KomentoTableHashkeys extends KomentoTable
{
	public $id = null;
	public $uid = null;
	public $type = null;
	public $key = null;
	public $state = null;

	public function __construct(&$db)
	{
		parent::__construct('#__komento_hashkeys', 'id', $db);
	}

	public function loadByKey($key)
	{
		$sql = KT::sql();

		$sql->select( '#__komento_hashkeys' )
			->where( 'key', $key );

		$data	= $sql->loadObject();

		return parent::bind( $data );
	}

	/**
	 * Storess a hashkey
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{
		if (!$this->key) {
			$this->key = $this->generate();
		}

		return parent::store($updateNulls);
	}

	/**
	 * Generates a hashkey
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function generate()
	{
		$key = md5($this->uid . $this->type . FH::date()->toSql());

		return FCJString::substr($key, 0, 12);
	}
}