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

class KomentoComj2store extends KomentoExtension
{
	public $_item;
	public $_map = [
		'id' => 'id',
		'title' => 'title',
		'hits' => 'hits',
		'created_by' => 'created_by',
		'catid' => 'catid',
		'state' => 'state'
	];

	public function __construct($component)
	{
		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = [];

		if (!isset($instances[$cid])) {
			$db	= KT::db();
			$query = [];

			$query[] = 'SELECT a.`j2store_product_id` AS `id`, a.`created_by`, b.`title`, b.`hits`, b.`state`, c.`id` AS `catid`';
			$query[] = 'FROM ' . $db->nameQuote('#__j2store_products') . ' AS a';
			$query[] = 'LEFT JOIN ' . $db->nameQuote('#__content') . ' AS b ON a.`product_source_id` = b.`id`';
			$query[] = 'LEFT JOIN ' . $db->nameQuote('#__categories') . ' AS c ON b.`catid` = c.`id`';
			$query[] = 'WHERE b.`id` = ' . $db->quote((int) $cid);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$result = $db->loadObject();

			if (!$result) {
				return $this->onLoadArticleError($cid);
			}

			$instances[$cid] = $result;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$db	= KT::db();
		$query = [];

		if (empty($categories)) {
			$query[] = 'SELECT `id` FROM ' . $db->nameQuote('#__content') . ' ORDER BY `id`';
		
		} else {
			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query[] = 'SELECT `id` FROM ' . $db->nameQuote('#__content') . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResultArray();

		return $result;
	}

	public function getCategories()
	{
		$db	= KT::db();
		$query = [];

		$query[] = 'SELECT a.`id`, a.`title`, a.`level`, a.`parent_id` FROM `#__categories` AS a';
		$query[] = 'WHERE a.`extension` = ' . $db->quote('com_content');
		$query[] = 'AND a.`parent_id` > 0';
		$query[] = 'ORDER BY a.`lft`';

		$query = implode(' ', $query);

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

	public function isEntryView()
	{
		$task = $this->input->get('task', '', 'cmd');
		$view = $this->input->get('view', '', 'cmd');

		$isEntryView = $view == 'products' && $task == 'view' ? true : false;
		return $isEntryView;
	}

	public function onExecute(&$article, $html, $view, $options = [])
	{
		if ($view == 'entry') {
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onContentAfterDisplay';
	}

	public function getContext()
	{
		// standalone product page
		if ($this->isEntryView()) {
			return 'com_content.category.productlist';
		}

		return false;
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author->name;
	}

	public function getContentPermalink()
	{
		$link = 'index.php?option=com_j2store&view=products&task=view&id=' . $this->_item->id;
		$link = $this->prepareLink($link);

		return $link;
	}	
}