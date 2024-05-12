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

class KomentoError extends KomentoExtension
{
	public $_map = [
		'id' => 'id',
		'title' => 'title',
		'hits' => 'hits',
		'created_by' => 'created_by',
		'catid' => 'catid',
		'permalink' => 'permalink'
	];

	public function load($cid)
	{
		static $instances = [];

		if (empty($instances[$cid])) {
			$instance = new stdClass();

			$instance->id = $cid;
			$instance->title = JText::_('COM_KOMENTO_UNABLE_TO_LOAD_ARTICLE_DETAILS') . ' (' . $cid . ')';
			$instance->permalink = 'javascript:void(0);';
			$instance->hits = 0;
			$instance->created_by = 0;
			$instance->cat_id = 0;

			$instances[$cid] = $instance;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		return [];
	}

	public function getCategories()
	{
		return [];
	}

	public function isListingView()
	{
		return false;
	}

	public function isEntryView()
	{
		return false;
	}

	public function onExecute(&$article, $html, $view, $options = [])
	{
		return false;
	}

	public function getComponentThemePath()
	{
		return '';
	}

	public function getComponentName()
	{
		if (!empty($this->component)) {
			return $this->component;
		}

		return JText::_('COM_KOMENTO_NO_COMPONENT_NAME_ASSIGNED');
	}
}
