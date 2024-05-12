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

class KomentoFormatterComment
{
	protected $items = null;
	protected $cache = null;
	protected $options = null;

	public function __construct(&$items, $options = array(), $cache = true)
	{
		$this->items = $items;
		$this->cache = $cache;
		$this->options = $options;
	}

	/**
	 * Default method to format comments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function execute()
	{
		static $_cache = [];
		$result = [];
		$type = FH::normalize($this->options, 'type', 'inline');

		$isSingleObject = false;

		if (!is_array($this->items)) {
			$isSingleObject = true;

			// convert into array
			$this->items = [$this->items];
		}

		if ($this->cache) {
			KT::cache()->cacheAttachments($this->items);
			KT::cache()->cacheActionCounts($this->items);

			// test if these items are parent items or not. if yes,
			// lets cache the replies
			if (isset($this->options['loadreplies']) && $this->options['loadreplies'] && !$this->items[0]->parent_id) {
				KT::cache()->cacheReplies($this->items, $this->options);
			}
		}

		foreach ($this->items as $item) {

			// Load up the comment library
			$comment = KT::comment($item);

			$config = KT::config();
			$user = JFactory::getUser()->id;
			$commentsModel	= KT::model('Comments');

			// Get number of child for each comment
			if (! isset($item->childs)) {
				$repliesCnt = 0;
				$boundary = ($comment->rgt - $comment->lft) - 1;

				if (!$comment->parent_id && $boundary > 0) {
					$repliesCnt = floor($boundary / 2);
				}

				$comment->childs = $repliesCnt;
			}

			// 1. Load article and article details
			$application = KT::loadApplication($comment->component)->load($comment->cid);

			if($application === false) {
				$application = KT::getErrorApplication($comment->component, $comment->cid);
			}

			// set component title
			$comment->componenttitle = $application->getComponentName();

			// set content title
			$comment->contenttitle = $application->getContentTitle();

			// set extension object
			// use this to check if application is able to load article details
			// if row->extension is false, means error loading article details
			$comment->extension = $application;

			$actionsModel = KT::model('Actions');

			// Parse comments HTML
			$comment->preview = ($comment->preview) ? $comment->preview : KT::parser()->parseComment($comment->comment);

			// author's object
			$comment->author = $comment->getAuthor();

			// Don't convert for guest
			if ($comment->created_by != 0 && $comment->created_by != $comment->author->id) {
				KT::comment()->convertOrphanitem($comment->id);
			}

			// Get actions likes
			if (KT::cache()->exists($comment->id, 'likes')) {
				$comment->likes = KT::cache()->get($comment->id, 'likes');
			} else {
				$comment->likes = $actionsModel->countAction('likes', $comment->id);
			}

			// Get actions dislikes
			if (KT::cache()->exists($comment->id, 'dislikes')) {
				$comment->dislikes = KT::cache()->get($comment->id, 'dislikes');
			} else {
				$comment->dislikes = $actionsModel->countAction('dislikes', $comment->id);
			}

			// get reports count for this comment
			if (KT::cache()->exists($comment->id, 'report')) {
				$comment->reports = KT::cache()->get($comment->id, 'report');
			} else {
				$comment->reports = $actionsModel->countAction('report', $comment->id);;
			}
			
			// get user liked
			if (is_null($comment->liked)) {
				$comment->liked = $actionsModel->liked($comment->id, $user);
			}

			// get user disliked
			if (is_null($comment->disliked)) {
				$comment->disliked = $actionsModel->disliked($comment->id, $user);
			}

			// get user reported
			if (is_null($comment->reported)) {
				$comment->reported = $actionsModel->reported($comment->id, $user);
			}

			$model = KT::model('Actions');

			// Get maximum of 10 likes only for preview
			$comment->likedUsers = [];
			$comment->dislikedUsers = [];


			// cache these data incase the comment formatter being called multiple times in a single request.
			// e.g. from comments modules and listing page.
			$likedUsersIdx = 'likes' . $comment->id;
			$dislikedUsersIdx = 'dislikes' . $comment->id;

			// retrieve likedusers only if this comment has likes
			if ($comment->likes) {
				if (!isset($_cache[$likedUsersIdx])) {
					$_cache[$likedUsersIdx] = $model->getLikedUsers($comment->id, 10, 'likes');
				}

				$comment->likedUsers = $_cache[$likedUsersIdx];
			}

			// retrieve dislikedUsers only if this comment has dislikes
			if ($comment->dislikes) {
				if (!isset($_cache[$dislikedUsersIdx])) {
					$_cache[$dislikedUsersIdx] = $model->getLikedUsers($comment->id, 10, 'dislikes');
				}

				$comment->dislikedUsers = $_cache[$dislikedUsersIdx];
			}

			// If the placement is right or left, we disable indentation. #473
			if (KT::config()->get('layout_comment_placement') !== 'inline' || $type !== 'inline') {
				$comment->depth = false;
			}

			$result[] = $comment;

			if ($this->cache) {
				KT::cache()->set($comment, 'comment');
			}
		}

		return ($isSingleObject) ? $result[0] : $result;
	}

}
