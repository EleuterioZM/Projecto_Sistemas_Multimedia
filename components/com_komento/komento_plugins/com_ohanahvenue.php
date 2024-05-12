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

class KomentoComOhanahVenue extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id' => 'ohanah_venue_id',
		'title' => 'name',
		'created_by' => 'created_by',
		'permalink' => 'permalink'
		);

	public function __construct($component)
	{
		parent::__construct($component);
	}

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 *
	 * @return	object	Instance of this class
	 */
	public function load($cid)
	{
		static $instances = null;

		if (is_null($instances)) {
			$instances = array();
		}

		$cid = $cid ?: $this->input->get('ohanah_venue_id', 0, 'int');

		if (!array_key_exists($cid, $instances)) {
			$db = KT::db();
			$query = 'SELECT * FROM ' . $db->nameQuote('#__ohanah_venues')
					. ' WHERE ' . $db->nameQuote('ohanah_venue_id') . '=' . $db->quote($cid);

			$db->setQuery($query);

			if (!$this->_item = $db->loadObject()) {
				return $this->onLoadArticleError($cid);
			}

			// Ohanah venue does not store hits
			$this->_item->hits = null;

			$link = 'index.php?option=com_ohanah&view=events&ohanah_venue_id=' . $this->_item->ohanah_venue_id;
			$this->_item->permalink = $this->prepareLink($link);

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$db = KT::db();
		$query = '';

		$query = 'SELECT `ohanah_venue_id` FROM ' . $db->nameQuote('#__ohanah_venues') . ' ORDER BY `ohanah_venue_id`';

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function isListingView()
	{
		return false;
	}

	public function isEntryView()
	{
		return ($this->input->get('view') == 'events' && !empty($this->input->get('ohanah_venue_id')));
	}

	public function onExecute(&$article, $html, $view, $options = array())
	{
		if ($view == 'entry') {
			$article->description .= $html;
		}
	}

	public function getEventTrigger()
	{
		$trigger = 'onContentBeforeDisplay';

		return $trigger;
	}

	public function getContext()
	{
		return 'com_ohanah.venue';
	}

	public function getCategories()
	{
		return false;
	}

	/**
	 * Method to get content's hits count
	 *
	 * @access	public
	 *
	 * @return	string	The hits count of the article
	 */
	public function getContentHits()
	{
		return false;
	}

	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		// We got to go through 3 checks:
		// 1. Check if POST parameter have id (this is when user go to an event page through event listings)
		// 2. Check if page parameter have id (this is when user assign an event on a menu item)
		// 3. Find back the id based on text parameter passed in (this was the old trigger before changing from onPrepareContent to onAfterDisplayContent for Joomla 1.5)

		$cid = $this->input->get('ohanah_venue_id', 0, 'int');

		if (!$cid) {
			$pageParameters = JFactory::getApplication()->getPageParameters();
			$cid = $pageParameters->get('id');

		}

		if (!$cid) {

			// Bad fallback due to limited data that is being passed in
			// Use the description text to backtrace and search for the article id
			$text	= $article->text;
			$text	= str_ireplace('<!--{emailcloak=off}-->', '', $text);
			$db		= KT::db();
			$query	= 'SELECT ohanah_venue_id FROM `#__ohanah_venues` WHERE description = ' . $db->quote($text);
			$db->setQuery($query);
			$cid = $db->loadResult();
		}

		// If we still can't get the id of the ohanah event, we'll try to load it based on the "slug"
		$slug = $this->input->get('slug', '', 'string');

		if (!$cid && $slug) {

			// Query the database to retrieve the id of the event
			$db = KT::db();
			$query = 'select `ohanah_venue_id` from `#__ohanah_venues` WHERE `slug`=' . $db->Quote($slug);
			$db->setQuery($query);

			$cid = $db->loadResult();
		}

		// If cid is still empty then we don't continue
		if (empty($cid)) {
			return false;
		}

		$article->id = $cid;

		return true;
	}
}
