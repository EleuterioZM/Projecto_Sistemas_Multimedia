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

require_once(__DIR__ . '/abstract.php');

class KomentoComjshopping extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id' => 'product_id',
		'title' => 'name_en-GB',
		'hits' => 'hits',
		'created_by' => 'created_by',
		'catid' => 'catid',
		'state' => 'product_publish'
		);

	private $_currentTrigger = '';

	public function __construct($component)
	{
		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = array();

		if (!isset($instances[$cid])) {
			$db	= KT::db();
			$query	= 'SELECT a.*, c.category_id AS catid'
					. ' FROM ' . $db->nameQuote('#__jshopping_products') . ' AS a'
					. ' LEFT JOIN ' . $db->nameQuote('#__jshopping_products_to_categories')  . ' AS c ON c.product_id = a.product_id'
					. ' WHERE a.product_id' . '=' . $db->quote($cid);
			$db->setQuery($query);

			if (!$this->_item = $db->loadObject()) {
				return $this->onLoadArticleError($cid);
			}

			// Since jshopping does not store the creator, we need to map it ourselves by finding the first super admin on the site
			$admins = KT::getSAUsersIds();
			$this->_item->created_by = $admins[0];

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$db = KT::db();
		$query = '';

		if (empty($categories)) {
			$query = 'SELECT `product_id` FROM ' . $db->nameQuote('#__shopping_products_to_categories') . ' ORDER BY `product_id`';
		} else {
			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `product_id` FROM ' . $db->nameQuote('#__shopping_products_to_categories') . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `product_id`';
		}

		$db->setQuery($query);

		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db	= KT::db();
		$query	= 'SELECT a.category_id as id, a.`name_en-GB` AS title, a.category_parent_id AS parent_id, a.`name_en-GB`, a.category_parent_id'
				. ' FROM `#__jshopping_categories` AS a'
				. ' WHERE a.category_publish = 1'
				. ' ORDER BY a.ordering';
		$db->setQuery($query);
		$categories	= $db->loadObjectList();

		$children = array();

		foreach ($categories as $row) {
			$pt = $row->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			$list[] = $row;
			$children[$pt] = $list;
		}

		$categories	= JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		return $categories;
	}

	public function isListingView()
	{
		return ($this->_currentTrigger == 'onBeforeDisplayProductListingView') ? true : false;
	}

	public function isEntryView()
	{
		$task = $this->input->get('task');
		$controller = $this->input->get('controller');

		return ($this->_currentTrigger == 'onBeforeDisplayProductView' && $task == 'view' && $controller == 'product') ? true : false;
	
	}

	public function onExecute(&$article, $html, $view, $options = array())
	{
		return $html;
	}

	public function getEventTrigger()
	{
		return array('onBeforeDisplayProductView');
	}

	public function getContext()
	{
		return array('jshopping_products');
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author->name;
	}

	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		$this->_currentTrigger = $eventTrigger;

		return true;
	}

	public function getContentPermalink()
	{
		$link = 'index.php?option=com_jshopping&controller=product&task=view&category_id=' . $this->_item->catid . '&product_id=' . $this->_item->product_id;

		$link = $this->prepareLink($link);

		return $link;
	}
}
