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

class ScraperPluginDescription extends ScraperPlugin
{
	const PATTERN = '/\<meta name="description" content=*[\"\']{0,1}([^\"\\>]*)/i';

	/**
	 * Processes the contents and match the meta description
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function process(&$result)
	{
		preg_match(self::PATTERN, $this->contents, $matches);

		$result->description = isset($matches[1]) ? $matches[1] : '';
	}
}
