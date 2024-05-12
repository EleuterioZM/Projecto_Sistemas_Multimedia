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

require_once(dirname(__DIR__) . '/views.php');

class KomentoViewLikes extends KomentoView
{
	/**
	 * Allows caller to like / unlike a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function action()
	{
		$lib = KT::likes();

		if (!$lib->isEnabled()) {
			throw FH::exception('COM_KT_NOT_ALLOWED_LIKE_COMMENT', 500);
		}

		// Comment id
		$id = $this->input->get('id', 0, 'int');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KT_INVALID_ID', 500);
		}

		// Get the action type
		$action = $this->input->get('type', '', 'word');
		$allowed = ['like', 'unlike', 'dislike', 'removedislike'];

		if (!in_array($action, $allowed)) {
			throw FH::exception('Unknown action', 500);
		}

		// If the user is already liked the comment but try to like it again
		if ($action === 'like' && $lib->isLiked($comment->id, $this->my->id)) {
			return $this->ajax->reject();
		}

		// If the user is already liked the comment and click on the dislike button
		if ($action === 'dislike' && $lib->isLiked($comment->id, $this->my->id)) {
			// here we will remove his previous like record and add new dislike record
			$lib->unlike($comment);
			$lib->dislike($comment);

			return $this->ajax->resolve('unlike');
		}

		// if the user already disliked the comment and click on the like button
		if ($action === 'like' && $lib->isDisliked($comment->id, $this->my->id)) {
			// here we will remove his previous dislike record and add like
			$lib->removedislike($comment);
			$lib->like($comment);

			return $this->ajax->resolve('removedislike');
		}

		// Perform the action
		$lib->$action($comment);

		return $this->ajax->resolve();
	}

	/**
	 * Preview a list of users that also likes the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function browse()
	{
		$likes = KT::likes();

		if (!$likes->isEnabled()) {
			throw FH::exception('COM_KOMENTO_LIKES_ERROR_NOT_ALLOWED_VIEW_LIKES', 500);
		}

		// Comment id
		$id = $this->input->get('id', 0, 'int');
		$type = $this->input->get('type', 'likes', 'word');
		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KOMENTO_LIKES_ERROR_INVALID_COMMENT_ID', 500);
		}

		$model = KT::model('Actions');

		// Get maximum of 10 likes only for preview
		$users = $model->getLikedUsers($comment->id, 10, $type);

		// Get total likes
		$total = $this->input->get('total', 0, 'int');

		$theme = KT::themes();
		$theme->set('users', $users);
		$theme->set('id', $comment->id);
		$theme->set('total', $total);
		$output = $theme->output('site/likes/users/default');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders a list of users that likes the comment in form of dialog
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function browseAll()
	{
		$likes = KT::likes();

		if (!$likes->isEnabled()) {
			throw FH::exception('COM_KOMENTO_LIKES_ERROR_NOT_ALLOWED_VIEW_LIKES', 500);
		}

		// Comment id
		$id = $this->input->get('id', 0, 'int');

		$comment = KT::comment($id);

		if (!$id || !$comment->id) {
			throw FH::exception('COM_KOMENTO_LIKES_ERROR_INVALID_COMMENT_ID', 500);
		}

		$model = KT::model('Actions');
		$users = $model->getLikedUsers($comment->id);

		$theme = KT::themes();
		$theme->set('users', $users);
		$output = $theme->output('site/likes/dialogs/default');

		return $this->ajax->resolve($output);
	}
}
