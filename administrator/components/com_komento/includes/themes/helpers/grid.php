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

class KomentoThemesGrid
{
	/**
	 * Renders publish / unpublish icon
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function published($obj, $controllerName = '', $key = '', $tasks = [], $tooltips = [], $classes = [], $allowed = true)
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'state';

		// array_replace is only supported php>5.3
		// While array_replace goes by base, replacement
		// Using + changes the order where base always goes last

		$classes += [
			-1 => 'trash',
			0 => 'unpublish',
			1 => 'publish',
			2 => 'pending'
		];
		
		$tasks += [
			-1 => 'publish',
			0 => 'publish',
			1 => 'unpublish',
			2 => 'publish'
		];

		$tooltips += [
			-1 => 'Trashed item',
			0 => 'Click to publish this item',
			1 => 'Click to unpublish this item',
			2 => 'Click to publish this item'
		];

		$class = FH::normalize($classes, $obj->$key, '');
		$task = FH::normalize($tasks, $obj->$key, '');
		$tooltip = FH::normalize($tooltips, $obj->$key, '');

		return KT::fd()->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders featured / unfeatured icon.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function featured($obj , $controllerName = '' , $key = '' , $task = '' , $allowed = true , $tooltip = array())
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'default';
		$task = !empty($task) ? $task : 'toggleDefault';

		// Default is unfeatured
		$class = 'default';
		$tooltip = isset($tooltip[0]) ? $tooltip[0] : JText::_('COM_KT_GRID_TOOLTIP_FEATURE_ITEM', true);

		if ($obj->$key == 1) {
			$class = 'featured';
			$tooltip = '';

			if ($allowed) {
				$tooltip = isset($tooltip[1]) ? $tooltip[1] : JText::_('COM_KT_GRID_TOOLTIP_UNFEATURE_ITEM', true);
			}
		}

		return KT::fd()->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}
}
