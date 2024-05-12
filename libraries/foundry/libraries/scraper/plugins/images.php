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

class ScraperPluginImages extends ScraperPlugin
{
	/**
	 * Scan and match all images on the document
	 *
	 * @since	1.0.0
	 * @access	public
	 */ 	
	public function process(&$data)
	{
		if (!$this->parser) {
			return;
		}
		
		// Find all image tags on the page.
		$images = [];
		$items = $this->parser->find('img');

		foreach ($items as $image) {

			if (!$image->src) {
				continue;
			}

			// Some image source is not valid. We can determine this by checking it's width, if the width is exists.
			if (isset($image->width) && !$image->width) {
				continue;
			}

			// If there's a ../ , we need to replace it.
			if (stristr($image->src, '/../') !== false) {
				$image->src = str_ireplace('/../', '/', $image->src);
			}

			if (stristr($image->src, 'http://') === false && stristr($image->src, 'https://') === false) {
				$image->src = $this->url . '/' . $image->src;
			}

			// Convert those html entity &amp; from URL
			$image->src = html_entity_decode($image->src);

			$images[] = $image->src;
		}

		// Ensure that there are no duplicate images.
		$images = array_values(array_unique($images, SORT_STRING));

		$data->images = $images;
	}
}
