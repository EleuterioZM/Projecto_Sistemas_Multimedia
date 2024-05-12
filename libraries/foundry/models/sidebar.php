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
namespace Foundry\Models;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Models\Base;

abstract class Sidebar extends Base
{
	protected $element = 'sidebar';

	/**
	 * Retrieve menus defined by the respective extension
	 *
	 * @since	1.1.0
	 * @access	protected
	 */
	abstract protected function getMenus();

	/**
	 * Retrieve counters defined by the respective extension
	 *
	 * @since	1.1.0
	 * @access	protected
	 */
	abstract protected function getCount($namespace);

	/**
	 * Centralized method to formats the dataset for sidebar at the back-end
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public static function format($activeView, $items, $extension, $getViewsCallback, $getCountCallback)
	{
		// Initialize default result.
		$result = [];

		$input = \JFactory::getApplication()->input;
		$my = \JFactory::getUser();

		foreach ($items as $item) {
			$uid = uniqid();

			$obj = clone($item);
			$obj->uid = $uid;
			$obj->activeChildType = $input->get($obj->active, '', 'string');
			$obj->count = 0;
			$obj->access = \FH::normalize($obj, 'access', false);

			// Ensure that the user has access to view this menu
			if ($obj->access && !$my->authorise($obj->access, $extension)) {
				continue;
			}

			// Ensure that each menu item has a child property
			$obj->childs = \FH::normalize($obj, 'childs', []);

			if (isset($obj->counter) && !$obj->childs) {
				$obj->count = $getCountCallback($obj->counter);
			}

			$obj->views = $getViewsCallback($obj);
			$obj->isActive = in_array($activeView, $obj->views) ? true : false;

			// For menus without a link, we try to generate it ourselves
			$obj->link = \FH::normalize($obj, 'link', null);

			if (is_null($obj->link) && !$obj->childs) {
				$obj->link = 'index.php?option=' . $extension . '&view=' . $obj->view;
			}

			if (!empty($obj->childs)) {
				$childItems = [];

				foreach ($obj->childs as $child) {

					// Clone the child object.
					$childObj = clone($child);

					// Ensure that the user has access to view this menu
					$childObj->access = \FH::normalize($childObj, 'access', false);

					if ($childObj->access && !$my->authorise($childObj->access, $extension)) {
						continue;
					}

					// Let's get the URL.
					$url = ['index.php?option=' . $extension];
					$query = (array) $child->url;

					// Set the url into the child item so that we can determine the active submenu.
					$childObj->url = $child->url;

					if ($query) {

						foreach ($query as $queryKey => $queryValue) {

							if ($queryValue) {
								$url[]	= $queryKey . '=' . $queryValue;
							}

							// If this is a call to the controller, it must have a valid token id.
							if ($queryKey == 'controller') {
								$url[] = \FH::token() . '=1';
							}
						}
					}

					// Set the item link.
					$childObj->link = implode('&amp;', $url);

					// Initialize the counter
					$childObj->count = 0;

					// Check if there's any sql queries to execute.
					if (isset($childObj->counter)) {
						$childObj->count = $getCountCallback($childObj->counter);

						$obj->count += $childObj->count;
					}

					$childObj->activeLayouts = \FH::normalize($childObj, 'activeLayouts', []);

					// Add a unique id for the side bar for accordion purposes.
					$childObj->uid = $uid;

					// Determine if the current child menu should be active
					$childObj->isActive = false;

					if (($obj->activeChildType == $childObj->url->{$obj->active} || in_array($obj->activeChildType, $childObj->activeLayouts)) && ($activeView === $childObj->url->view)) {
						$childObj->isActive = true;
					}

					// Add the menu item to the child items.
					$childItems[] = $childObj;
				}

				$obj->childs = $childItems;

				// Sort child items
				$sort = \FH::normalize($obj, 'sort', false);

				if ($sort) {
					usort($obj->childs, function($a, $b) {
						$al = strtolower(\JText::_($a->title));
						$bl = strtolower(\JText::_($b->title));

						if ($al == $bl) {
							return 0;
						}

						return ($al > $bl) ? +1 : -1;
					});
				}
			}

			$result[] = $obj;
		}

		return $result;
	}

	/**
	 * Retrieves a list of menus on the sidebar
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getItems($activeView)
	{
		$menus = $this->getMenus();

		$views = function($menu) {
			return $this->getViews($menu);
		};

		$counter = function($namespace) {
			return $this->getCount($namespace);
		};

		$result = $this->format($activeView, $menus, $this->fd->getComponentName(), $views, $counter);

		return $result;
	}


	/**
	 * Given a list of sidebar structure, collect all the views used in the childs
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function getViews($menu)
	{
		$views = [$menu->view];

		if (isset($menu->childs) && $menu->childs) {
			foreach ($menu->childs as $child) {
				$views[] = $child->url->view;
			}
		}

		$views = array_unique($views);

		return $views;
	}
}
