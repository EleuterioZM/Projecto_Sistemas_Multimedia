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

class KomentoModelReports extends KomentoModel
{
	protected $element = 'reports';
	public $_data;
	public $_total;
	public $_pagination;

	public $order;
	public $order_dir;
	public $limit;
	public $limitstart;
	public $filter_publish;
	public $filter_component;
	public $search;

	public function __construct($config = [])
	{
		parent::__construct($config);

		$mainframe = JFactory::getApplication();

		$db = KT::db();

		$this->limit = $mainframe->getUserStateFromRequest( 'com_komento.reports.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( 'com_komento.reports.limitstart', 'limitstart', 0, 'int' );
		$this->filter_publish = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_publish', 'filter_publish', '*', 'string' );
		$this->filter_component = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_component', 'filter_component', '*', 'string' );
		$this->order = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order', 'filter_order', 'created', 'cmd' );
		$this->order_dir = $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order_Dir','filter_order_Dir','DESC', 'word' );
		$this->search = $mainframe->getUserStateFromRequest( 'com_komento.reports.search', 'search', '', 'string' );

		$this->search = FH::escape(trim(FCJString::strtolower($this->search)));
	}

	public function getItems()
	{
		// Lets load the content ifit doesn't already exist
		if (empty($this->_data)) {
			$sql = $this->buildQuery();
			$this->_data = $sql->loadObjectList();
		}

		return $this->_data;
	}

	public function buildQuery()
	{
		$sql = KT::sql();

		$sql->select('#__komento_comments', 'a')
			->column('a.*')
			->column('b.comment_id', 'reports', 'count')
			->rightjoin('#__komento_actions', 'b')
			->on('a.id', 'b.comment_id')
			->where('type', 'report');

		// filter by component
		if ($this->filter_component != '*') {
			$sql->where('component', $this->filter_component);
		}

		// filter by publish state
		if ($this->filter_publish != '*') {
			$sql->where('published', $this->filter_publish);
		}

		if ($this->search) {
			$sql->where( 'LOWER(`comment`)', '%' . $this->search . '%', 'LIKE' );
		}

		$sql->group('comment_id')
			->order($this->order, $this->order_dir)
			->limit($this->limitstart, $this->limit);

		return $sql;
	}

	public function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if (empty($this->_pagination)) {
			
			$this->_pagination = KT::pagination($this->getTotal(), $this->limitstart, $this->limit);
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$sql = KT::sql();

			$sql->select( '#__komento_comments', 'a' )
				->column( 'a.id', 'id', 'count distinct' )
				->rightjoin( '#__komento_actions', 'b' )
				->on( 'a.id', 'b.comment_id' )
				->where( 'b.type', 'report' );

			$this->_total = $sql->loadResult();
		}

		return $this->_total;
	}
}
