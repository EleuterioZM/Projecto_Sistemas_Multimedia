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

class KomentoViewComments extends KomentoView
{
	public function __construct()
	{
		parent::__construct();

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');
		$id = $this->input->get('id', 0, 'int');

		if ($component == '' && $cid == 0 && $id != 0) {
			$tmp = KT::getTable('comments');
			$tmp->load($id);

			$component = $tmp->component;
			$cid = $tmp->cid;
		}

		if ($component) {
			KT::setCurrentComponent($component);
		}
	}

	/**
	 * Allows caller to save an edited comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		$data = $this->input->getArray('post');
		$comment = KT::comment($data['id']);

		if (!$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$comment->canEdit()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_EDIT_COMMENT', 500);
		}

		$data['comment'] = $this->input->get('comment', '', 'raw');
		$data['modified_by'] = $this->my->id;
		$data['modified'] = JFactory::getDate()->toSql();

		$comment->bind($data);

		$comment->save([
			'isEdited' => true, 
			'ignorePreSave' => true,
			'processAttachments' => true
		]);

		// If there is file removed during the edit, we should remove here
		if (isset($data['filesRemoved']) && $data['filesRemoved']) {
			foreach ($data['filesRemoved'] as $fileId) {
				$file = KT::table('Uploads');
				$file->load($fileId);
				$file->delete();
			}
		}

		$attachments = $comment->getAttachments();
		$attachmentHtml = '';
		$locationHtml = '';
		$ratingsHtml = '';

		if ($attachments) {
			$theme = KT::themes();
			$theme->set('comment', $comment);
			$theme->set('attachments', $attachments);

			$attachmentHtml = $theme->output('site/comments/attachments');
		}

		if ($comment->hasLocation()) {
			$theme = KT::themes();
			$theme->set('comment', $comment);

			$locationHtml = $theme->output('site/comments/location');
		}

		if ($comment->ratings) {
			$theme = KT::themes();
			$ratingsHtml = $theme->fd->html('rating.item', [
				'score' => $comment->ratings,
				'showScore' => true
			]);
		}

		// Get the content so that we can update the comment's content
		$contents = $comment->getContent();
		$message = JText::sprintf('COM_KOMENTO_COMMENT_EDITTED_BY', $comment->getModifiedDate()->toLapsed(), KT::themes()->html('html.name', $comment->modified_by));

		return $this->ajax->resolve($message, $contents, $attachmentHtml, $locationHtml, $ratingsHtml);
	}

	/**
	 * Allows caller to submit a new comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function add()
	{
		FH::checkToken();

		$data = $this->input->getArray('post');
		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		$comment = KT::comment();

		if (!$comment->canPostComments()) {
			return $this->ajax->reject($comment->getError());
		}

		// We need to get the comment
		$data['comment'] = $this->input->get('comment', '', 'raw');

		// Bind the data
		$comment->bind($data);

		// Validate the comment
		$state = $comment->validate($data);

		if ($state === false) {
			return $this->ajax->reject($comment->getError());
		}

		// Validate the comment for spam
		$state = $comment->validateSpam();

		if ($state === false) {
			return $this->ajax->reject($comment->getError());
		}

		// Save the comment
		$state = $comment->save([
			'processAttachments' => true
		]);

		// Throw some errors
		if ($state === false) {
			throw FH::exception($comment->getError(), 500);
		}

		// Initialise additional values = 0
		$comment->likes = 0;
		$comment->liked = 0;
		$comment->dislikes = 0;
		$comment->disliked = 0;
		$comment->sticked = 0;
		$comment->childs = 0;
		$comment->reported = 0;

		// Format the comments
		$comment = KT::formatter('comment', $comment->table);

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$theme->set('application', $application);

		$html = $theme->output('site/comments/item');

		// Purge the cache in case the site is caching contents
		FH::clearCache();

		$message = JText::_('COM_KOMENTO_FORM_NOTIFICATION_SUBMITTED');

		if ($comment->published == KOMENTO_COMMENT_MODERATE) {
			$message = JText::_('COM_KOMENTO_FORM_NOTIFICATION_PENDING');
		}

		if ($comment->published == KOMENTO_COMMENT_SPAM) {
			$spamType = $comment->getSpamType();
			$message = JText::sprintf('COM_KOMENTO_FORM_NOTIFICATION_SPAM', ucfirst($spamType));
		}

		$sorting = $this->config->get('default_sort', 'oldest');

		return $this->ajax->resolve($message, $html, $comment->published, $sorting);
	}

	public function loadmore()
	{
		// $config = KT::getConfig();
		$profile = KT::user();
		// $ajax = KT::getAjax();
		$commentsModel = KT::model('comments');

		$defaultsort = $this->config->get('default_sort', 'oldest');
		$limit = $this->config->get('max_comments_per_page');
		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', '', 'cmd');
		$start = $this->input->get('start', 0, 'default');
		$sort = $this->input->get('sort', $defaultsort, 'default');

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		$options = [
			'limit' => $limit,
			'limitstart' => $start,
			'sort' => $sort,
			'parentid' => '0',
			'threaded' => $this->config->get('enable_threaded'),
			'loadreplies' => true,
			'sticked' => false
		];

		if (!$profile->allow('read_comment')) {
			return $this->ajax->reject(JText::_('COM_KOMENTO_ACL_NO_PERMISSION'));
		}

		$comments = $commentsModel->getComments($component, $cid, $options);
		$commentCount = $commentsModel->getTotal($component, $cid, $options);

		$loadedComments = count($comments);

		$html = "";

		if ($comments) {
			$comments = KT::formatter('comment', $comments, $options);

			foreach ($comments as $comment) {

				$theme = KT::themes();
				$theme->set('comment', $comment);
				$theme->set('application', $application);

				$html .= $theme->output('site/comments/item');
			}
		}

		$loadMoreLink = '';
		$nextstart = $start + $limit;

		if ($nextstart >= $commentCount) {
			$nextstart = -1;
		} else {
			$contentLink = $application->getContentPermalink();
			$loadMoreLink = '#comments_' . $nextstart;

			if (!FH::isJoomla4()) {
				$loadMoreLink = $contentLink . $loadMoreLink;
			}
		}

		return $this->ajax->resolve($html, $nextstart, $loadMoreLink);
	}

	public function loadComments()
	{
		$commentsModel = KT::model('comments');
		$profile = KT::user();

		$defaultsort = $this->config->get('default_sort', 'oldest');
		$limit = $this->config->get('max_comments_per_page');
		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', '', 'cmd');
		$endlimit = $this->input->get('endlimit', 0, 'default'); // endlimit
		$sort = $this->input->get('sort', $defaultsort, 'default');
		$sticked = $this->input->get('sticked', 1, 'default');

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		if ($endlimit) {
			// we are simulating the limit start using the 'limit' in query.
			$endlimit = $endlimit + $limit;
		} else {
			$endlimit = $limit;
		}

		$options = [
			'limit' => $endlimit,
			'limitstart' => 0,
			'parentid' => 0,
			'sort' => $sort,
			'threaded' => $this->config->get('enable_threaded'),
			'loadreplies' => true,
			'sticked' => $sticked
		];

		if (!$profile->allow('read_comment')) {
			return $this->ajax->reject(JText::_('COM_KOMENTO_ACL_NO_PERMISSION'));
		}

		$comments = $commentsModel->getComments($component, $cid, $options);
		$commentCount = $commentsModel->getTotal($component, $cid, $options);


		$loadedComments = count($comments);

		if ($loadedComments == 0) {
			// this is not suppose to happen. return fail state.
			return $this->ajax->reject();
		}

		$html = "";

		if ($comments) {
			$comments = KT::formatter('comment', $comments, $options);

			foreach ($comments as $comment) {

				$theme = KT::themes();
				$theme->set('comment', $comment);
				$theme->set('application', $application);

				$html .= $theme->output('site/comments/item');
			}

		}

		$loadMoreLink = '';
		$nextstart = $endlimit;

		if ($nextstart >= $commentCount) {
			$nextstart = -1;
		} else {
			$contentLink = $application->getContentPermalink();
			$loadMoreLink = '#comments_' . $nextstart;

			if (!FH::isJoomla4()) {
				$loadMoreLink = $contentLink . $loadMoreLink;
			}
		}

		return $this->ajax->resolve($html, $nextstart, $loadMoreLink);
	}


	public function loadReplies()
	{
		$commentsModel = KT::model('comments');
		$profile = KT::user();

		$defaultsort = $this->config->get('default_sort', 'oldest');
		$limit = $this->config->get('reply_autohide') ? $this->config->get('reply_autohide_threshold') : 0;
		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', '', 'cmd');
		$sort = $this->input->get('sort', $defaultsort, 'default');
		$rownumber = $this->input->get('rownumber', 0, 'default');
		$parentId = $this->input->get('parentid', 0, 'default');

		$comment = KT::comment($parentId);
		$comment->rownumber = $rownumber;

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		$repliesCount = $comment->getRepliesCount();

		if (!$repliesCount) {
			return $this->ajax->resolve(false);
		}

		$endlimit = $limit;

		if ($repliesCount > $limit) {
			$endlimit = $repliesCount - $limit;
		}

		$options = [
			'limit' => $endlimit,
			'parentid' => $parentId,
			'sort' => $sort,
			'published' => 1,
			'threaded' => $this->config->get('enable_threaded'),
			'startlimit' => 0
		];

		if (!$profile->allow('read_comment')) {
			return $this->ajax->reject(JText::_('COM_KOMENTO_ACL_NO_PERMISSION'));
		}

		$comments = $commentsModel->loadReplies($comment, $options, $endlimit);

		$loadedComments = count($comments);

		if ($loadedComments == 0) {
			// this is not suppose to happen. return fail state.
			return $this->ajax->reject();
		}

		$html = "";

		if ($comments) {
			$comments = KT::formatter('comment', $comments, $options);

			foreach ($comments as $comment) {

				$theme = KT::themes();
				$theme->set('comment', $comment);
				$theme->set('application', $application);

				$html .= $theme->output('site/comments/item');
			}

		}

		return $this->ajax->resolve($html);
	}



	/**
	 * Allows caller to reload a set of comments on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function reload()
	{
		FH::checkToken();

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', '', 'int');
		$sort = $this->input->get('sort', '', 'word');
		$contentLink = $this->input->get('contentLink', '', 'default');

		$model = KT::model('Comments');

		$application = KT::loadApplication($component)->load($cid);

		if ($application === false) {
			$application = KT::getErrorApplication($component, $cid);
		}

		if (!$this->my->allow('read_comment')) {
			return $this->ajax->reject('You are not allowed here');
		}

		// check if allowed in admin mode
		if ( isset( $options['published'] ) && $options['published'] != '1' && !$acl->allow( 'publish', '', $component, $cid ) ) {
			$ajax->fail(JText::_('COM_KOMENTO_ERROR'));
			$ajax->send();
		}

		$options = [];
		$options['sort'] = $sort;

		$total = $model->getCount($component, $cid, $options);

		// Load previous comments
		if ($this->config->get('load_previous')) {
			$options['limitstart'] = $total - $this->config->get('max_comments_per_page');

			if ($options['limitstart'] < 0) {
				$options['limitstart'] = 0;
			}
		}

		$options['threaded'] = $this->config->get('enable_threaded');

		$comments = $model->getComments($component, $cid, $options);

		$theme = KT::themes();
		$theme->set('ajaxcall', 1 );
		$theme->set('component', $component);
		$theme->set('cid', $cid);
		$theme->set('comments', $comments);
		$theme->set('options', $options);
		$theme->set('commentCount', $total);
		$theme->set('application', $application);
		$theme->set('contentLink', $contentLink);
		$html = $theme->output('site/comments/list.php');

		return $this->ajax->resolve($html, count($comments), $total);
	}

	public function getComment()
	{
		if (!$this->my->allow('read_comment')) {
			return $this->ajax->reject();
		}

		$id = $this->input->get('id', 0, 'int');

		$comment = KT::getComment($id);
		$comment = KomentoCommentHelper::process($comment);

		$themes = KT::themes();
		$themes->set('row', $comment);

		// todo: configurable
		$html  = $parentTheme->fetch( 'comment/item/avatar.php' );
		$html .= $parentTheme->fetch( 'comment/item/author.php' );
		$html .= $parentTheme->fetch( 'comment/item/time.php' );
		$html .= $parentTheme->fetch( 'comment/item/text.php' );

		return $this->ajax->resolve($html);
	}

	/**
	 * Allows caller to edit the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function edit()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$comment->canEdit()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_EDIT_COMMENT', 500);
		}

		$attachments = $comment->getAttachments();

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$theme->set('attachments', $attachments);
		$output = $theme->output('site/form/edit/default');

		$data = $comment->export();

		return $this->ajax->resolve($output, $data);
	}

	/**
	 * Renders the delete confirmation dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$comment->canDelete()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_DELETE_COMMENT', 500);
		}

		$theme = KT::themes();
		$theme->set('comment', $comment);

		$output = $theme->output('site/comments/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog to confirm submit comment as spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmSubmitSpam()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$output = $theme->output('site/comments/dialogs/spam');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog to confirm unpublish comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function confirmUnpublish()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$output = $theme->output('site/comments/dialogs/unpublish');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allows caller to delete a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('id', 0, 'int');

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$childs = [];

		$commentsModel = KT::model('comments');

		foreach ($ids as $id) {
			$id = (int) $id;
			$comment = KT::comment($id);

			$childs = $commentsModel->getChilds($id);

			if (!$id || !$comment->id) {
				throw FH::exception('COM_KT_INVALID_ID', 500);
			}

			// Ensure that the user is really allowed to delete
			if (!$comment->canDelete()) {
				throw FH::exception('COM_KT_NOT_ALLOWED_DELETE_COMMENT', 500);
			}

			$repliesCount = $commentsModel->getRepliesCount($comment);

			// Try to delete the comment now
			$state = $comment->delete();

			if (!$state) {
				throw FH::exception('COM_KT_NOT_ALLOWED_DELETE_COMMENT', 500);
			}
		}

		return $this->ajax->resolve($childs, $repliesCount);
	}

	/**
	 * Unpublishes a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unpublish()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$comment->canUnpublish()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_UNPUBLISH_COMMENT', 500);
		}

		// Unpublish the comment
		$state = $comment->publish(0);

		return $this->ajax->resolve();
	}

	public function submitSpam()
	{
		$id	= $this->input->get('id', 0, 'int');

		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		if (!$comment->canSubmitSpam()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_SUBMIT_COMMENT_AS_SPAM', 500);
		}

		// submit the comment as spam
		$comment->spam();

		return $this->ajax->resolve();
	}

	/**
	 * Expands a comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function expand()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canMinimize()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_PIN_COMMENT', 500);
		}

		// Try to pin the comment now
		$comment->expand();

		return $this->ajax->resolve();
	}

	/**
	 * Minimizes a comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function minimize()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canMinimize()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_PIN_COMMENT', 500);
		}

		// Try to pin the comment now
		$comment->minimize();

		return $this->ajax->resolve();
	}

	/**
	 * Pin a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function pin()
	{
		$id = $this->input->get('id', 0, 'int');
		$hasFeatured = $this->input->get('hasFeatured', 0, 'int');

		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canFeature()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_PIN_COMMENT', 500);
		}

		$userId = JFactory::getUser()->id;

		// Try to pin the comment now
		$comment->feature();

		$application = $comment->getApplication();
		$theme = KT::themes();
		$theme->set('application', $application);


		// get the require data that used in theme file
		$actionsModel = KT::model('Actions');
		$comment->likes = $actionsModel->countAction('likes', $comment->id);
		$comment->dislikes = $actionsModel->countAction('dislikes', $comment->id);

		// Get maximum of 10 likes only for preview
		$comment->likedUsers = $actionsModel->getLikedUsers($comment->id, 10, 'likes');
		$comment->dislikedUsers = $actionsModel->getLikedUsers($comment->id, 10, 'dislikes');

		$comment->liked = $actionsModel->liked($comment->id, $userId);
		$comment->disliked = $actionsModel->disliked($comment->id, $userId);




		if ($hasFeatured) {
			// if the page already has the featured container, when we should not return another featured container again.
			$theme->set('comment', $comment);
			$theme->set('pinned', true);
			$html = $theme->output('site/comments/item');
			return $this->ajax->resolve($html);
		}

		$theme->set('pinnedComments', [$comment]);
		$html = $theme->output('site/comments/featured');

		return $this->ajax->resolve($html);
	}

	/**
	 * Renders the dialog to confirm unpublish comments
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmPin()
	{
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);


		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canFeature()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_PIN_COMMENT', 500);
		}


		$theme = KT::themes();
		$theme->set('comment', $comment);
		$output = $theme->output('site/comments/dialogs/pin');

		return $this->ajax->resolve($output);
	}

	/**
	 * Unpins a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unpin()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canFeature()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_UNPIN_COMMENT', 500);
		}

		// Try to pin the comment now
		$comment->unfeature();

		return $this->ajax->resolve();
	}

	/**
	 * Renders the dialog to confirm unpublish comments
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmUnpin()
	{
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);


		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Ensure that the user has permissions
		if (!$comment->canFeature()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_UNPIN_COMMENT', 500);
		}

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$output = $theme->output('site/comments/dialogs/unpin');

		return $this->ajax->resolve($output);
	}

	/**
	 * Checks for new comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function check()
	{
		FH::checkToken();

		$user = KT::user();

		// We don't have to check if it is guest
		if (!$this->config->get('enable_live_notification') || !$user->id) {
			throw FH::exception('COM_KT_LIVE_NOTIFICATION_DISABLED_MESSAGE', 500);
		}

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');

		if (!$component || !$cid) {
			throw FH::exception('COM_KT_INVALID_REQUEST', 500);
		}

		if (!$user->allow('read_comment') || !$user->allow('read_others_comment')) {
			throw FH::exception('COM_KOMENTO_ACL_NO_PERMISSION', 500);
		}

		$lastchecktime = $this->input->get('lastchecktime', FH::date()->toSql(), 'default');
		$excludeIds = $this->input->get('excludeIds', [], 'array');

		$model = KT::model('Comments');
		$comments = $model->getNewComments($component, $cid, $lastchecktime, $user->id, $excludeIds);

		$options = [
			'sorting' => $this->config->get('default_sort'),
			'placement' => $this->config->get('layout_comment_placement')
		];

		return $this->ajax->resolve($comments, $options);
	}

	/**
	 * Display terms and conditions dialog message
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function terms()
	{
		FH::checkToken();

		$content = JText::_($this->config->get('tnc_text'));

		$content = nl2br($content);

		$theme = KT::themes();
		$theme->set('content', $content);
		$output = $theme->output('site/form/dialogs/terms');

		return $this->ajax->resolve($output);
	}
	
	/**
	 * Show the attachment items from the comment
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function viewAttachmentItems()
	{
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		$attachments = $comment->getAttachments();

		$theme = KT::themes();
		$theme->set('comment', $comment);
		$theme->set('attachments', $attachments);

		$output = $theme->output('site/comments/attachments.list');

		return $this->ajax->resolve($output);
	}

	/**
	 * Load the commment list html
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function fetchComments()
	{
		FH::checkToken();

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', '', 'int');
		$type = $this->input->get('type', 'inline', 'default');
		$options = $this->input->get('commentOptions', '', 'string');
		$returnURL = $this->input->get('returnUrl', '', 'string');
		$options = json_decode($options, true);

		$options['type'] = $type;
		$options['returnURL'] = $returnURL;

		KT::load('commentify');
		$commentify = new KomentoCommentify($component, true);

		// Loading article infomation with defined get methods
		if (!$commentify->adapter->load($cid)) {
			return $this->ajax->reject(JText::_('COM_KOMENTO_UNABLE_TO_LOAD_ARTICLE_DETAILS'));
		}

		$html = $commentify->entry($cid, $cid, $options);

		return $this->ajax->resolve($html);
	}

	/**
	 * Query users from the site (for mention)
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function searchUsers()
	{
		$query = $this->input->get('q', '', 'string');
		
		$model = KT::model('Users');
		$results = $model->getMentionUsers(['search' => $query, 'exclusion' => [$this->my->id]]);

		$names = [];

		foreach ($results as $result) {
			$user = new stdClass;
			$user->key = $result->name;
			$user->value = $result->username;
			$names[] = $user;
		}
		
		return $this->ajax->resolve($names);
	}

	/**
	 * Retrieve likes and dislikes of the comment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getLikedUsers()
	{
		$type = $this->input->get('type', 'likes', 'string');
		$id = $this->input->get('id', '', 'int');

		$limit = 30;

		$model = KT::model('Actions');
		$users = $model->getLikedUsers($id, $limit + 1, $type);

		$hasLoadMore = count($users) > $limit;

		if ($hasLoadMore) {
			array_pop($users);
		}

		$theme = KT::themes();
		$theme->set('users', $users);
		$theme->set('type', $type);
		$theme->set('hasLoadMore', $hasLoadMore);
		$theme->set('limitstart', $limit);

		$themeFile = $this->config->get('layout_avatar_enable') ? 'avatar' : 'name';

		$output = $theme->output('site/comments/likes/' . $themeFile . '.list');

		return $this->ajax->resolve($output);
	}

	/**
	 * Load more of the liked users of the comment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function loadmoreLikedUsers()
	{
		$limitstart = $this->input->get('start', 0, 'int');
		$type = $this->input->get('type', 'likes', 'string');
		$id = $this->input->get('id', 0, 'int');

		$limit = 30;

		$model = KT::model('Actions');
		$users = $model->getLikedUsers($id, $limit + 1, $type, ['start' => $limitstart]);

		$hasLoadMore = count($users) > $limit;

		if ($hasLoadMore) {
			array_pop($users);
		}

		$output = '';
		$themeFile = $this->config->get('layout_avatar_enable') ? 'avatar' : 'name';

		foreach ($users as $user) {
			$themes = KT::themes();
			$themes->set('user', $user);
			$output .= $themes->output('site/comments/likes/' . $themeFile . '.item', $user);
		}

		return $this->ajax->resolve($output, $hasLoadMore, $limit + $limitstart);
	}

	/**
	 * Location suggestions
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getLocations()
	{
		FH::checkToken();

		$lat = $this->input->get('latitude', '', 'string');
		$lng = $this->input->get('longitude', '', 'string');
		$query = $this->input->get('query', '', 'string');

		// Get the configured service provider for location
		$provider = $this->config->get('location_service_provider');

		$service = KT::location($provider);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		if ($lat && $lng) {
			$service->setCoordinates($lat, $lng);
		}

		if ($query) {
			$service->setSearch($query);
		}

		$venues = $service->getResult($query);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		$theme = KT::themes();
		$html = $theme->fd->html('location.list', ['locations' => $venues]);

		return $this->ajax->resolve($html);
	}
}
