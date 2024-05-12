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

class KomentoModelComments extends KomentoModel
{
	protected $element = 'comments';
	public $_total = null;
	public $_comments = null;

	public $pagination = null;

	// set views without depth
	// move this to hidden config?
	private $viewWithoutDepth = ['rss', 'dashboard', 'pending'];

	// use for row number
	private $hasRowId = null;

	public function __construct($config = [])
	{
		parent::__construct($config);

		$app = JFactory::getApplication();

		$limit = $app->getUserStateFromRequest('com_komento.comments.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->db = KT::db();
	}

	/**
	 * Returns the pagination object.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (is_null($this->pagination)) {
			$limitstart = (int) $this->getState('limitstart');
			$limit = (int) $this->getState('limit');
			// $total = (int) $this->getState('total');
			$total = (int) $this->_total;

			$this->pagination = KT::pagination($total, $limitstart, $limit);
		}

		return $this->pagination;
	}

	public function getPaginationx()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function getCount($component = 'all', $cid = 'all', $options = [])
	{
		// define default values
		$defaultOptions	= [
			'sort' => 'default',
			'limit'	=> 0,
			'limitstart' => 0,
			'search' => '',
			'sticked' => 'all',
			'published'	=> 1,
			'userid' => 'all',
			'parentid' => 'all',
			'threaded' => 0,
			'random' => 0
		];

		$options = KT::mergeOptions($defaultOptions, $options);

		$queryTotal	= $this->buildTotal($component, $cid, $options);
		$queryWhere = $this->buildWhere($component, $cid, $options);

		$query = $queryTotal . $queryWhere;

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}

	/**
	 * Retreive the new comments on last check time given
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getNewComments($component, $cid, $lastchecktime, $userId, $excludeIds = [])
	{
		$db = $this->db;
		$config = KT::config();

		$query = [];
		$query[] = 'SELECT * FROM `#__komento_comments`';
		$query[] = 'WHERE `component` = ' . $db->Quote($component);
		$query[] = 'AND `cid` = ' . $db->Quote($cid);
		$query[] = 'AND `published` = ' . $db->Quote('1');
		$query[] = 'AND `created` > ' . $db->Quote($lastchecktime);
		$query[] = 'AND `created_by` != ' . $db->Quote($userId);

		if (!empty($excludeIds)) {
			$query[] = 'AND `id` NOT IN(' . implode(',', $excludeIds) . ')';
		}

		$query[] = $this->buildOrder($component, $cid, [
			'threaded' => $config->get('enable_threaded'),
			'sort' => $config->get('default_sort')
		]);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if (empty($list)) {
			return [];
		}

		$comments = [];

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		foreach ($list as $row) {
			$comment = KT::comment($row);

			$obj = new stdClass();
			$obj->id = $comment->id;
			$obj->author = $comment->getAuthorName();
			$obj->avatar = $comment->getAuthor()->getAvatarHtml($obj->author, '', '', 'default');
			$obj->parent_id = (int) $comment->parent_id;
			$obj->isReply = $obj->parent_id !== 0;
			$obj->message = $obj->isReply ? JText::sprintf('COM_KT_LIVE_NOTIFICATION_NEW_REPLY_MESSAGE', $obj->author) : JText::sprintf('COM_KT_LIVE_NOTIFICATION_NEW_COMMENT_MESSAGE', $obj->author);

			$themes = KT::themes();
			$themes->set('comment', $comment);
			$themes->set('application', $application);

			$obj->html = $themes->output('site/comments/item');

			$comments[] = $obj;
		}

		return $comments;
	}


	public function getOverallRatings($component = 'all', $cid = 'all', $options = [], $debug = false)
	{
		$config	= KT::getConfig();
		$userId	= JFactory::getUser()->id;

		// define default values
		$defaultOptions	= [
			'sort' => 'default',
			'limit'	=> 0,
			'limitstart' => 0,
			'search' => '',
			'sticked' => 'all',
			'published'	=> 1,
			'userid' => 'all',
			'parentid' => 'all',
			'threaded' => 0,
			'random' => 0,
			'ratings' => true
		];

		// take the input values and clear unexisting keys
		$options = KT::mergeOptions($defaultOptions, $options);

		$query = 'SELECT ROUND(AVG(x.`value`),2) as `value`, count(1) as `total` FROM ';
		$query .= '(SELECT a.`ratings` as `value` FROM ' . $this->db->nameQuote('#__komento_comments') . ' as a ';
		$query .= $this->buildWhere($component, $cid, $options);
		$query .= ' AND a.`created` = ';
		$query .= '(SELECT max(b.`created`) FROM ' . $this->db->nameQuote('#__komento_comments') . ' as b';
		$query .= ' where a.' . $this->db->nameQuote('email') . ' = b.' . $this->db->nameQuote('email');

		if ($component !== 'all') {
			$query .= ' AND b.' . $this->db->nameQuote('component') . ' = ' . $this->db->quote($component);
		}
		
		if (is_array($cid) && !empty($cid)) {
			$cid = implode(', ', $cid);
			$query .= ' AND b.' . $this->db->nameQuote('cid') . ' in( ' . $cid . ' )';
		} else if ($cid !== 'all') {
			$query .= ' AND b.' . $this->db->nameQuote('cid') . ' = ' . $this->db->quote($cid);
		}

		$query .= ' AND b.' . $this->db->nameQuote('published') . ' = ' . $this->db->quote(1);
		$query .= ' AND b.' . $this->db->nameQuote('ratings') . ' != ' . $this->db->quote(0) . ')) as x';
		

		if ($debug) {
			echo str_replace('#_', 'jos', $query);
			exit;
		}

		$this->db->setQuery($query);

		$ratings = $this->db->loadObject();

		if (!$ratings) {
			return false;
		}

		return $ratings;
	}

	public function getComments($component = 'all', $cid = 'all', $options = [])
	{
		$config = KT::getConfig();
		$userId = JFactory::getUser()->id;

		// define default values
		$defaultOptions	= [
			'sort' => 'default',
			'limit' => 0,
			'limitstart' => 0,
			'search' => '',
			'sticked' => 'all',
			'published' => 1,
			'userid' => 'all',
			'parentid' => 'all',
			'threaded' => 0,
			'random' => 0,
			'loadLib' => 0,
			'showRepliesCount' => 1,
			'itemListing' => false
		];

		// take the input values and clear unexisting keys
		$options = KT::mergeOptions($defaultOptions, $options);

		// the actuall data query
		$query = $this->buildQuery($component, $cid, $options);

		// dump($query);

		if ($this->hasRowId) {
			$initialId = 0;

			if (isset($options['limitstart']) && $options['limitstart']) {
				$initialId = $options['limitstart'];
			}

			$rowIdAssignmentSQL = "SELECT $initialId INTO @ktrowid;";

			// lets execute this sql now so that the the following query can accesss this 'ktrowid';
			$this->db->setQuery($rowIdAssignmentSQL);
			$this->db->query();
		}

		$this->db->setQuery($query);

		$results = $this->db->loadObjectList();
		$this->_total = $this->getTotal($component, $cid, $options);

		if ($this->db->getErrorNum() > 0) {
			throw FH::exception($this->db->getErrorMsg() . $this->db->stderr(), $this->db->getErrorNum());
		}

		if (! $results) {
			return [];
		}

		// build random
		if ($options['random']) {
			$results = $this->buildRandom($results, $options);
		}

		return $results;
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal($component = 'all', $cid = 'all', $options = [])
	{
		// Lets load the content if it doesn't already exist
		if (is_null($this->_total)) {
			// define default values
			$defaultOptions	= [
				'sort' => 'default',
				'limit' => 0,
				'limitstart' => 0,
				'search' => '',
				'sticked' => 'all',
				'published' => 1,
				'userid' => 'all',
				'parentid' => 'all',
				'threaded' => 0,
				'random' => 0,
				'isCount' => 1
			];

			// take the input values and clear unexisting keys
			$options = KT::mergeOptions($defaultOptions, $options);

			// we don't want any limit on this total
			unset($options['limit']);

			$query = $this->buildQuery($component, $cid, $options);
			$this->db->setQuery($query);

			$total = $this->db->loadResult();

			$this->_total = $total;
		}

		return $this->_total;
	}

	private function buildQuery($component = 'all', $cid = 'all', $options = [])
	{
		$isCount = isset($options['isCount']) && $options['isCount'] ? true : false;

		$querySelect = $this->buildSelect($component, $cid, $options);
		$queryWhere = $this->buildWhere($component, $cid, $options);

		$queryOrder = '';
		$queryLimit = '';
		if (!$isCount) {
			$queryOrder = $this->buildOrder($component, $cid, $options);
			$queryLimit = $this->buildLimit($component, $cid, $options);
		}

		$query	= $querySelect . $queryWhere . $queryOrder . $queryLimit;

		return $query;
	}

	private function buildTotal($component = 'all', $cid = 'all', $options = [])
	{
		$queryTotal = 'SELECT COUNT(1) FROM ' . $this->db->nameQuote('#__komento_comments');

		return $queryTotal;
	}

	private function buildSelect($component = 'all', $cid = 'all', $options = [])
	{
		$isCount = isset($options['isCount']) && $options['isCount'] ? true : false;
		$showRepliesCount = isset($options['showRepliesCount']) && $options['showRepliesCount'] ? true : false;

		$my = KT::user();

		$querySelect = '';

		if ($isCount) {
			$querySelect = 'SELECT count(1) FROM ' . $this->db->nameQuote('#__komento_comments');
		} else {

			$this->hasRowId = true;
			$querySelect = 'SELECT a.*, (@ktrowid:=@ktrowid+1) as `rownumber`';

			if (!$my->guest) {

				if ($this->config->get('enable_likes')) {
					// likes
					$querySelect .= ', (select count(1) from ' . $this->db->nameQuote('#__komento_actions') . ' as act1 where act1.`comment_id` = a.`id` and act1.`type` = ' . $this->db->Quote('likes') . ' and act1.`action_by` = ' . $this->db->Quote($my->id) . ') as `liked`,';

					// dislikes
					$querySelect .= ' (select count(1) from ' . $this->db->nameQuote('#__komento_actions') . ' as act2 where act2.`comment_id` = a.`id` and act2.`type` = ' . $this->db->Quote('dislikes') . ' and act2.`action_by` = ' . $this->db->Quote($my->id) . ') as `disliked`,';
				} else {
					$querySelect .= ', 0 as `liked`, 0 as `disliked`, ';
				}

				if ($this->config->get('enable_report')) {
					$querySelect .= ' (select count(1) from ' . $this->db->nameQuote('#__komento_actions') . ' as act3 where act3.`comment_id` = a.`id` and act3.`type` = ' . $this->db->Quote('report') . ' and act3.`action_by` = ' . $this->db->Quote($my->id) . ') as `reported`';
				} else {
					$querySelect .= ' 0 as `reported`';
				}

			} else {
				$querySelect .= ', 0 as `liked`, 0 as `disliked`, 0 as `reported`';
			}

			if ($showRepliesCount) {
				$childQuery = "(select count(1) from `#__komento_comments` as cc where cc.`component` = a.`component` and cc.`cid` = a.`cid` and cc.`published` = 1 and cc.`lft` > a.`lft` and cc.`rgt` < a.`rgt`)";
				$querySelect .= ', ' . $childQuery . ' as `childs`';
			} else {
				$querySelect .= ', 0 as `childs`';
			}

			if (isset($options['sort']) && $options['sort'] == 'popular') {
				$querySelect .= ', (select count(1) from ' . $this->db->nameQuote('#__komento_actions') . ' as ax where ax.' . $this->db->qn('type') . '=' . $this->db->Quote('likes') . ' AND ' . $this->db->qn('comment_id') . ' = a.' . $this->db->qn('id') . ') as `likes`';
			}

			$querySelect .= ' FROM ' . $this->db->nameQuote('#__komento_comments') . ' as a';

		}

		return $querySelect;
	}

	private function buildWhere($component = 'all', $cid = 'all', $options = [])
	{
		$queryWhere = [];

		if ($component !== 'all') {
			$queryWhere[] = $this->db->nameQuote('component') . ' = ' . $this->db->quote($component);
		}

		if ($cid !== 'all' && !empty($cid)) {
			if (is_array($cid)) {
				$cid = implode(',', $cid);
				$queryWhere[] = $this->db->nameQuote('cid') . ' IN (' . $cid . ')';
			} else {
				$queryWhere[] = $this->db->nameQuote('cid') . ' = ' . $this->db->quote($cid);
			}
		}

		if ($options['published'] !== 'all') {
			$queryWhere[] = $this->db->nameQuote('published') . ' = ' . $this->db->quote($options['published']);
		}
		
		if ($options['sticked'] !== 'all') {
			$queryWhere[] = $this->db->nameQuote('sticked') . ' = ' . $this->db->quote($options['sticked']);
		}

		if ($options['userid'] !== 'all') {

			if (is_array($options['userid'])) {
				$userId = implode(',', $options['userid']);
				$queryWhere[] = $this->db->nameQuote('created_by') . ' IN (' . $userId . ')';
			} else {
				$queryWhere[] = $this->db->nameQuote('created_by') . ' = ' . $this->db->quote($options['userid']);
			}
		}

		if ($options['parentid'] !== 'all') {
			$queryWhere[] = $this->db->nameQuote('parent_id') . ' = ' . $this->db->quote($options['parentid']);
		}

		if ($options['search'] !== '') {
			$queryWhere[] = $this->db->nameQuote('comment') . ' LIKE ' . $this->db->quote('%' . $options['search'] . '%');
		}

		if (isset($options['ratings']) && $options['ratings']) {
			$queryWhere[]	= $this->db->nameQuote('ratings') . ' !=' . $this->db->Quote('');
		}

		if (count($queryWhere) > 0) {
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		} else {
			$queryWhere = '';
		}

		return $queryWhere;
	}

	private function buildOrder($component = 'all', $cid = 'all', $options = [])
	{
		// $queryOrder = ' GROUP BY ' . $this->db->nameQuote('a.id');
		$queryOrder = '';

		if ($options['threaded']) {
			switch (strtolower($options['sort'])) {
				case 'latest' :
					$queryOrder .= ' ORDER BY ' . $this->db->nameQuote('rgt') . ' DESC';
					break;
				case 'popular': 
					$queryOrder .= ' ORDER BY ' . $this->db->qn('likes') . ' DESC';
					break;
				case 'oldest' :
				default :
					$queryOrder .= ' ORDER BY ' . $this->db->nameQuote('lft') . ' ASC';
			}
		} else {
			switch (strtolower($options['sort'])) {
				case 'latest' :
					$queryOrder .= ' ORDER BY ' . $this->db->nameQuote('created') . ' DESC';
					break;
				case 'popular': 
					$queryOrder .= ' ORDER BY ' . $this->db->qn('likes') . ' DESC';
					break;
				case 'oldest' :
				default :
					$queryOrder .= ' ORDER BY ' . $this->db->nameQuote('created') . ' ASC';
					break;
			}
		}

		return $queryOrder;
	}

	private function buildLimit($component = 'all', $cid = 'all', $options = [])
	{
		$config = KT::getConfig();
		$queryLimit = '';

		$limit = (isset($options['limit']) && $options['limit']) ? $options['limit'] : 0;

		// if random is on, then don't parse limit here
		if ($options['random'] == 1) {
			return $queryLimit;
		}

		if ($limit) {

			// if this is listing view, don't apply the limitstart
			if ($options['itemListing']) {
				return ' LIMIT ' . $limit;
			}
			
			$this->setState('limit', $limit);

			$limitstart = $this->app->get('limitstart', $this->getState('limitstart'), 'int');

			if (isset($options['limitstart']) && $options['limitstart'] !== false) {
				$limitstart = $options['limitstart'];
			}

			$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		return $queryLimit;
	}

	private function buildRandom($comments, $options = [])
	{
		$limit = 0;

		if ($options['limit'] > 0) {
			$limit = $options['limit'];
		} else {
			$jLimit = FH::jconfig()->get('list_limit');
			$limit = $this->input->get('limit', 0, 'int') ?: $config->get('max_comments_per_page', $jLimit);
		}

		if (count($comments) <= 1) {
			return $comments;
		}

		$limit = $limit > count($comments) ? count($comments) : $limit;

		$indexes = array_rand($comments, $limit);

		$tmp = [];

		if (is_array($indexes)) {
			foreach ($indexes as $index) {
				$tmp[] = $comments[$index];
			}
		} else {
			$tmp[] = $comments[$indexes];
		}

		return $tmp;
	}

	/**
	 * Retrieves comments for the back-end
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getItems($options = [])
	{
		$mainframe = JFactory::getApplication();
		$view = $this->input->get('view', '', 'string');

		// define default values
		$defaultOptions	= [
			'no_tree'	=> 0,
			'component' => 'all',
			'published'	=> 'all',
			'userid'	=> '',
			'parent_id'	=> false,
			'no_search' => 0,
			'no_child'	=> 0
		];

		// take the input values and clear unexisting keys
		$options = KT::mergeOptions($defaultOptions, $options);

		$querySelect = '';
		$querySelectCount = '';
		$queryWhere = [];
		$queryOrder	= '';
		$queryLimit = '';
		$queryTotal = '';

		$filter_publish = $mainframe->getUserStateFromRequest('com_komento.comments.filter_publish', 'filter_publish',  $options['published'], 'string');
		$filter_component = $mainframe->getUserStateFromRequest('com_komento.comments.filter_component', 'filter_component', $options['component'], 'string');
		$filter_order = $mainframe->getUserStateFromRequest('com_komento.comments.filter_order', 'filter_order', 'created', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_komento.comments.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word');
		$search = $mainframe->getUserStateFromRequest('com_komento.comments.search', 'search', '', 'string');
		$limit = $mainframe->getUserStateFromRequest('com_komento.comments.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('com_komento.comments.limitstart', 'limitstart', 0, 'int');

		$db = JFactory::getDBO();

		$search = trim(FCJString::strtolower($search));
		$search = $db->escape($search);

		// clear search if nosearch = 1
		// for view parent purposes during search
		if ($options['no_search']) {
			$search = '';
		}

		$querySelect = 'SELECT * FROM ' . $this->db->nameQuote('#__komento_comments');

		$querySelectCount = 'SELECT COUNT(1) FROM ' . $this->db->nameQuote('#__komento_comments');

		// filter by component
		if ($filter_component !== 'all') {
			$queryWhere[] = $this->db->nameQuote('component') . ' = ' . $this->db->quote($filter_component);
		}

		// Filter by publishing state
		$publishState = FH::normalize($options, 'published', $filter_publish);

		if ($publishState !== 'all') {
			$state = KOMENTO_COMMENT_PUBLISHED;

			if ($publishState === 'unpublished') {
				$state = KOMENTO_COMMENT_UNPUBLISHED;
			}

			if ($publishState === 'pending' || $publishState === 2) {
				$state = KOMENTO_COMMENT_MODERATE;
			}

			if ($publishState === KT_COMMENT_SPAM) {
				$state = KT_COMMENT_SPAM;
			}

			$queryWhere[] = $this->db->nameQuote('published') . ' = ' . $this->db->quote($state);
		}

		// Exclude spams
		if ($publishState === 'all') {
			$queryWhere[] = $this->db->nameQuote('published') . ' != ' . $this->db->quote(KOMENTO_COMMENT_SPAM);
		}

		if ($search) {
			if (stripos($search, 'id:') === 0) {
				$queryWhere[] = 'created_by = ' . (int) substr($search, 3);
			} else {
				$queryWhere[] = 'LOWER(' . $this->db->nameQuote('comment') . ') LIKE \'%' . $search . '%\' ';
			}
		} else {
			if ($options['no_tree'] == 0 && $options['parent_id']) {
				$queryWhere[] = $this->db->nameQuote('parent_id') . ' = ' . $this->db->quote($options['parent_id']);
			}
		}

		if (count($queryWhere) > 0) {
			$queryWhere  = ' WHERE ' . implode(' AND ', $queryWhere);
		} else {
			$queryWhere = '';
		}

		$queryOrder = ' ORDER BY ' . $filter_order . ' ';

		if (strtolower($filter_order_Dir) == 'desc') {
			$queryOrder .= 'DESC';
		} else {
			$queryOrder .= 'ASC';
		}

		if ($options['parent_id'] == 0 && $limit != 0) {
			$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$queryTotal = $querySelectCount . $queryWhere;

		// set pagination
		$this->db->setQuery($queryTotal);
		$this->_total = $this->db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->_total, $limitstart, $limit);

		// actual query
		$query = $querySelect . $queryWhere . $queryOrder . $queryLimit;

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		if ($this->db->getErrorNum() > 0) {
			throw FH::exception($this->db->getErrorMsg() . $this->db->stderr(), $this->db->getErrorNum());
		}

		if (!empty($result) && $options['no_child'] == 0) {
			$ids = [];
			foreach ($result as $row) {
				$ids[] = $row->id;
			}

			$childCount = $this->getChildCount($ids);

			foreach ($result as &$row) {
				$row->childs = isset($childCount[$row->id]) ? $childCount[$row->id] : 0;
			}
		}

		return $result;
	}

	public function publish(&$comments, $publish = 1)
	{
		if ($comments == null) {
			return false;
		}

		if (!is_array($comments)) {
			$comments = array($comments);
		}

		$affectChild = $this->input->get('affectchild', 0, 'int');

		if (count($comments) > 0) {
			$now = FH::date()->toSql();

			$publishDateColumn = '';

			if ($publish == 0) {
				$publishDateColumn = 'publish_down';
			} else {
				$publishDateColumn = 'publish_up';
			}

			$nodes = $comments;

			foreach ($nodes as $comment) {
				$related = [];

				if ($publish == 1) {
					$related = array_merge($related, self::getParents($comment));
				}

				if ($publish == 0 || ($publish == 1 && $affectChild)) {
					$related = array_merge($related, self::getChilds($comment));
				}

				if (count($related) > 0) {
					$comments = array_merge($comments, $related);
				}
			}

			$comments = array_unique($comments);
			$allComments = implode(',' , $comments);

			foreach ($comments as $comment) {
				if (!KT::getComment($comment)->publish($publish)) {
					return false;
				}
			}

			return true;
		}
		return false;
	}

	public function unpublish($comments = [], $publish = 0)
	{
		return self::publish($comments, $publish);
	}

	public function remove($comments = [])
	{
		if ($comments == null) {
			return false;
		}

		if (!is_array($comments)) {
			$comments = array($comments);
		}

		$affectChild = $this->input->get('affectchild', 0, 'int');

		if (count($comments) > 0) {
			$node = $comments;

			foreach ($node as $comment) {
				if ($affectChild) {

					$childs = self::getChilds($comment);
					if (count($childs) > 0) {
						$comments = array_merge($comments, $childs);
					}
				} else {
					self::moveChildsUp($comment);
				}
			}

			$comments = array_unique($comments);

			foreach ($comments as $comment) {
				$obj = KT::getComment($comment);
				$obj->delete();
			}

			return true;
		}
		return false;
	}

	public function stick($comments = [], $stick = 1)
	{
		if (!is_array($comments)) {
			$comments = array($comments);
		}

		if (count($comments) > 0) {
			$allComments = implode(',', $comments);

			$query  = 'UPDATE ' . $this->db->namequote('#__komento_comments');
			$query .= ' SET ' . $this->db->namequote('sticked') . ' = ' . $this->db->quote($stick);
			$query .= ' WHERE ' . $this->db->namequote('id') . ' IN (' . $allComments . ')';

			$this->db->setQuery($query);

			if (!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			foreach ($comments as $comment) {
				if ($stick) {
					KT::activity()->process('stick', $comment);
				} else {
					KT::activity()->process('unstick', $comment);
				}
			}

			return true;
		}
		return false;
	}

	public function unstick($comments = [])
	{
		return self::stick($comments, 0);
	}

	public function flag($comments, $flag)
	{
		$affectChild = $this->input->get('affectchild', 0, 'int');

		if (count($comments) > 0) {
			$user = JFactory::getUser()->id;

			if ($affectChild) {
				$node = $comments;

				foreach ($node as $comment) {
					$childs = self::getChilds($comment);
					if (count($childs) > 0) {
						$comments = array_merge($comments, $childs);
					}
				}
			}

			$comments = array_unique($comments);
			$allComments = implode(',', $comments);

			$query  = 'UPDATE ' . $this->db->namequote('#__komento_comments');
			$query .= ' SET ' . $this->db->namequote('flag') . ' = ' . $this->db->quote($flag);
			$query .= ', ' . $this->db->namequote('flag_by') . ' = ' .$this->db->quote($user);
			$query .= ' WHERE ' . $this->db->namequote('id') . ' IN (' . $allComments . ')';

			$this->db->setQuery($query);

			if (!$this->db->query()) {
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}

	/**
	 * Retrieve the total number of comments that are reported
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalReported()
	{
		$db = KT::db();
		$sql = KT::sql();

		$sql->select('#__komento_actions')
			->column('1', 'total', 'count', true)
			->where('type', KOMENTO_ACTIONS_TYPE_REPORT);

		$db->setQuery($sql);
		$total = (int) $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve the total number of comments that are marked as spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalSpams()
	{
		$db = KT::db();
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('1', 'total', 'count', true)
			->where('published', KOMENTO_COMMENT_SPAM);
		$db->setQuery($sql);

		$total = (int) $db->loadResult();
		return $total;
	}

	/**
	 * Retrieve the total number of comments that are pending
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTotalPending()
	{
		$db = KT::db();
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('1', 'total', 'count', true)
			->where('published', KOMENTO_COMMENT_MODERATE);

		$db->setQuery($sql);
		$total = (int) $db->loadResult();

		return $total;
	}

	// todo: should support options/component/cid filtering as well
	public function getTotalComment($userId = 0)
	{
		$config	= KT::getConfig();
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('1', 'total', 'count', true)
			->where('published', '1');

		if (!empty($userId)) {
			$sql->where('created_by', $userId);
		}

		$result	= $sql->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function getTotalReplies($userId = 0)
	{
		$config	= KT::getConfig();

		$where  = [];

		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote('#__komento_comments');

		$where[] = '`parent_id` <> 0';

		if (! empty($userId))
			$where[]  = '`created_by` = ' . $this->db->Quote($userId);

		$extra = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query = $query . $extra;

		$this->db->setQuery($query);

		$result	= $this->db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function getUniqueComponents()
	{
		$query = 'SELECT DISTINCT ' . $this->db->namequote('component') . ' FROM ' . $this->db->namequote('#__komento_comments') . ' ORDER BY ' . $this->db->namequote('component');
		$this->db->setQuery($query);
		$components = $this->db->loadResultArray();

		return $components;
	}

	public function getLatestComment($component, $cid, $parentId = 0)
	{
		$query  = 'SELECT `id`, `lft`, `rgt` FROM `#__komento_comments`';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `parent_id` = ' . $this->db->Quote($parentId);
		$query .= ' ORDER BY `lft` DESC LIMIT 1';

		$this->db->setQuery($query);
		$result	= $this->db->loadObject();

		return $result;
	}

	public function getCommentDepth($id)
	{
		$comment = KT::getComment($id);
		$component = $comment->component;
		$cid = $comment->cid;

		$query  = 'SELECT COUNT(`parent`.`id`)-1 AS `depth`';
		$query .= ' FROM `#__komento_comments` AS `node`';
		$query .= ' INNER JOIN `#__komento_comments` AS `parent` on parent.component = node.component and node.cid = parent.cid';
		$query .= ' WHERE `node`.`component` = ' . $this->db->Quote($component);
		$query .= ' AND `node`.`cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `node`.`id` = ' . $this->db->Quote($id);
		$query .= ' AND `node`.`lft` BETWEEN `parent`.`lft` AND `parent`.`rgt`';
		$query .= ' GROUP BY `node`.`id`';

		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		return $result->depth;
	}

	public function updateCommentSibling($component, $cid, $nodeValue)
	{
		$query  = 'UPDATE `#__komento_comments` SET `rgt` = `rgt` + 2';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `rgt` > ' . $this->db->Quote($nodeValue);
		$this->db->setQuery($query);
		$this->db->query();

		$query  = 'UPDATE `#__komento_comments` SET `lft` = `lft` + 2';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `lft` > ' . $this->db->Quote($nodeValue);
		$this->db->setQuery($query);
		$this->db->query();
	}

	public function updateCommentLftRgt(&$commentObj)
	{
		$commentsModel = KT::model('comments');

		$latestComment = $commentsModel->getLatestComment($commentObj->component, $commentObj->cid, $commentObj->parent_id);
		if ($commentObj->parent_id != 0) {
			$parentComment = KT::getTable('comments');
			$parentComment->load($commentObj->parent_id);

			//adding new child comment
			$lft = $parentComment->lft + 1;
			$rgt = $parentComment->lft + 2;
			$nodeVal = $parentComment->lft;

			if (!empty($latestComment)) {
				$lft = $latestComment->rgt + 1;
				$rgt = $latestComment->rgt + 2;
				$nodeVal = $latestComment->rgt;
			}

			$commentsModel->updateCommentSibling($commentObj->component, $commentObj->cid, $nodeVal);

			$commentObj->lft = $lft;
			$commentObj->rgt = $rgt;

		} else {

			//adding new comment
			$lft = 1;
			$rgt = 2;

			if (! empty($latestComment)) {
				$lft = $latestComment->rgt + 1;
				$rgt = $latestComment->rgt + 2;
				$nodeVal = $latestComment->rgt;

				$commentsModel->updateCommentSibling($commentObj->component, $commentObj->cid, $nodeVal);
			}

			$commentObj->lft = $lft;
			$commentObj->rgt = $rgt;
		}
	}

	public function getChilds($id, $cid = 0)
	{
		$commentTable = KT::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid = $cid > 0 ? $cid : $commentTable->cid;
		$lft = $commentTable->lft;
		$rgt = $commentTable->rgt;

		$query = 'SELECT ' . $this->db->namequote('id') . ' FROM ' . $this->db->namequote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->namequote('lft') . ' BETWEEN ' . $this->db->quote($lft) . ' AND ' . $this->db->quote($rgt);
		$query .= ' AND ' . $this->db->qn('id') . '!=' . $this->db->Quote($id);

		$this->db->setQuery($query);

		return $this->db->loadResultArray();
	}

	public function getRootParents($component, $cid)
	{
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('id')
			->where('component', $component)
			->where('cid', $cid)
			->where('parent_id', 0)
			->order('id');

		$result = $sql->loadResultArray();

		return $result;
	}

	public function fixItemStructure($id, &$boundary, $depth)
	{
		$item = KT::getTable('comments');
		$item->load($id);

		$item->lft = $boundary++;
		$item->rgt = $boundary++;

		$item->depth = $depth;

		$item->store();
	}

	public function fixChildStructure($id)
	{
		$parent = KT::getTable('comments');
		$parent->load($id);

		$boundary = $parent->lft + 1;
		$depth = $parent->depth + 1;

		$children = $this->getChildren($id);

		if (!empty($children)) {
			$total = count($children);

			$this->pushBoundaries($parent, $total);

			// Fix all the direct children first
			foreach ($children as $child) {
				$this->fixItemStructure($child, $boundary, $depth);
			}

			foreach ($children as $child) {
				$this->fixChildStructure($child);
			}
		}
	}

	public function getChildren($id)
	{
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('id')
			->where('parent_id', $id)
			->order('created');

		$result = $sql->loadResultArray();

		return $result;
	}


	public function pushBoundaries($item, $count)
	{
		$diff = $count * 2;

		$sql = KT::sql();

		$query = "UPDATE `#__komento_comments` SET `lft` = `lft` + $diff WHERE `component` = '$item->component' AND `cid` = '$item->cid' AND `lft` > $item->lft";

		$sql->raw($query);
		$sql->query();

		$query = "UPDATE `#__komento_comments` SET `rgt` = `rgt` + $diff WHERE `component` = '$item->component' AND `cid` = '$item->cid' AND `rgt` > $item->lft";

		$sql->raw($query);
		$sql->query();

	}


	public function getParents($id, $rootOnly = false)
	{
		$commentTable = KT::table('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid = $commentTable->cid;
		$lft = $commentTable->lft;

		$query = 'SELECT ' . $this->db->namequote('id') . ' FROM ' . $this->db->namequote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->quote($lft) . ' BETWEEN ' . $this->db->namequote('lft') . ' AND ' . $this->db->namequote('rgt');
		if ($rootOnly) {
			$query .= ' AND ' . $this->db->namequote('parent_id') . ' = 0';
		}

		$this->db->setQuery($query);

		if ($rootOnly) {
			return $this->db->loadResult();
		}

		return $this->db->loadResultArray();
	}

	public function getTotalChilds($id)
	{
		// CANNOT RELY ON JUST RGT-LFT
		$commentTable = KT::getTable('comments');
		$commentTable->load($id);

		$component = $commentTable->component;
		$cid = $commentTable->cid;
		$lft = $commentTable->lft;
		$rgt = $commentTable->rgt;

		$query = 'SELECT COUNT(1) FROM ' . $this->db->namequote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->namequote('lft') . ' BETWEEN ' . $this->db->quote($lft) . ' AND ' . $this->db->quote($rgt);
		$query .= ' AND ' . $this->db->namequote('lft') . ' != ' .$this->db->quote($lft);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	public function moveChildsUp($id)
	{
		$commentTable = KT::getTable('comments');
		$commentTable->load($id);

		$query = 'UPDATE ' . $this->db->namequote('#__komento_comments');
		$query .= ' SET ' . $this->db->namequote('parent_id') . ' = ' . $this->db->quote($commentTable->parent_id);
		$query .= ' WHERE ' . $this->db->namequote('parent_id') . ' = ' . $this->db->quote($commentTable->id);

		$this->db->setQuery($query);

		if (!$this->db->query()) {
			$this->setError($this->db->getErrorMsg());
			return false;
		}

		return true;
	}

	public function updateChildsArticle($comment, $previousCid)
	{
		$childs = $this->getChilds($comment->id, $previousCid);

		if (!$childs) {
			return true;
		}

		$childIds = array();
		
		if ($childs) {
			foreach ($childs as $childId) {
				$childIds[] = $childId;
			}
		}

		$query = 'UPDATE ' . $this->db->namequote('#__komento_comments');
		$query .= ' SET ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($comment->cid);
		$query .= ' WHERE ' . $this->db->namequote('id') . ' IN (' . implode(',', $childIds) . ')';

		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}

	public function deleteChilds($id)
	{
		// Get the replies
		$query = 'SELECT `id` FROM ' . $this->db->namequote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->nameQuote('parent_id') . ' = ' . $this->db->quote($id);

		$this->db->setQuery($query);
		$replies = $this->db->loadResultArray();

		if (!$replies) {
			return;
		}

		foreach ($replies as $id) {

			// Load the comment lib
			$reply = KT::comment($id);

			// Delete the reply
			$reply->delete();
		}

		return true;
	}

	public function isSticked($id)
	{
		$commentTable = KT::getTable('comments');
		$commentTable->load($id);
		return $commentTable->sticked;
	}

	public function getConversationBarAuthors($component, $cid)
	{
		$config = KT::getConfig();

		$limit = ' LIMIT ' . $config->get('conversation_bar_max_authors', 10);
		$order = ' ORDER BY ' . $this->db->namequote('created') . ' DESC';

		$main  = 'SELECT `name`, `created_by`, `created`, `email` FROM ' . $this->db->namequote('#__komento_comments');
		$main .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$main .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$main .= ' AND ' . $this->db->namequote('published') . ' = ' . $this->db->quote('1');

		$query  = $main . ' AND ' . $this->db->namequote('created_by') . ' <> ' . $this->db->quote('0') . ' AND ' . $this->db->namequote('created_by') . ' <> ' . $this->db->quote('');
		$query .= ' GROUP BY ' . $this->db->namequote('created_by') . $order . $limit;

		if ($config->get('conversation_bar_include_guest')) {
			$temp  = $main . ' AND ' . $this->db->namequote('created_by') . ' = ' . $this->db->quote('0');
			$temp .= ' GROUP BY ' . $this->db->namequote('name') . $order . $limit;

			$query = '(' . $query . ') UNION (' . $temp . ')';
		}

		$query = 'SELECT `name`, `created_by`, `email` FROM (' . $query . ') AS x' . $order . $limit;
		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		$authors = new stdClass();
		$authors->guest = [] ;
		$authors->registered = [];

		foreach ($result as $item) {
			$type = $item->created_by == '0' ? 'guest' : 'registered';

			array_push($authors->$type, $item);
		}

		return $authors;
	}

	public function getPopularComments($component = 'all', $cid = 'all', $options = [])
	{
		// define default values
		$defaultOptions	= array(
			'start'			=> 0,
			'limit'			=> 10,
			'userid'		=> 'all',
			'sticked'		=> 'all',
			// 'search'	=> '', future todo
			'published'		=> 1,
			'minimumlikes'	=> 0,
			'random'		=> 0,
			'threaded'		=> 0
		);

		$querySelect = '';
		$queryWhere = [];
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = KT::mergeOptions($defaultOptions, $options);

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes FROM ' . $this->db->nameQuote('#__komento_comments') . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $this->db->nameQuote('#__komento_actions') . ' AS actions ON comments.id = actions.comment_id';

		if ($component !== 'all')
		{
			$queryWhere[] = 'comments.component = ' . $this->db->quote($component);
		}

		if ($cid !== 'all')
		{
			if (is_array($cid))
			{
				$cid = implode(',', $cid);
			}

			if (empty($cid))
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if ($options['userid'] !== 'all')
		{
			$queryWhere[] = 'comments.created_by = ' . $this->db->quote($options['userid']);
		}

		if ($options['published'] !== 'all')
		{
			$queryWhere[] = 'comments.published = ' . $this->db->quote($options['published']);
		}

		if ($options['sticked'] !== 'all')
		{
			$queryWhere[] = 'comments.sticked = ' . $this->db->quote(1);
		}

		$queryWhere[] = 'actions.type = ' . $this->db->quote('likes');

		if (count($queryWhere) > 0)
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';

		if ($options['minimumlikes'] > 0)
		{
			$queryGroup .= ' HAVING likes >= ' . $options['minimumlikes'];
		}

		$queryOrder = ' ORDER BY likes DESC, created DESC';
		$queryLimit = ' LIMIT ' . $options['start'] . ',' . $options['limit'];

		$query = $querySelect . $queryWhere . $queryGroup . $queryOrder . $queryLimit;

		$this->db->setQuery($query);

		$results = $this->db->loadObjectList();

		if (! $results) {
			return ;
		}

		// build random
		if ($options['random'])
		{
			$buildRandom	= $this->buildRandom($results, $options);
		}

		$comments = [];

		foreach ($results as $row) {
			if (!$options['threaded']) {
				$row->depth = 0;
			}

			$comments[] = $row;
		}

		return $comments;
	}

	public function getTotalPopularComments($component = 'all', $cid = 'all', $options = [])
	{
		// define default values
		$defaultOptions	= array(
			'start'			=> 0,
			'limit'			=> 10,
			'userid'		=> 'all',
			'sticked'		=> 'all',
			// 'search'		=> '', future todo
			'published'		=> 1,
			'minimumlikes'	=> 0,
			'random'		=> 0
		);

		$querySelect = '';
		$queryWhere = [];
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = KT::mergeOptions($defaultOptions, $options);

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes FROM ' . $this->db->nameQuote('#__komento_comments') . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $this->db->nameQuote('#__komento_actions') . ' AS actions ON comments.id = actions.comment_id';

		if ($component !== 'all')
		{
			$queryWhere[] = 'comments.component = ' . $this->db->quote($component);
		}

		if ($cid !== 'all')
		{
			if (is_array($cid))
			{
				$cid = implode(',', $cid);
			}

			if (empty($cid))
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if ($options['userid'] !== 'all')
		{
			$queryWhere[] = 'comments.created_by = ' . $this->db->quote($options['userid']);
		}

		if ($options['published'] !== 'all')
		{
			$queryWhere[] = 'comments.published = ' . $this->db->quote($options['published']);
		}

		if ($options['sticked'] !== 'all')
		{
			$queryWhere[] = 'comments.sticked = ' . $this->db->quote(1);
		}

		$queryWhere[] = 'actions.type = ' . $this->db->quote('likes');

		if (count($queryWhere) > 0)
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';

		if ($options['minimumlikes'] > 0)
		{
			$queryGroup .= ' HAVING likes >= ' . $options['minimumlikes'];
		}

		$query = 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . $queryGroup . ') AS x';
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	public function deleteArticleComments($component, $cid)
	{
		$query  = 'DELETE FROM ' . $this->db->nameQuote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->nameQuote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->nameQuote('cid') . ' = ' . $this->db->quote($cid);

		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function getChildCount($ids, $nested = false)
	{
		$idsString = '';

		if (is_string($ids) || is_integer($ids))
		{
			$idsString = (string) $ids;

			$ids = explode(',', $idsString);
		}
		else
		{
			if (is_array($ids))
			{
				$idsString = implode(',', $ids);
			}
		}

		$query = 'SELECT ' . $this->db->nameQuote('parent_id') . ' AS id, COUNT(' . $this->db->nameQuote('parent_id') . ') AS child FROM ' . $this->db->nameQuote('#__komento_comments');
		$query .= ' WHERE ' . $this->db->nameQuote('parent_id') . ' IN(' . $idsString . ')';
		$query .= ' GROUP BY ' . $this->db->nameQuote('parent_id');

		$this->db->setQuery($query);

		$result = $this->db->loadObjectList();

		$childsCount = [];

		foreach ($ids as $id)
		{
			$match = false;

			foreach ($result as $row)
			{
				if ($row->id == $id)
				{
					$childsCount[$id] = $row->child;
					$match = true;
					break;
				}
			}

			if (!$match)
			{
				$childCount[$id] = 0;
			}
		}

		return $childsCount;
	}

	public function getUserTopCommentCount()
	{
		static $userTopCommentCount = null;

		if (is_null($userTopCommentCount))
		{
			$sql = KT::sql();

			$sql->select('#__komento_comments')
				->column('created_by', 'total', 'count')
				->where('created_by', '0', '<>')
				->where('published', '1')
				->group('created_by')
				->order('total', 'desc')
				->limit(1);

			$userTopCommentCount = $sql->loadResult();
		}

		return $userTopCommentCount;
	}

	public function getUsers($options = [])
	{
		$sql = KT::sql();

		$sql->select('#__komento_comments')
			->column('created_by', 'user', 'distinct');

		if (!empty($options['noguest']))
		{
			$sql->where('created_by', 0, '>');
		}

		if (!empty($options['component']))
		{
			$sql->where('component', $options['component']);
		}

		if (!empty($options['cid']))
		{
			$sql->where('cid', $options['cid']);
		}

		if (isset($options['state']))
		{
			$sql->where('published', $options['state']);
		}

		$result = $sql->loadColumn();

		return $result;
	}

	public function getRepliesCount($comment)
	{
		$db = $this->db;

		$query = "select count(1) from `#__komento_comments` as cc";
		$query .= " where cc.`component` = " . $db->Quote($comment->component);
		$query .= " and cc.`cid` = " . $db->Quote($comment->cid);
		$query .= " and cc.`published` = '1'";
		$query .= " and cc.`lft` > " . $db->Quote($comment->lft);
		$query .= " and cc.`rgt` < " . $db->Quote($comment->rgt);

		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}

	public function loadReplies($comment, $options = [] , $limit = 0)
	{
		$db = $this->db;
		$my = KT::user();

		// this loadReplies is used to load up x number of recent replies made to a comment.
		// in order to achieve this, we need to play with the start limit again.
		// startlimit == total replies - limit.
		// to get the total replies, use this formula:
		// ((rgt - lft) - 1) / 2

		$component = $comment->component;
		$cid = $comment->cid;
		$lft = $comment->lft;
		$rgt = $comment->rgt;
		$id = $comment->id;

		$totalReplies = 0;

		if (is_null($comment->childs)) {
			$boundary = ($rgt - $lft) - 1;
			if ($boundary > 0) {
				$totalReplies = floor($boundary / 2);
			}
		} else {
			$totalReplies = $comment->childs;
		}

		$query = 'SELECT a.*';

		if (isset($comment->rownumber)) {
			$query .= ',' . $comment->rownumber . ' as ' . $db->nameQuote('rownumber');
		} else {
			$query .= ',0 as ' . $db->nameQuote('rownumber');
		}

		// include the parent-of-all id.
		$query .= ',' . $comment->id . ' as ' . $db->nameQuote('pid');


		if (! $my->guest) {
			$query .= ', IFNULL(act1.`id`, 0) as `liked`, IFNULL(act2.`id`, 0) as `reported`';
		} else {
			$query .= ', 0 as `liked`, 0 as `reported`';
		}

		$query .= ' FROM ' . $db->nameQuote('#__komento_comments') . ' as a';
		if (! $my->guest) {
			$query .= ' left join ' . $db->nameQuote('#__komento_actions') . ' as act1 on a.`id` = act1.`comment_id` and act1.`type` = ' . $db->Quote('likes') . ' and act1.`action_by` = ' . $db->Quote($my->id);
			$query .= ' left join ' . $db->nameQuote('#__komento_actions') . ' as act2 on a.`id` = act2.`comment_id` and act2.`type` = ' . $db->Quote('report') . ' and act2.`action_by` = ' . $db->Quote($my->id);
		}

		$query .= ' WHERE ' . $db->namequote('a.component') . ' = ' . $db->Quote($component);

		$query .= ' AND ' . $db->namequote('a.cid') . ' = ' . $db->Quote($cid);

		if (isset($options['published'])) {
			$query .= ' AND ' . $db->namequote('a.published') . ' = ' . $db->Quote($options['published']);
		}

		$query .= ' AND ' . $db->namequote('a.lft') . ' BETWEEN ' . $db->Quote($lft) . ' AND ' . $db->Quote($rgt);
		$query .= ' AND ' . $db->namequote('a.id') . '!=' . $db->Quote($id);
		$query .= ' GROUP BY ' . $this->db->nameQuote('a.id');

		if (isset($options['threaded'])) {
			switch (strtolower($options['sort'])) {
				case 'latest' :
					$query .= ' ORDER BY ' . $db->nameQuote('a.rgt') . ' DESC';
					break;
				case 'oldest' :
				default :
					$query .= ' ORDER BY ' . $db->nameQuote('a.lft') . ' ASC';
			}
		} else {
			switch (strtolower($options['sort'])) {
				case 'latest' :
					$query .= ' ORDER BY ' . $db->nameQuote('a.created') . ' DESC';
					break;
				case 'oldest' :
				default :
					$query .= ' ORDER BY ' . $db->nameQuote('a.created') . ' ASC';
					break;
			}
		}

		if ($limit) {

			$startlimit = 0;

			if (isset($options['startlimit'])) {
				$startlimit = $options['startlimit'];
			} else {

				// we know we need to just retrieve x number of replies.
				if ($totalReplies > $limit) {
					$startlimit = $totalReplies - $limit;
				}
			}

			$query .= ' LIMIT ' . $startlimit . ', ' . $limit;
		}

		// echo $query;
		// echo '<br /><br />';

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}


	/*
	 * This method used to get the row number for parent-of-all and only used
	 * to generate comment's permalink
	 */
	public function getRowNumber($id, $sort)
	{
		$db = $this->db;

		$query = "select count(1) from `#__komento_comments` as a";
		$query .= " inner join `#__komento_comments` as b";
		$query .= "		on a.`component` = b.`component`";
		$query .= "			and a.`cid` = b.`cid`";
		if ($sort == 'oldest') {
			$query .= "			and a.`lft` >= b.`lft`";
		} else {
			$query .= "			and a.`lft` <= b.`lft`";
		}
		$query .= " WHERE a.`id` = " . $db->Quote($id);
		$query .= " and b.`published` = 1";
		$query .= " and b.`parent_id` = 0";

		$db->setQuery($query);
		$row = $db->loadResult();

		return $row;
	}

	/**
	 * Retrieve article permalink
	 *
	 * @since   3.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getItemPermalink($component, $cid)
	{
		// Select dinstict comment for this article
		$query = 'SELECT ' . $this->db->namequote('id') . ' FROM ' . $this->db->namequote('#__komento_comments');
		$query .= ' WHERE `component`=' . $this->db->Quote($component) . ' AND `cid`=' . $this->db->Quote($cid) . ' ORDER BY id desc';
		
		$this->db->setQuery($query);
		
		$commentId = $this->db->loadResult();

		// Load the comment
		$comment = KT::comment($commentId);

		return $comment->getItemPermalink();
	}

	/**
	 * Method used to get user's comments for GDPR download.
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function getCommentsGDPR($userid, $options = [])
	{
		$db = KT::db();

		$limit = isset($options['limit']) ? $options['limit'] : 20;
		$exclude = isset($options['exclude']) ? $options['exclude'] : [];

		if ($exclude && !is_array($exclude)) {
			$exclude = FH::makeArray($exclude);
		}

		$query = "select *";
		$query .= " from `#__komento_comments`";
		$query .= " where `created_by` = " . $db->Quote($userid);
		$query .= " and `published` = 1";

		if ($exclude) {
			$query .= " and `id` NOT IN (" . implode(',', $exclude) . ")";
		}

		$query .= " ORDER BY `created` desc LIMIT " . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

}