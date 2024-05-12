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

KT::import('admin:/controllers/controller');

class KomentoControllerComments extends KomentoController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');

		$this->registerTask('feature', 'toggleFeatured');
		$this->registerTask('unfeature', 'toggleFeatured');

		$this->registerTask('apply', 'save');
		$this->registerTask('save', 'save');

		$this->registerTask('markCommentSpam', 'markCommentSpam');
		$this->registerTask('deleteCommentSpam', 'deleteCommentSpam');		
	}

	/**
	 * Allows caller to clear reported comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function clearReports()
	{
		FH::checkToken();

		$ids = $this->input->get('id', 0, 'int');

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		if (!$ids) {
			throw FH::exception('Invalid comment id provided', 500);
		}

		foreach ($ids as $id) {
			$id = (int) $id;

			$comment = KT::comment($id);
			$comment->removeReport();
		}

		$this->info->set('COM_KOMENTO_REPORT_CLEARED_FROM_SELECTED_COMMENTS', KOMENTO_MSG_SUCCESS);

		$returnUrl = 'index.php?option=com_komento&view=comments&layout=reports';
		return $this->app->redirect($returnUrl);
	}

	public function delete()
	{
		// Check for request forgeries
		FH::checkToken();

		$comments = $this->input->get('cid', [], 'POST');
		$return = $this->input->get('return', '', 'default');

		$redirect = base64_decode($return);

		if (count($comments) <= 0) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect($redirect);
		} 
			
		$message = 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED';
		$type = KOMENTO_MSG_SUCCESS;

		foreach ($comments as $id) {
			$comment = KT::comment($id);

			if (!$comment->delete()) {
				$message = 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED_ERROR';
				$type = KOMENTO_MSG_ERROR;
			}
		}

		$this->info->set($message, $type);
		$this->app->redirect($redirect);
	}

	public function save()
	{
		// Check for request forgeries
		FH::checkToken();

		$message = 'COM_KOMENTO_COMMENTS_SAVED';
		$type = KOMENTO_MSG_SUCCESS;

		$post = $this->input->post->getArray();
		$now = FH::date()->toSql();
		$user = JFactory::getUser();
		$id = $this->input->getInt('id', '');

		// gettable instead of getobj to avoid sending mails
		$comment = KT::comment($id);
		$newCid = (int) $post['cid'];
		$currentCid = (int) $comment->cid;

		$options = [];

		if ($newCid != $currentCid) {
			$options['previousCid'] = $currentCid;
		}

		// check if modified
		if ($post['comment'] != $comment->comment) {
			$comment->modified = $now;
			$comment->modified_by = $user->id;
		}

		$comment->bind($post);

		// check publish change
		if ($post['published'] != $comment->published) {
			if ($post['published'] == 0) {
				$comment->publish_down = $now;
			} else {
				$comment->publish_up = $now;
			}

			$comment->published = $post['published'];
		}

		if ($post['created']) {
			$created = FH::date($post['created'])->toSql();
			$comment->created = $created;
		}

		if (!$comment->save($options)) {
			$message = $comment->getError();
			$type = KOMENTO_MSG_ERROR;
		}

		$task = $this->getTask();
		$redirect = 'index.php?option=com_komento&view=comments';

		if ($task == 'apply') {
			$redirect = 'index.php?option=com_komento&view=comments&layout=form&id=' . $id;
		}

		$this->info->set($message, $type);
		$this->app->redirect($redirect);
	}

	/**
	 * Toggles publishing states for comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function togglePublish()
	{
		// Check for request forgeries
		FH::checkToken();

		$ids = $this->input->get('cid', []);
		
		if (!$ids) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=comments');
		}

		$task = $this->getTask() == 'publish' ? 1 : 0;

		foreach ($ids as $id) {
			$id = (int) $id;
			$comment = KT::comment($id);

			$comment->publish($task);
		}

		$message = 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED';

		if ($this->getTask() == 'unpublish') {
			$message = 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED';
		}

		$message = JText::_($message);

		$this->info->set($message, KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=comments');
	}

	/**
	 * Toggles featured state for comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function toggleFeatured()
	{
		// Check for request forgeries
		FH::checkToken();

		$ids = $this->input->get('cid', []);
		
		if (!$ids) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=comments');
		}

		$task = $this->getTask() == 'feature' ? 'feature' : 'unfeature';

		foreach ($ids as $id) {
			$id = (int) $id;
			$comment = KT::comment($id);

			$comment->$task();
		}

		$message = 'COM_KOMENTO_COMMENTS_COMMENT_FEATURED';

		if ($this->getTask() == 'unfeature') {
			$message = 'COM_KOMENTO_COMMENTS_COMMENT_UNFEATURED';
		}

		$this->info->set($message, KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=comments');
	}

	/**
	 * Bans the author of the comment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function banUser()
	{
		FH::checkToken();

		$comments = $this->input->get('cid', [], 'post');

		if (!$comments) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			return $this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
		}

		foreach ($comments as $id) {
			$comment = KT::comment($id);

			$author = $comment->getAuthor();

			if ($author->isAdmin() || !$author->id) {
				// Do not allow banning admins or guests
				$this->info->set('COM_KT_COMMENTS_COMMENT_BAN_ERROR', KOMENTO_MSG_ERROR);
				return $this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
			}

			$user = JFactory::getUser($author->id);
			$user->block = true;
			$user->save();

			// Log the user out after being blocked
			$this->app->logout($user->id, [
				'clientid' => $this->app->get('shared_session', '0') ? null : 0
			]);
		}

		$this->info->set('COM_KT_COMMENTS_COMMENT_BANNED', KOMENTO_MSG_SUCCESS);
		return $this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
	}

	public function notspam()
	{
		// Check for request forgeries
		FH::checkToken();

		$comments = $this->input->get('cid', [], 'POST');

		if (count($comments) <= 0) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
		} 
			
		$message = 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED';
		$type = KOMENTO_MSG_SUCCESS;

		foreach ($comments as $id) {
			$comment = KT::comment($id);

			if (!$comment->publish(1)) {
				$message = 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISH_ERROR';
				$type = KOMENTO_MSG_ERROR;
			}
		}

		$this->info->set($message, $type);
		$this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
	}

	public function markSpam()
	{
		// Check for request forgeries
		FH::checkToken();

		$comments = $this->input->get('cid', [], 'POST');
		$return = $this->input->get('return', '', 'default');
		
		$redirect = base64_decode($return);

		if (count($comments) <= 0) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect($redirect);
		} 
			
		$message = 'COM_KOMENTO_COMMENTS_COMMENT_SPAMMED';
		$type = KOMENTO_MSG_SUCCESS;

		foreach ($comments as $id) {
			$comment = KT::comment($id);

			if (!$comment->spam()) {
				$message = 'COM_KOMENTO_COMMENTS_COMMENT_SPAMMED_ERROR';
				$type = KOMENTO_MSG_ERROR;
			}
		}

		$this->info->set($message, $type);
		$this->app->redirect($redirect);
	}

	public function trainAkismet($submitType = 'spam')
	{
		// Check for request forgeries
		FH::checkToken();

		$comments = $this->input->get('cid', [], 'POST');
		$submitType = $this->input->get('action', $submitType);

		if (count($comments) <= 0) {
			$this->info->set('COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID', KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
		} 
			
		$message = 'COM_KOMENTO_FORM_NOTIFICATION_AKISMET_SUCCESS_SUBMIT_SPAM';
		$type = KOMENTO_MSG_SUCCESS;

		foreach ($comments as $id) {
			$comment = KT::comment($id);

			// Akismet detection
			$akismetData = [
				'author' => $comment->name,
				'email' => $comment->email,
				'website' => JURI::root(),
				'body' => $comment->comment
			];

			$action = ($submitType == 'spam') ? 'submitSpam' : 'submitHam';

			if (!KT::akismet()->$action($akismetData)) {
				$this->info->set('COM_KOMENTO_FORM_NOTIFICATION_AKISMET_ERROR_SUBMIT_SPAM', KOMENTO_MSG_ERROR);
				$this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
			}

			// Add flag to show that this has been sent to akismet
			$comment->flag(KOMENTO_COMMENT_AKISMET_TRAINED);
		}

		$this->info->set($message, $type);
		$this->app->redirect('index.php?option=com_komento&view=comments&layout=spamlist');
	}

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_komento&view=comments');

		return;
	}

	/**
	 * Allows caller to delete comments from the edit form
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function deleteCommentSpam()
	{
		// Check for request forgeries
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
		$return = $this->input->get('return', '', 'default');

		$redirect = base64_decode($return);

		$message = 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED';
		$type = KOMENTO_MSG_SUCCESS;

		$comment = KT::comment($id);

		if (!$comment->delete()) {
			$message = 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED_ERROR';
			$type = KOMENTO_MSG_ERROR;
		}

		$this->info->set($message, $type);
		$this->app->redirect($redirect);
	}

	/**
	 * Allows caller to mark spam in comment edit form
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function markCommentSpam()
	{
		// Check for request forgeries
		FH::checkToken();

		$id = $this->input->get('id', 0, 'int');
			
		$message = 'COM_KOMENTO_COMMENTS_COMMENT_SPAMMED';
		$type = KOMENTO_MSG_SUCCESS;

		$comment = KT::comment($id);

		if (!$comment->spam()) {
			$message = 'COM_KOMENTO_COMMENTS_COMMENT_SPAMMED_ERROR';
			$type = KOMENTO_MSG_ERROR;
		}

		$this->info->set($message, $type);
		$this->app->redirect('index.php?option=com_komento&view=comments');
	}	
}
