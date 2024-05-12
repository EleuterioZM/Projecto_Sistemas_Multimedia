<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class VideoParserBase
{
	protected $width = null;
	protected $height = null;
	protected $fullWidth = false;

	public function __construct($width, $height, $fullWidth = false)
	{
		$this->width = $width;
		$this->height = $height;
		$this->fullWidth = $fullWidth;
	}

	final protected function getWidth()
	{
		if ($this->isFullWidth()) {
			return '100%';
		}

		return $this->width . 'px';
	}

	final protected function getHeight()
	{
		return $this->height . 'px';
	}

	final protected function isFullWidth()
	{
		return $this->fullWidth;
	}
}
