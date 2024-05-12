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
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;
use Foundry\Libraries\Scripts;

class Location extends Base
{
	/**
	 * Renders the location html wrapper that is used by respective extensions
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function wrapper($options = [])
	{
		$preview = \FH::normalize($options, 'preview', true);
		$title = \FH::normalize($options, 'title', true);
		$footer = \FH::normalize($options, 'footer', true);
		$value = \FH::normalize($options, 'value', '');
		$latitude = \FH::normalize($options, 'latitude', '');
		$longitude = \FH::normalize($options, 'longitude', '');
		$type = \FH::normalize($options, 'type', 'inline');

		$theme = $this->getTemplate();
		$theme->set('preview', $preview);
		$theme->set('title', $title);
		$theme->set('footer', $footer);
		$theme->set('value', $value);
		$theme->set('latitude', $latitude);
		$theme->set('longitude', $longitude);

		$output = $theme->output('html/location/' . $type);

		return $output;
	}

	/**
	 * Renders the location item HTML
	 *
	 * @since	1.1.3
	 * @access	public
	 */
	public function list($options = [])
	{
		$locations = \FH::normalize($options, 'locations', []);

		$theme = $this->getTemplate();
		$theme->set('locations', $locations);

		$output = $theme->output('html/location/list');

		return $output;
	}
}
