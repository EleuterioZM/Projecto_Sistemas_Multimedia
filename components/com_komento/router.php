<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ROOT . '/components/com_komento/bootstrap.php');

function KomentoBuildRoute(&$query)
{
	// Declare static variables.
	static $items;
	static $default;
	static $dashboard;
	static $rss;

	// Initialise variables.
	$segments = array();
	$config	= KT::config();

	// Get the relevant menu items if not loaded.
	if (empty($items)) {

		// Get all relevant menu items.
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$items = $menu->getItems('component', 'com_komento');

		// Build an array of serialized query strings to menu item id mappings.
		for ($i = 0, $n = count($items); $i < $n; $i++) {

			// Check to see if we have found the dashboard menu item.
			if (empty($dashboard) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'dashboard')) {
				$dashboard = $items[$i]->id;
			}

			// Check to see if we have found the registration menu item.
			if (empty($feed) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'rss')) {
				$rss = $items[$i]->id;
			}
		}
	}

	if (!empty($query['view'])) {

		if (!isset($query['Itemid'])) {
			// Set menu item directly with the view as the variable string
			// Profile link should be generated with $profile item id
			// If the view is 'profile', then itemid shouhld be set with $profile
			// If the view is 'rss', then the itemid should be set with $rss
			$query['Itemid'] = ${$query['view']};
		}

		// If itemid is empty, then append the view into segments
		// If itemid is not empty, then no need to append segments as the itemid title will be in the address
		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}

		switch ($query['view']) {

			case 'rss':
				if ($query['Itemid'] == $rss) {
					unset ($query['view']);
				}
				break;
			
			default:
			case 'dashboard':
				
				unset($query['view']);

				if ($query['Itemid'] == $dashboard) {
					unset($query['view']);
				}

				// Translate filter urls
				$filter = isset($query['filter']) ? $query['filter'] : null;
				$addFilter = !is_null($filter) ? true : false;

				if ($addFilter) {
					$filterType = FCJString::strtoupper($query['filter']);
					$segments[] = JText::_('COM_KT_DASHBOARD_FILTER_' . $filterType);
				}

				unset($query['filter']);

				// Layout download
				$layout = isset($query['layout']) ? $query['layout'] : null;

				if ($layout) {
					$segments[] = $layout;
				}

				unset($query['layout']);

				break;
		}
	}

	return $segments;
}

function KomentoParseRoute(&$segments)
{
	// Initialise variables.
	$vars = array();
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();
	$total = count($segments);

	// Only run routine if there are segments to parse.
	if ($total < 1) {
		return;
	}

	if (!isset($item)) {
		$vars['view'] = $segments[0];
	} else {
		$vars['view'] = $item->query['view'];
	}

	if ($total == 1 && isset($vars['view']) && $vars['view'] == 'dashboard') {
		if ($segments[0] == 'download' || $segments[0] == 'downloaddata') {
			
			$vars['layout'] = $segments[0];
		} else {

			// Determine if the filter translated name match with the original filter name
			$filterName = KT::router()->getOriginalFilterName($segments[0]);
			$vars['filter'] = $filterName;
		}
	}

	return $vars;
}

