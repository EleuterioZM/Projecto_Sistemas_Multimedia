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

class ScraperPluginTitle extends ScraperPlugin
{
	public function process(&$result)
	{
		$nodes = $this->parser->find('title');

		foreach ($nodes as $title) {

			// Only retrieve the first <title> meta tag on the page
			if ($title->innertext) {

				// some title meta the html entities are wrong. e.g. &amp;quot;Trailer&amp;quot;
				// most likely due to two time of htmlentities
				// #3270
				$title->innertext = str_replace('&amp;', '&', $title->innertext);
				$result->title = $title->innertext;
				break;
			}
		}
	}
}
