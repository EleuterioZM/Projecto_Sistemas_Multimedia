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

class KomentoLikes
{
	public function __construct()
	{
		$this->my = JFactory::getUser();
		$this->profile = KT::user();
		$this->config = KT::config();
	}

	/**
	 * Determines if like functionality is enabled
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isEnabled()
	{
		static $enabled = null;

		if (is_null($enabled)) {

			// check for the current viewer is the guest user or not
			// because like button shouldn't show in public
			$isGuest = JFactory::getUser()->guest;
			
			$enabled = $this->config->get('enable_likes') && $this->profile->allow('like_comment') && !$isGuest;
		}

		return $enabled;
	}

	/**
	 * Determines if the like count should appear on the comment
	 *
	 * @since	3.0.12
	 * @access	public
	 */
	public function showLikeCount()
	{
		static $activate = null;

		if (is_null($activate)) {

			$activate = $this->config->get('enable_likes');
		}

		return $activate;
	}

	/**
	 * Allows caller to like a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function like($comment)
	{
		$this->toggleLike('likes', $comment);

		KT::activity()->process('like', $comment->id);

		return true;
	}

	/**
	 * Allows caller to dislike a comment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function dislike($comment)
	{
		$this->toggleLike('dislikes', $comment);

		KT::activity()->process('dislike', $comment->id);

		return true;
	}

	/**
	 * Allows caller to unlike a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unlike($comment)
	{
		// When the user unlikes a comment, we should just delete the action
		$table = KT::table('Actions');
		$table->load(['type' => 'likes', 'action_by' => $this->my->id, 'comment_id' => $comment->id]);

		$table->delete();

		KT::activity()->process('unlike', $comment->id);

		return true;
	}

	/**
	 * Allows caller to undo dislike a comment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removedislike($comment)
	{
		// When the user unlikes a comment, we should just delete the action
		$table = KT::table('Actions');
		$table->load(['type' => 'dislikes', 'action_by' => $this->my->id, 'comment_id' => $comment->id]);

		$table->delete();

		KT::activity()->process('removedislike', $comment->id);

		return true;
	}

	/**
	 * Method to create the like actions
	 *
	 * @since	3.0
	 * @access	public
	 */
	private function toggleLike($action, $comment)
	{
		$table = KT::table('Actions');
		$table->type = $action;
		$table->comment_id = $comment->id;
		$table->action_by = $this->my->id;
		$table->actioned = FH::date()->toSql();

		if (!$table->store()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if this user is already liked the comment
	 *
	 * @since   3.0.10
	 * @access  public
	 */
	public function isLiked($commentId, $userId)
	{
		$db = KT::db();
		$sql = $db->sql();

		$sql->select('#__komento_actions');
		$sql->column('id');
		$sql->where('action_by', $userId);
		$sql->where('comment_id', $commentId);
		$sql->where('type', 'likes');

		$db->setQuery($sql);
		$result = $db->loadResult();

		return $result ? true : false;
	}

	/**
	 * Determines if this user is already disliked the comment
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isDisliked($commentId, $userId)
	{
		$db = KT::db();
		$sql = $db->sql();

		$sql->select('#__komento_actions');
		$sql->column('id');
		$sql->where('action_by', $userId);
		$sql->where('comment_id', $commentId);
		$sql->where('type', 'dislikes');

		$db->setQuery($sql);
		$result = $db->loadResult();

		return $result ? true : false;
	}
}