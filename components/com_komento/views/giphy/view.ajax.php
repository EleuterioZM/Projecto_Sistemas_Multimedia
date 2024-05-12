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

require_once(dirname(__DIR__) . '/views.php');

class KomentoViewGiphy extends KomentoView
{
	/**
	 * Search for GIFs and stickers of GIPHY via query
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function search()
	{
		$giphy = KT::giphy();

		if (!$giphy->isEnabled()) {
			return $this->ajax->reject('The feature has been disabled.');
		}

		// Get the search query input
		$query = $this->input->get('query', '', 'string');

		// The type of GIPHY. Either gifs or stickers only
		$type = $this->input->get('type', 'gifs', 'string');

		// Search and get the data
		$data = $giphy->getData($type, $query);

		$hasGiphies = true;

		if (!$data) {
			$data = false;
			$hasGiphies = false;
		}

		$theme = KT::themes();
		$html = $theme->fd->html('giphy.list', $data, $type);

		return $this->ajax->resolve($hasGiphies, $html);
	}
}