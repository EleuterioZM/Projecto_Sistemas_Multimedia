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

class KomentoComjoomgallery extends KomentoExtension
{
	public $_item;

	// map the keys here
	public $_map = [
		'id' => 'id',
		'title' => 'imgtitle',
		'hits' => 'hits',
		'created_by' => 'imgauthor',
		'catid' => 'catid',
		'state' => 'published'
	];

	// constructor. add all required files here
	public function __construct($component)
	{
		parent::__construct($component);
	}

	// load all main properties here based on article id
	public function load($cid)
	{
		static $instances = array();

		if (!isset($instances[$cid])) {

			$db = KT::db();
			$query = 'SELECT `id`, `imgtitle`, `hits`, `imgauthor`, `catid`, `published` FROM ' . $db->nameQuote('#__joomgallery') . ' WHERE `id` = ' . $db->quote($cid);
			$db->setQuery($query);

			$result = $db->loadObject();

			// return false or call the onLoadArticleError event if there are no objects to load
			if (!$result) {
				return $this->onLoadArticleError($cid);
			}

			$instances[$cid] = $result;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	/**
	 * Method to get content's permalink
	 *
	 * @access	public
	 */
	public function getContentPermalink()
	{
		$permalink = '/index.php?view=detail&id=' . $this->_item->{$this->_map['id']} . '&option=com_joomgallery';
		$permalink = $this->prepareLink($permalink);

		return $permalink;
	}

	/**
	 * Method to get author's ID
	 *
	 * @access	public
	 */
	public function getAuthorId()
	{
		if (!$this->_map['created_by']) {
			return 659;
		}

		return $this->_item->{$this->_map['created_by']};
	}

	public function getContentIds($categories = '')
	{
		$db = KT::db();
		$query = '';

		if (empty($categories)) {
			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__joomgallery') . ' ORDER BY `id`';
		} else {
			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__joomgallery') . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db = KT::db();
		$query = 'SELECT `cid`, `name` as `title`, `level`, `parent_id`'
				. ' FROM `#__joomgallery_catg`'
				. ' WHERE `parent_id` > 0'
				. ' ORDER BY `lft`';

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		foreach ($categories as &$row) {
			$repeat = ($row->level - 1 >= 0) ? $row->level - 1 : 0;
			$row->treename = str_repeat('.&#160;&#160;&#160;', $repeat) . ($row->level - 1 > 0 ? '|_&#160;' : '') . $row->title;
		}

		return $categories;
	}

	// to determine if is listing view
	public function isListingView()
	{
		$views = ['gallery', 'category'];

		return in_array($this->input->get('view'), $views);
	}

	// to determine if is entry view
	public function isEntryView()
	{
		return $this->input->get('view') == 'detail';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		return $html;
	}

	public function getEventTrigger()
	{
		return 'onJoomAfterDisplayDetailImage';
	}
}
