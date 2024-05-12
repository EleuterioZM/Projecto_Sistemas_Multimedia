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

class KomentoCache
{
	public $comments = null;
	private $comment = [];
	private $attachment = [];
	private $report = [];
	private $likes = [];
	private $replies = [];
	private $dislikes = [];

	// types that we will be caching
	private $types = ['comment'];

	/**
	 * Load comment's attchments in batch processing and cache the results
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cacheAttachments($comments)
	{
		$ids = [];
		$data = [];

		foreach ($comments as $comment) {
			$ids[] = $comment->id;

			// setup the initial values.
			$data[$comment->id] = [];
		}

		$model = KT::model('Uploads');
		$results = $model->loadBatchAttachments($ids);

		foreach ($results as $item) {
			$data[$item->uid][] = $item;
		}

		// now lets cache it.
		foreach ($ids as $id) {
			$this->attachment[$id] = $data[$id];
		}

	}

	/**
	 * Load comment's attchments in batch processing and cache the results
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cacheReplies($comments, $options = [])
	{
		$ids = [];
		$pcomments = [];
		$data = [];

		foreach ($comments as $comment) {
			// setup the initial values.
			$data[$comment->id] = [];
			$ids[] = $comment->id;


			$repliesCnt = 0;
			$boundary = ($comment->rgt - $comment->lft) - 1;

			if ($boundary > 0) {
				$repliesCnt = floor($boundary / 2);
			}

			if ($repliesCnt > 0) {
				$pcomments[] = $comment;
			}

		}

		if ($pcomments) {
			$model = KT::model('Comments');

			$allreplies = [];

			$config = KT::config();
			$repliesLimit = $config->get('reply_autohide') ? $config->get('reply_autohide_threshold') : 0;
			$repliesLimit = (int) $repliesLimit;

			// filter only published replies
			$options['published'] = 1;

			foreach ($pcomments as $pcomment) {

				$results = $model->loadReplies($pcomment, $options, $repliesLimit);

				if ($results) {
					$data[$pcomment->id] = $results;

					$allreplies = array_merge($allreplies, $results);
				}
			}

			$type = FH::normalize($options, 'type', 'inline');

			if ($allreplies) {
				$allreplies = KT::formatter('comment', $allreplies, array('loadreplies' => false, 'type' => $type));
			}
		}

		foreach ($ids as $id) {
			$this->replies[$id] = $data[$id];
		}
	}


	public function cacheActionCounts($comments)
	{
		$ids = [];
		$data = [];

		foreach ($comments as $comment) {
			$ids[] = $comment->id;

			// setup the initial values.
			$data['likes'][$comment->id] = 0;
			$data['report'][$comment->id] = 0;
			$data['dislikes'][$comment->id] = 0;
		}

		$model = KT::model('Actions');
		$results = $model->loadBatchActionCounts($ids);

		foreach ($results as $item) {
			$data[$item->type][$item->comment_id] = $item->cnt;
		}

		// now lets cache it.
		foreach ($ids as $id) {
			$this->report[$id] = $data['report'][$id];
			$this->likes[$id] = $data['likes'][$id];
			$this->dislikes[$id] = $data['dislikes'][$id];
		}
	}

	/**
	 * Adds a cache for a specific item type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function set($item, $type = 'comment')
	{
		$this->{$type}[$item->id] = $item;
	}

	/**
	 * set cache for the object type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function get($id, $type = 'comment')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return $this->{$type}[$id];
		}

		// There should be a fallback method if the cache doesn't exist
		// return $this->fallback($id, $type);
	}

	/**
	 * Retrieves a fallback
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function fallback($id, $type)
	{
		$table = KT::table($type);
		$table->load($id);

		$this->set($table, $type);

		return $table;
	}

	/**
	 * check if the cache for the object type exists or not
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function exists($id, $type = 'comment')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return true;
		}

		return false;
	}
}