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

class KomentoFormatter extends KomentoBase
{
	private $type = null;
	private $items = null;
	private $cache = null;
	private $options = null;

	public function __construct($type, $items, $options = [], $cache = true)
	{
		parent::__construct();

		$this->type = $type;
		$this->items = $items;
		$this->cache = $cache;
		$this->options = $options;
	}

	public function execute()
	{
		// If there's no items, skip this altogether
		if (empty($this->items) || empty($this->type)) {
			return $this->items;
		}

		require_once(__DIR__ . '/types/' . $this->type . '.php');

		$class = 'KomentoFormatter' . ucfirst($this->type);

		$obj = new $class($this->items, $this->options, $this->cache);

		return $obj->execute();
	}
}
