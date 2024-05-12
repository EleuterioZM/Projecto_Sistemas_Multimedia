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
use Joomla\CMS\Pagination\Pagination;

class Table extends Base
{
	/**
	 * Renders a check all checkbox in a table
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function checkall()
	{
		$theme = $this->getTemplate();
		$output = $theme->output('html/table/checkall');

		return $output;
	}

	/**
	 * Renders a checkbox for each table row.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function id($number, $value, $allowed = true, $checkedOut = false, $name = 'cid')
	{
		$theme = $this->getTemplate();
		$theme->set('allowed', $allowed);
		$theme->set('number', $number);
		$theme->set('name', $name);
		$theme->set('checkedOut', $checkedOut);
		$theme->set('value', $value);

		$contents = $theme->output('html/table/id');

		return $contents;
	}

	/**
	 * Renders a lock/unlock icon in a grid
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function locked($isLocked, $actionAllowed, $options = [])
	{
		$tooltip = \FH::normalize($options, 'tooltip', '');
		$task = \FH::normalize($options, 'task', '');
		$class = $isLocked ? 'locked' : 'unlocked';

		$themes = $this->getTemplate();
		$themes->set('actionAllowed', $actionAllowed);
		$themes->set('tooltip', $tooltip);
		$themes->set('task', $task);
		$themes->set('class', $class);

		return $themes->output('html/table/locked');
	}

	/**
	 * Renders core icon.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function core($isCore, $options = [])
	{
		$tooltip = \FH::normalize($options, 'tooltip', '');
		$class = $isCore ? 'publish' : 'unpublish';

		$themes = $this->getTemplate();
		$themes->set('tooltip', $tooltip);
		$themes->set('class', $class);

		return $themes->output('html/table/core');
	}

	/**
	 * Renders publish/unpublish icon.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function published($class, $actionAllowed, $options = [])
	{
		$tooltip = \FH::normalize($options, 'tooltip', '');
		$task = \FH::normalize($options, 'task', '');

		// Ensure that it is translated
		$tooltip = \JText::_($tooltip);

		$themes = $this->getTemplate();
		$themes->set('actionAllowed', $actionAllowed);
		$themes->set('tooltip', $tooltip);
		$themes->set('task', $task);
		$themes->set('class', $class);

		return $themes->output('html/table/published');
	}

	/**
	 * Renders a pending moderation icon
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function moderation($task)
	{
		$themes = $this->getTemplate();
		$themes->set('task', $task);

		return $themes->output('html/table/moderation');
	}

	/**
	 * Renders a sortable table column
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function sort($title, $column, $currentOrdering, $currentDirection, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$title = \JText::_($title);

		$currentOrdering = strtolower($currentOrdering);
		$currentDirection = strtolower($currentDirection);
		
		$column = strtolower($column);

		$themes = $this->getTemplate();
		$themes->set('class', $class);
		$themes->set('column', $column);
		$themes->set('title', $title);
		$themes->set('currentOrdering', $currentOrdering);
		$themes->set('currentDirection', $currentDirection);

		return $themes->output('html/table/sort');
	}

	/**
	 * Renders a button to save order for the table column
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function saveOrder($task)
	{
		$themes = $this->getTemplate();
		$themes->set('task', $task);

		return $themes->output('html/table/save.order');
	}

	/**
	 * Renders an order for each of the table items
	 *
	 * @since	1.1.0
	 * @access	public
	 */
	public function order(Pagination $pagination, $name, $key, $saveOrder = false, $options = [])
	{
		$class = \FH::normalize($options, 'class', '');
		$rowIndex = \FH::normalize($options, 'rowIndex', 0);
		$showArrowIcon = \FH::normalize($options, 'showArrowIcon', true);
		$orderUpTask = \FH::normalize($options, 'orderUpTask', '');
		$orderDownTask = \FH::normalize($options, 'orderDownTask', '');
		$showOrderUpIcon = \FH::normalize($options, 'showOrderUpIcon', true);
		$showOrderDownIcon = \FH::normalize($options, 'showOrderDownIcon', true);
		$accessControl = \FH::normalize($options, 'accessControl', true);
		$total = \FH::normalize($options, 'total', true);

		$themes = $this->getTemplate();
		$themes->set('pagination', $pagination);
		$themes->set('name', $name);
		$themes->set('key', $key);
		$themes->set('saveOrder', $saveOrder);
		$themes->set('class', $class);
		$themes->set('rowIndex', $rowIndex);
		$themes->set('showArrowIcon', $showArrowIcon);
		$themes->set('orderUpTask', $orderUpTask);
		$themes->set('orderDownTask', $orderDownTask);
		$themes->set('showOrderUpIcon', $showOrderUpIcon);
		$themes->set('showOrderDownIcon', $showOrderDownIcon);
		$themes->set('accessControl', $accessControl);
		$themes->set('total', $total);

		return $themes->output('html/table/order');
	}
}