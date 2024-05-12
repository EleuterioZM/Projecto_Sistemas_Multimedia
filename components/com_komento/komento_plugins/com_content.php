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

class KomentoComcontent extends KomentoExtension
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
			$db		= KT::db();
			$query	= 'SELECT a.`id`, a.`title`, a.`alias`, a.`catid`, a.`created_by`, a.`created_by_alias`, a.`hits`, a.`state`, a.`language`, ' 
					. ' c.`title` AS category_title, c.`alias` AS category_alias,'
					. ' u.`name` AS author,'
					. ' parent.`id` AS parent_id, parent.`alias` AS parent_alias'
					. ' FROM ' . $db->nameQuote('#__content') . ' AS a'
					. ' LEFT JOIN ' . $db->nameQuote('#__categories') . ' AS c ON c.`id` = a.`catid`'
					. ' LEFT JOIN ' . $db->nameQuote('#__users') . ' AS u ON u.`id` = a.`created_by`'
					. ' LEFT JOIN ' . $db->nameQuote('#__categories') . ' AS parent ON parent.`id` = c.`parent_id`'
					. ' WHERE a.`id` = ' . $db->quote((int) $cid);
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
		$query = '';

		if (empty($categories)) {
			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__content') . ' ORDER BY `id`';
		} else {
			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__content') . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= KT::db();
		$query	= 'SELECT a.`id`, a.`title`, a.`level`, a.`parent_id`'
				. ' FROM `#__categories` AS a'
				. ' WHERE a.`extension` = ' . $db->quote('com_content')
				. ' AND a.`parent_id` > 0'
				. ' ORDER BY a.`lft`';

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
		$views = ['featured', 'category', 'categories', 'archive', 'frontpage'];

		return in_array($this->input->get('view'), $views);
	}

	public function isEntryView()
	{
		return $this->input->get('view') == 'article';
	}

	public function onExecute(&$article, $html, $view, $options = [])
	{
		if ($view == 'listing') {
			$config = KT::config();

			if ($config->get('layout_frontpage_readmore_button') != 'joomla') {
				$article->readmore = false;
			} else {
				if ($config->get('layout_frontpage_readmore') == 2) {
					$article->readmore = true;
				}

				if ($config->get('layout_frontpage_readmore') == 0) {
					$article->readmore = false;
				}
			}
			return $html;
		}

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
		// Entry view's context is definitely com_content.article
		if ($this->isEntryView()) {
			return 'com_content.article';
		}

		// Due to a change in the latest Joomla (Joomla 2.5.14 and Joomla 3.1.5)
		// The context in listing pages is no longer com_content.article
		// Return array with all 3 context here to support prior Joomla version, as well as the latest Joomla version
		if ($this->isListingView()) {
			return ['com_content.article', 'com_content.category', 'com_content.featured'];
		}

		return false;
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author;
	}

	public function getContentPermalink()
	{
		$slug = $this->_item->alias ? ($this->_item->id.':'.$this->_item->alias) : $this->_item->id;
		$catslug = $this->_item->category_alias ? ($this->_item->catid.':'.$this->_item->category_alias) : $this->_item->catid;
		$langslug = $this->_item->language;
		$parent_slug = $this->_item->category_alias ? ($this->_item->parent_id.':'.$this->_item->parent_alias) : $this->_item->parent_id;

		$link = FCContentHelperRoute::getArticleRoute($slug, $catslug, $langslug);

		$link = $this->prepareLink($link);

		return $link;
	}

	/**
	 * Method to get extra integration settings
	 *
	 * @access	public
	 *
	 */
	public function getComponentSettings()
	{
		$settings = [];

		$obj = new stdClass; 
		$obj->name = 'layout_frontpage_readmore';
		$obj->type = 'settings.dropdown';
		$obj->values = [
			'0' => 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_READMORE_ALWAYS_HIDE',
			'1' => 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_READMORE_OBEY_READMORE_BREAK',
			'2' => 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_READMORE_ALWAYS_SHOW'
		];

		$settings[] = $obj;

		$obj = new stdClass; 
		$obj->name = 'pagebreak_load';
		$obj->type = 'settings.dropdown';
		$obj->values = [
			'all' => 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_ALL',
			'first' => 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_FIRST',
			'last' => 'COM_KOMENTO_SETTINGS_PAGEBREAK_LOAD_LAST'
		];

		$settings[] = $obj;

		$obj = new stdClass; 
		$obj->name = 'layout_frontpage_readmore_button';
		$obj->type = 'settings.dropdown';
		$obj->values = [
			'joomla' => 'COM_KOMENTO_SETTINGS_READMORE_JOOMLA',
			'komento' => 'COM_KOMENTO_SETTINGS_READMORE_KOMENTO'
		];

		$settings[] = $obj;

		return $settings;
	}

	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		if (!$this->isEntryView()) {
			return true;
		} 

		// Get Pagebreak Plugin params
		$plugin = JPluginHelper::getPlugin('content', 'pagebreak');
		$pluginParams = new JRegistry($plugin->params);

		// If the style is sliders or tab, 
		// We don't need to implement this.
		if ($pluginParams->get('style') != 'pages') {
			return true;
		}

		$config = KT::config();

		if ($config->get('pagebreak_load') === 'all' || $this->input->get('showall', 0, 'int') === 1) {
			return true;
		}

		$regex = '#<hr(.*)class="system-pagebreak"(.*)\/>#iU';

		$matches = [];
		$count = 0;

		preg_match_all($regex, $article->introtext, $matches, PREG_SET_ORDER);
		$count += count($matches);

		preg_match_all($regex, $article->fulltext, $matches, PREG_SET_ORDER);
		$count += count($matches);

		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);
		$count += count($matches);

		if ($count === 0) {
			return true;
		} 

		if ($config->get('pagebreak_load') === 'first' && $page === 0) {
			return true;
		}

		if ($config->get('pagebreak_load') === 'last' && $count == $page) {
			return true;
		}

		return false;
	}
}