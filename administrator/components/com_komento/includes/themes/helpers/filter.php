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

class KomentoThemesFilter
{
	/**
	 * Renders a filter to list down available extensions on the site that are supported by Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function extensions($name, $selected)
	{
		$items = [
			'all' => 'COM_KOMENTO_ALL_COMPONENTS'
		];

		$extensions = KT::components()->getAvailableComponents();

		if ($extensions) {
			foreach ($extensions as $extension) {
				$items[$extension] = KT::loadApplication($extension)->getComponentName();
			}
		}

		return KT::fd()->html('filter.lists', $name, $items, $selected, ['minWidth' => 280]);
	}
}
