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

class ScraperPluginKeywords extends ScraperPlugin
{
	const PATTERN = '/\<meta name="keywords" content=*[\"\']{0,1}([^\"\\>]*)/i';

	public function process(&$result)
	{
		preg_match(self::PATTERN, $this->contents, $matches);

		$result->keywords = isset($matches[1]) ? $matches[1] : '';
	}
}