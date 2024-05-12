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

abstract class KomentoExtension
{
	const APIVERSION = '1.3';

	/**
	 * The state of the component plugin
	 * @var boolean
	 */
	public $state = true;

	/**
	 * The extension name
	 * @var string
	 */
	public $component = null;

	public $tableName = null;

	/**
	 * The main article object
	 * @var mixed
	 */
	public $_item = null;

	/**
	 * Article properties mapping
	 *
	 * @var    array
	 */
	public $_map = [
		// not needed with custom getContentId()
		'id' => 'id',

		// not needed with custom getContentTitle()
		'title' => 'title',

		// not needed with custom getContentHits()
		'hits' => 'hits',

		// not needed with custom getAuthorId()
		'created_by' => 'created_by',

		// not needed with custom getCategoryId()
		'catid' => 'catid',

		// not needed with custom getContentPermalink()
		'permalink' => 'permalink'
	];

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 */
	abstract public function load($cid);

	/**
	 * Method to get content's ID based on category filter
	 *
	 * @access	public
	 */
	abstract public function getContentIds($categories = '');

	/**
	 * Method to get a list of categories
	 *
	 * @access	public
	 */
	abstract public function getCategories();

	/**
	 * Method to check if the current view is listing view
	 *
	 * @access	public
	 */
	abstract public function isListingView();

	/**
	 * Method to check if the current view is entry view
	 *
	 * @access	public
	 */
	abstract public function isEntryView();

	/**
	 * Method to append the comment to the article
	 *
	 * @access	public
	 */
	abstract public function onExecute(&$article, $html, $view, $options = []);

	public function __construct($component)
	{
		$this->component = $component;
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	/**
	 * Allow the plugin to do early initialization before anything else
	 *
	 * @since	4.0.3
	 * @access	public
	 */
	public function init()
	{
		return true;
	}

	public function componentTableExists()
	{
		if (is_null($this->tableName)) {
			return true;
		}

		$jConfig = JFactory::getConfig();
		$prefix = $jConfig->get('dbprefix');

		$db = KT::db();
		$query = "SHOW TABLES LIKE '%" . $prefix . $this->tableName . "%'";

		$db->setQuery($query);

		return (boolean) $db->loadResult();
	}

	public function addFile($file)
	{
		static $data = [];

		if (!$this->state) {
			return false;
		}

		$index = md5($file);

		if (!isset($data[$index])) {
			$exists = JFile::exists($file);
			$this->state = false;
			$data[$index] = false;

			if ($exists) {
				require_once($file);
				$data[$index] = true;
				$this->state = true;
			}
		}

		return $data[$index];
	}

	/**
	 * Method to get the name of the current API version number
	 *
	 * @access	public
	 */
	public function getAPIVersion()
	{
		return self::APIVERSION;
	}

	/**
	 * Method to get the translated name of current component
	 *
	 * @access	public
	 */
	public function getComponentName()
	{
		return JText::_('COM_KOMENTO_' . strtoupper($this->component));
	}

	/**
	 * Method to get the icon address of current component
	 *
	 * @access	public
	 */
	public function getComponentIcon()
	{
		$base = 'administrator/components/com_komento/assets/images/components/';
		$file = $this->component . '.png';
		$exists = JFile::exists(JPATH_ROOT . '/' . $base . $file);

		if (!$exists) {
			return JURI::root() . $base . 'error.png';
		}

		return JURI::root() . $base . $file;
	}

	/**
	 * Method to get the component template override path
	 *
	 * @access	public
	 */
	public function getComponentThemePath()
	{
		return JPATH_ROOT . '/components/' . $this->component . '/komento';
	}

	/**
	 * Method to get the component template override path uri
	 *
	 * @access	public
	 */
	public function getComponentThemeURI()
	{
		return JURI::root() . 'components/' . $this->component . '/komento';
	}

	/**
	 * Method to prepare a proper link
	 *
	 * @access	public
	 */
	public function prepareLink($link)
	{
		$link = JRoute::_($link);

		// remove relatiave path if exist
		$relpath = JURI::root(true);

		if ($relpath != '' && strpos($link, $relpath) === 0) {
			$link = substr($link, strlen($relpath));
		}

		// backend or frontend, remove administrator from link
		if (strpos($link, '/administrator/') === 0) {
			$link = substr($link, 14);
		}

		$link = rtrim(JURI::root(), '/') . '/' . ltrim($link, '/');

		return $link;
	}

	/**
	 * Method to get allowed trigger to run Komento
	 *
	 * @access	public
	 */
	public function getEventTrigger()
	{
		return true;
	}

	/**
	 * Method to get allowed context to run Komento
	 *
	 * @access	public
	 */
	public function getContext()
	{
		return true;
	}

	/**
	 * Method to get content's ID
	 *
	 * @access	public
	 */
	public function getContentId()
	{
		return $this->_item->{$this->_map['id']};
	}

	/**
	 * Method to get content's title
	 *
	 * @access	public
	 */
	public function getContentTitle()
	{
		return $this->_item->{$this->_map['title']};
	}

	/**
	 * Method to get content's state
	 *
	 * @access	public
	 */
	public function getContentState()
	{
		return $this->_item->{$this->_map['state']};
	}

	/**
	 * Method to get content's hits count
	 *
	 * @access	public
	 */
	public function getContentHits()
	{
		return $this->_item->{$this->_map['hits']};
	}

	/**
	 * Method to get content's permalink
	 *
	 * @access	public
	 */
	public function getContentPermalink()
	{
		return $this->_item->{$this->_map['permalink']};
	}

	/**
	 * Method to get article's category ID.
	 * If category is not applicable, return true
	 *
	 * @access	public
	 */
	public function getCategoryId()
	{
		return $this->_item->{$this->_map['catid']};
	}

	/**
	 * Method to get author's ID
	 *
	 * @access	public
	 */
	public function getAuthorId()
	{
		return $this->_item->{$this->_map['created_by']};
	}

	/**
	 * Method to get author's display name
	 *
	 * @access	public
	 */
	public function getAuthorName()
	{
		return JFactory::getUser($this->getAuthorId() )->name;
	}

	/**
	 * Method to get author's avatar
	 *
	 * @access	public
	 */
	public function getAuthorAvatar()
	{
		return '';
	}

	/**
	 * Method to get custom anchor link to work with comment section jump
	 *
	 * @access	public
	 */
	public function getCommentAnchorId()
	{
		return '';
	}

	/**
	 * Method to get extra integration settings
	 *
	 * @access	public
	 */
	public function getComponentSettings()
	{
		return [];
	}

	/**
	 * Prepare the data if necessary before the checking
	 *
	 * @access	public
	 */
	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		return true;
	}

