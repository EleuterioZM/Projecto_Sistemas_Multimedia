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

class KomentoComjdownloads extends KomentoExtension
{
	public $_item;

	public $_map = array(
		'id' => 'id',
		'title' => 'title',
		'hits' => 'downloads',
		'created_by' => 'created_by',
		'catid' => 'catid',
		'permalink' => 'permalink',
		'state' => 'published'
	);

	public $itemId;

	public $tableName = 'jdownloads_files';

	public function __construct($component)
	{
		// Get version of JDownloads
		$file = JPATH_ADMINISTRATOR . '/components/com_jdownloads/jdownloads.xml';

		if (JFile::exists($file)) {
			$manifest = file_get_contents($file);

			$jdownloads = simplexml_load_string($manifest);
			$version = (float) $jdownloads->version;

			$beforeColumnChange = version_compare('3.9', round($version, 2));

			if ($beforeColumnChange) {
				$this->_map['id'] = 'file_id';
				$this->_map['title'] = 'file_title';
				$this->_map['catid'] = 'cat_id';
			}
		}
		
		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = array();

		if (!$this->componentTableExists()) {
			return $this->onLoadArticleError($cid);
		}

		if (empty($instances[$cid])) {
			$db = KT::db();

			$query = [];
			$query[] = 'SELECT * FROM ' . $db->nameQuote('#__jdownloads_files');
			$query[] = ' WHERE ' . $this->_map['id'] . ' = ' . $db->quote((int) $cid);

			$db->setQuery($query);
			$result = $db->loadObject();

			if (!$result) {
				return $this->onLoadArticleError($cid);
			}

			$categoryColumn = $this->_map['catid'];

			$result->itemid = $this->getItemId($result->$categoryColumn);

			$instances[$cid] = $result;

			// After load we get the itemid because itemid relies on category id
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$categoryColumn = $this->_map['catid'];

		$sql = KT::sql();

		$sql->select('#__jdownload_files')
			->order('id');

		if (!empty($categories)) {
			$sql->where('catid', $categories, 'in');
		}

		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$sql = KT::sql();
		$sql->select('#__jdownloads_categories')
			->column('id', 'id')
			->column('title', 'title')
			->column('parent_id')
			// ->where('published', 1)
			->order('ordering');

		$categories = $sql->loadObjectList();

		$result = array();

		$this->setLevel(0, 0, $categories, $result);

		return $result;
	}

	private function setLevel($pid, $level, $categories, &$result)
	{
		foreach ($categories as &$category) {
			if ((int) $category->parent_id === (int) $pid) {
				$category->level = $level;

				$category->treename = str_repeat('.&#160;&#160;&#160;', $level) . ($level > 0 ? '|_&#160;' : '') . $category->title;

				$result[] = $category;

				$this->setLevel($category->id, $level + 1, $categories, $result);
			}
		}
	}

	public function isListingView()
	{
		return JRequest::getString('view', '') === 'category';
	}

	public function isEntryView()
	{
		return JRequest::getString('view', '') === 'download';
	}

	public function onExecute(&$article, $html, $view, $options = array())
	{
		// $article->text .= $html;

		return $html;
	}

	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		$id = JRequest::getInt('id');

		$article->{$this->_map['id']} = $id;

		return true;
	}

	public function getEventTrigger()
	{
		return 'onContentAfterDisplay';
	}

	public function getContext()
	{
		return 'com_jdownloads.download';
	}

	public function getContentPermalink()
	{
		$link = 'index.php?option=' . $this->component . '';

		$pieces = array(
			'option=' . $this->component,
			'Itemid=' . $this->getItemId(),
			'view=download',
			'catid=' . $this->getCategoryId(),
			'id=' . $this->getContentId()
		);

		$link = $this->prepareLink('index.php?' . implode('&', $pieces));

		return $link;
	}

	public function getItemId($categoryId = null)
	{
		static $itemids = array();


		if(is_null($categoryId)) {
			$categoryId = $this->getCategoryId();
		}

		if (empty($itemids[$categoryId])) {
			$sql = KT::sql();

			$sql->select('#__menu')
				->column('id')
				->column('link')
				->where('link', 'index.php?option=com_jdownloads&view=category&catid%', 'LIKE')
				->where('published', 1)
				->where('client_id', 0);

			$itemid = 0;

			$result = $sql->loadObjectList();

			if (!empty($result)) {
				foreach ($result as $row) {
					$catid = substr(strrchr($row->link, '='), 1);

					if ($catid == $categoryId) {
						$itemid = $row->id;
						break;
					}
				}
			}

			// check for all categories menu item.
			if (empty($itemid)) {
				$sql->clear();
				$sql->select('#__menu')
					->column('id')
					->where('link', 'index.php?option=com_jdownloads&view=categories')
					->where('published', 1)
					->where('client_id', 0);

				$itemid = $sql->loadResult();
			}

			if (empty($itemid)) {
				$sql->clear();
				$sql->select('#__menu')
					->column('id')
					->where('link', 'index.php?option=com_jdownloads&view=category')
					->where('published', 1)
					->where('client_id', 0);

				$itemid = $sql->loadResult();
			}

			if (empty($itemid)) {
				$sql->clear();
				$sql->select('#__menu')
					->column('id')
					->where('link', 'index.php?option=com_jdownloads%', 'LIKE')
					->where('published', 1)
					->where('client_id', 0);

				$itemid = $sql->loadResult();
			}

			$itemids[$categoryId] = $itemid;
		}

		return $itemids[$categoryId];
	}
}
