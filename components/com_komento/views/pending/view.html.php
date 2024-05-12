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

class KomentoViewPending extends KomentoView
{
	function display($tmpl = null)
	{
		$mainframe = JFactory::getApplication();
		$commentsModel = KT::model('comments');

		$cid = $this->input->get('cid', 'all', 'string');

		$filter['component']	= $mainframe->getUserStateFromRequest( 'com_komento.pending.filter_component', 'filter-component', 'all', 'string' );
		$filter['sort']			= $mainframe->getUserStateFromRequest( 'com_komento.pending.filter_sort', 'filter-sort', 'latest', 'string' );
		$filter['search']		= trim( FCJString::strtolower( $mainframe->getUserStateFromRequest( 'com_komento.pending.filter_search', 'filter-search', '', 'string' ) ) );

		$options = array(
			'limit'		=> 0,
			'sort'		=> $filter['sort'],
			'search'	=> $filter['search'],
			'published'	=> 2,
			'threaded'	=> 0
		);

		$comments = $commentsModel->getComments( $filter['component'], $cid, $options );
		$pagination = $commentsModel->getPagination();
		$components = $commentsModel->getUniqueComponents();

		$theme = KT::themes();
		$theme->set( 'components', $components );
		$theme->set( 'pagination', $pagination );
		$theme->set( 'comments', $comments );
		$theme->set( 'filter', $filter );

		echo $theme->fetch('dashboard/pending.php');
	}
}
