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

KT::import('admin:/views/views');

class KomentoViewComments extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$layout = $this->getLayout() ? $this->getLayout() : 'default';

		$access = $layout;

		if ($layout === 'default') {
			$access = 'comments';
		}

		if ($layout === 'pending') {
			$access = 'pendings';
		}

		// Check for access
		$this->checkAccess('komento.manage.' . $access);

		$this->heading('COM_KOMENTO_HEADING_COMMENTS_' . strtoupper($layout));

		$this->registerToolbar($layout);

		$publishState = $this->app->getUserStateFromRequest('com_komento.comments.filter_publish', 'filter_publish', 'all', 'string');
		$selectedExtension = $this->app->getUserStateFromRequest('com_komento.comments.filter_component', 'filter_component', 'all', 'string');
		$search = $this->app->getUserStateFromRequest('com_komento.comments.search', 'search', '', 'string');
		$search = trim(FCJString::strtolower($search));
		$order = $this->app->getUserStateFromRequest('com_komento.comments.filter_order', 'filter_order', 'created', 'string');
		$orderDirection = $this->app->getUserStateFromRequest('com_komento.comments.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$parentId = $this->input->get('parentid', 0);

		$options = [
			'no_search' => $this->input->getInt('nosearch', 0),
			'parent_id' => $parentId,
			'published' => $publishState,
			'component' => $selectedExtension
		];

		$return = base64_encode('index.php?option=com_komento&view=comments');

		if ($layout === 'pending') {
			$options['published'] = 'pending';
			$options['no_tree'] = 1;
			$return = base64_encode('index.php?option=com_komento&view=comments&layout=pending');
		}

		if ($layout === 'reports') {
			$model = KT::model('reports');
			$comments = $model->getItems();
			$return = base64_encode('index.php?option=com_komento&view=comments&layout=reports');
		} 

		if ($layout !== 'reports') {
			$model = KT::model('comments');
			$comments = $model->getItems($options);
		}

		$limit = $model->getState('limit');
		$pagination = $model->getPagination();

		$comments = KT::formatter('comment', $comments, false);

		if ($search) {
			$parentId = 0;
		}

		$parent	= '';

		if ($parentId) {
			$parent = KT::comment($parentId);
			$parent = KT::formatter('comment', $parent, false);
		}

		$emptyMessage = 'COM_KOMENTO_COMMENTS_NO_COMMENT';

		if ($layout === 'reports') {
			$emptyMessage = 'COM_KOMENTO_COMMENTS_NO_REPORTED_COMMENTS';
		}

		if ($layout === 'pending') {
			$emptyMessage = 'COM_KOMENTO_COMMENTS_NO_PENDING_COMMENTS';
		}

		$this->set('publishState', $publishState);
		$this->set('return', $return);
		$this->set('limit', $limit);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);
		$this->set('parent', $parent);
		$this->set('parentid', $parentId);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('layout', $this->getLayout());
		$this->set('orderDirection', $orderDirection);
		$this->set('selectedExtension', $selectedExtension);
		$this->set('emptyMessage', $emptyMessage);

		parent::display('comments/default');
	}

	/**
	 * Renders the comment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function form()
	{
		$this->checkAccess('komento.manage.comments');

		$layout = $this->getLayout() ? $this->getLayout() : 'default';
		$from = $this->input->get('from', '', 'string');

		$id = $this->input->get('id', '');
		$comment = KT::comment($id);

		JToolBarHelper::title(JText::_('COM_KOMENTO_EDITING_COMMENT'), 'comments');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();

		if ($comment->published !== KOMENTO_COMMENT_SPAM && $from !== 'spamlist') {
			JToolBarHelper::custom('markCommentSpam', 'unpublish', '', JText::_('COM_KOMENTO_MARK_SPAM'), false);
		}
		
		JToolBarHelper::trash('deleteCommentSpam', JText::_('COM_KOMENTO_DELETE'), false);

		$return = base64_encode('index.php?option=com_komento&view=comments');

		if ($comment->isSpam()) {
			$return = base64_encode('index.php?option=com_komento&view=comments&layout=spamlist');
		}

		if ($comment->isPending()) {
			$return = base64_encode('index.php?option=com_komento&view=comments&layout=pending');
		}

		if ($comment->isReport()) {
			$return = base64_encode('index.php?option=com_komento&view=comments&layout=reports');
		}

		$cancelLink = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_komento&view=comments';

		if ($from === 'reports') {
			$cancelLink .= '&layout=reports';
		}

		if ($from === 'pending') {
			$cancelLink .= '&layout=pending';
		}

		$this->set('from', $from);
		$this->set('cancelLink', $cancelLink);
		$this->set('comment', $comment);
		$this->set('return', $return);

		parent::display('comments/form');
	}

	/**
	 * Renders a list of comments that are flagged as spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function spamlist()
	{
		// Check for access
		$this->checkAccess('komento.manage.spamlist');

		$this->heading('COM_KOMENTO_HEADING_COMMENTS_SPAMLIST');

		JToolBarHelper::custom('notspam', 'publish', '', JText::_('COM_KOMENTO_NOT_SPAM'));
		JToolBarHelper::trash('delete', JText::_('COM_KOMENTO_DELETE'));

		

		$selectedExtension = $this->app->getUserStateFromRequest('com_komento.spamlist.filter_component', 'filter_component', '*', 'string');
		$search = $this->app->getUserStateFromRequest('com_komento.spamlist.search', 'search', '', 'string');
		$search = trim(FCJString::strtolower($search));
		$order = $this->app->getUserStateFromRequest('com_komento.spamlist.filter_order', 'filter_order', 'created', 'string');
		$orderDirection = $this->app->getUserStateFromRequest('com_komento.spamlist.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		// Get data from the model
		$model = KT::model('Comments');
		$comments = $model->getItems([
			'published' => KOMENTO_COMMENT_SPAM
		]);

		$pagination = $model->getPagination();
		$comments = KT::formatter('comment', $comments, false);
		$limit = $model->getState('limit');
		$return = base64_encode('index.php?option=com_komento&view=comments&layout=spamlist');

		$this->set('layout', $this->getLayout());
		$this->set('limit', $limit);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('return', $return);
		$this->set('orderDirection', $orderDirection);
		$this->set('selectedExtension', $selectedExtension);

		parent::display('comments/spamlist');
	}

	public function registerToolbar($layout = 'default')
	{
		$parentId = $this->input->get('parentid', 0);

		if ($parentId) {
			$parent = KT::table('comments');
			$parent->load($parentId);
			JToolBarHelper::title(JText::_('COM_KOMENTO_COMMENTS_TITLE_CHILD_OF') . $parentId, 'comments');
			JToolBarHelper::back(JText::_('COM_KOMENTO_BACK'), 'index.php?option=com_komento&view=comments&parentid=' . $parent->parent_id);
		} else {
			JToolBarHelper::title(JText::_('COM_KOMENTO_COMMENTS_TITLE'), 'comments');
		}

		if ($layout == 'default') {
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolBarHelper::custom('feature', 'featured', '', JText::_('COM_KOMENTO_FEATURE'));
			JToolBarHelper::custom('unfeature', 'star-empty', '', JText::_('COM_KOMENTO_UNFEATURE'));
			JToolBarHelper::divider();
		}

		if ($layout == 'reports') {
			JToolBarHelper::title(JText::_('COM_KOMENTO_REPORTS'), 'reports');
			JToolBarHelper::divider();
			JToolBarHelper::custom('clearReports', 'wand', '', JText::_('COM_KOMENTO_CLEAR_REPORTS'));
			JToolBarHelper::divider();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}

		if ($layout == 'pending') {
			JToolBarHelper::title(JText::_('COM_KOMENTO_PENDING'), 'pending');
			JToolBarHelper::custom('publish', 'publish', '', JText::_('COM_KOMENTO_APPROVE'));
			JToolBarHelper::custom('unpublish', 'unpublish', '', JText::_('COM_KOMENTO_REJECT'));
		}

		JToolBarHelper::custom('markSpam', 'unpublish', '', JText::_('COM_KOMENTO_MARK_SPAM'));
		JToolBarHelper::trash('delete', JText::_('COM_KOMENTO_DELETE'));
	}
}
