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

require_once(__DIR__ . '/abstract.php');

class KomentoComdpcalendar extends KomentoExtension
{
	public $_item;

	public function __construct($component)
	{
		parent::__construct($component);
	}

	/**
	 * Method to load the event item by given event id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function load($id)
	{
		static $instances = [];

		if (!isset($instances[$id])) {
			$db = KT::db();

			$query = [];
			$query[] = 'SELECT a.* FROM ' . $db->nameQuote('#__dpcalendar_events') . ' AS a';
			$query[] = ' LEFT JOIN ' . $db->nameQuote('#__categories') . ' AS c ON c.`id` = a.`catid`';
			$query[] = ' LEFT JOIN ' . $db->nameQuote('#__users') . ' AS u ON u.`id` = a.`created_by`';
			$query[] = ' LEFT JOIN ' . $db->nameQuote('#__categories') . ' AS parent ON parent.`id` = c.`parent_id`';
			$query[] = ' WHERE a.`id` = ' . $db->quote((int) $id);

			$db->setQuery($query);
			$result = $db->loadObject();

			if (!$result) {
				return $this->onLoadArticleError($id);
			}

			$instances[$id] = $result;
		}

		$this->_item = $instances[$id];

		return $this;
	}

	/**
	 * Retrieve the id of the event items
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContentIds($categories = '')
	{
		$db	= KT::db();
		$query = '';

		if (empty($categories)) {
			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__dpcalendar_events') . ' ORDER BY `id`';
		}

		if (!empty($categories)) {
			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__dpcalendar_events') . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery($query);

		return $db->loadResultArray();
	}

	/**
	 * Retrieves the categories of the DP Calendar
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCategories()
	{
		$db = KT::db();

		$query = [];
		$query[] = 'SELECT a.`id`, a.`title`, a.`level`, a.`parent_id`';
		$query[] = ' FROM `#__categories` AS a';
		$query[] = ' WHERE a.`extension` = ' . $db->quote('com_dpcalendar');
		$query[] = ' AND a.`parent_id` > 0';
		$query[] = ' ORDER BY a.`lft`';

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		foreach ($categories as &$row) {
			$repeat = ($row->level - 1 >= 0) ? $row->level - 1 : 0;
			$row->treename = str_repeat('.&#160;&#160;&#160;', $repeat) . ($row->level - 1 > 0 ? '|_&#160;' : '') . $row->title;
		}

		return $categories;
	}

	public function isListingView()
	{
		return false;
	}

	public function getEventTrigger()
	{
		return 'onContentAfterDisplay';
	}

	/**
	 * Determine if this is the event item page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isEntryView()
	{
		$view = $this->input->get('view', '', 'string');

		return $view === 'event';
	}

	/**
	 * Method to append the comment to the article
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onExecute(&$article, $html, $view, $options = [])
	{
		return $html;
	}

	/**
	 * Method to get allowed context to run Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContext()
	{
		// Entry view's context is definitely com_dpcalendar.event
		if ($this->isEntryView()) {
			return 'com_dpcalendar.event';
		}

		return false;
	}

	/**
	 * Method to get the author name of the event item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAuthorName()
	{
		return $this->_item->author;
	}

	/**
	 * Method to get the permalink of the event item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContentPermalink()
	{
		$link = JRoute::_('index.php?option=com_dpcalendar&view=event&id=' . $this->_item->id . '&calid=' . $this->_item->catid);
		$link = $this->prepareLink($link);

		return $link;
	}

	/**
	 * Method to get event's state
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContentState()
	{
		return $this->_item->state;
	}
}