	/**
	 * Prepare article if Komento is disabled
	 *
	 * @access	public
	 */
	public function onParameterDisabled($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		return false;
	}

	/**
	 * Should komento process parameter
	 *
	 * @access	public
	 */
	public function processParameter($context)
	{
		return true;
	}

	/**
	 * After the loading the content article with id
	 *
	 * @access	public
	 */
	public function onAfterLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		return true;
	}

	/**
	 * Roll back passed by reference
	 *
	 * @access	public
	 */
	public function onRollBack($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		return true;
	}

	/**
	 * When article of the component is deleted
	 *
	 * @access	public
	 */
	public function onArticleDeleted($article)
	{
		$cid = $article;

		if (is_object($article)) {
			$cid = $article->{$this->_map['id']};
		}

		$model = KT::model('comments');
		$result = $model->deleteArticleComments($this->component, $cid);

		return $result;
	}

	/**
	 * Failsafe function
	 *
	 * @access	public
	 */
	public function onLoadArticleError($cid)
	{
		static $componentInstances = [];
		static $cidInstances = [];

		if (empty($componentInstances[$this->component])) {
			require_once(__DIR__ . '/error.php');
			$componentInstances[$this->component] = new KomentoError($this->component);
		}

		if (empty($cidInstances[$this->component][$cid])) {
			$cidInstances[$this->component][$cid] = $componentInstances[$this->component]->load($cid);
		}

		return $cidInstances[$this->component][$cid];
	}

	/**
	 * Called before Komento's bar
	 *
	 * @access	public
	 */
	public function onBeforeKomentoBar($commentCount) {}

	/**
	 * Called after Komento's bar
	 *
	 * @access	public
	 */
	public function onAfterKomentoBar($commentCount) {}

	/**
	 * Called before Komento's box
	 *
	 * @access	public
	 */
	public function onBeforeKomentoBox($system, $comments) {}

	/**
	 * Called before Komento save comment
	 *
	 * @access	public
	 */
	public function onBeforeSaveComment($comment)
	{
		return true;
	}

	/**
	 * Called after Komento save comment
	 *
	 * @access	public
	 */
	public function onAfterSaveComment($comment) {}

	/**
	 * Called before Komento process comment
	 *
	 * @access	public
	 */
	public function onBeforeProcessComment($comment) {}

	/**
	 * Called after Komento process comment
	 *
	 * @access	public
	 */
	public function onAfterProcessComment($comment) {}

	/**
	 * Called before Komento stores a notification to the database
	 *
	 * @access	public
	 */
	public function onBeforeSendNotification($recipient)
	{
		return true;
	}

	/**
	 * Called before Komento delete comment
	 *
	 * @access	public
	 */
	public function onBeforeDeleteComment($comment)
	{
		return true;
	}

	/**
	 * Called after Komento delete comment
	 *
	 * @access	public
	 */
	public function onAfterDeleteComment($comment) {}

	/**
	 * Called before Komento publish comment
	 *
	 * @access	public
	 */
	public function onBeforePublishComment($comment)
	{
		return true;
	}

	/**
	 * Called after Komento publish comment
	 *
	 * @access	public
	 */
	public function onAfterPublishComment($comment) {}

	/**
	 * Called before Komento unpublish comment
	 *
	 * @access	public
	 */
	public function onBeforeUnpublishComment($comment)
	{
		return true;
	}

	/**
	 * Called after Komento unpublish comment
	 *
	 * @access	public
	 */
	public function onAfterUnpublishComment($comment) {}
}