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

require_once(dirname(__FILE__) . '/base.php');

class KomentoMigratorEasyblog extends KomentoMigratorBase
{	
	public $published;
	public $migrateLikes;

	public function migrate($categoryId, $publishState, $migrateLikes = false)
	{
		$this->migrateLikes = $migrateLikes;

		// First, we get the total comments to migrate
		$total = $this->getTotalComments(['categories' => $categoryId]);

		// Get all the parent comments limit by 10 for the first batch
		$items = $this->getComments(['depth' => 0, 'categories' => $categoryId, 'limit' => 50]);

		$balance = $total - count($items);

		$status = '';

		// If there is nothing to load, just skip
		if (empty($items)) {
			return $this->ajax->resolve(false, JText::_('COM_KT_MIGRATORS_NO_MIGRATED_ITEM'));
		}

		$break = 0;

		foreach ($items as $ebComment) {
			
			$komentoInsertNode = false;

			if ($break == 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($ebComment->created, 'com_easyblog', $ebComment->post_id);
			}

			$relDiff = 0;
			$base = 0;

			if ($break == 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = $ebComment->rgt - $ebComment->lft + 1;

				$this->pushKomentoComment($base, $diff, 'com_easyblog', $ebComment->post_id);
			} else {
				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment('com_easyblog', $ebComment->post_id);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// calculate the relative difference based on insertion node's base
			$relDiff = $base - $ebComment->lft;

			// reset it to parent_id = 0 since this section is all parent comment
			$ebComment->parent_id = 0;
			$ebComment->depth = 0;
			$ebComment->lft += $relDiff;
			$ebComment->rgt += $relDiff;

			$this->published = $publishState == 'inherit' ? $ebComment->published : $publishState;

			$kmtComment = $this->save($ebComment);

			if (!$kmtComment) {
				return $this->ajax->fail('Saving Failed: Comment ID:' . $ebComment->id);
			}

			if ($this->migrateLikes) {
				$state = $this->saveLikes($ebComment->id, $kmtComment->id);
			}

			$state = $this->saveChildren($ebComment->id, $kmtComment->id, $relDiff, 0);
			
			if (!$state) {
				return $this->ajax->fail('Saving Child Comment Failed:' . $ebComment->id);
			}

			// Add this to migrators table
			$this->addRecord('com_easyblog', $kmtComment->id, $ebComment->id);
			
			$status .= JText::sprintf('COM_KT_MIGRATOR_MIGRATED_COMMENTS', $ebComment->id, $kmtComment->id) . '<br />';
		}

		$hasMore = false;

		if ($balance) {
			$hasMore = true;
		}

		return $this->ajax->resolve($hasMore, $status);
	}

	/**
	 * Get total items need to be migrated
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function getTotalComments($options = [])
	{
		$query = 'SELECT COUNT(1) FROM `#__easyblog_comment` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__komento_migrators` AS b WHERE b.`external_id` = a.`id` AND b.`component` = ' . $this->db->Quote('com_easyblog');
		$query .= ' )';
		$query .= ' AND ' . $this->db->nameQuote('parent_id') . '=' . $this->db->Quote(0);

		if ($options['categories'] !== 'all') {
			$postIds = $this->getCategoryPosts($options['categories']);
			$query .= ' AND ' . $this->db->nameQuote('post_id') . ' IN (' . implode(',', $postIds) . ')';
		}

		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		return $total;
	}

	public function save($comment)
	{
		// Create a new comment object
		$new['component'] = 'com_easyblog';
		$new['cid'] = $comment->post_id;
		$new['comment'] = $comment->comment;
		$new['name'] = $comment->name;
		$new['email'] = $comment->email;
		$new['url'] = $comment->url;
		$new['created'] = $comment->created;
		$new['created_by'] = $comment->created_by;
		$new['published'] = $this->published;
		$new['sent'] = $comment->sent;
		$new['parent_id'] = $comment->parent_id;
		$new['depth'] = $comment->depth;
		$new['lft'] = $comment->lft;
		$new['rgt'] = $comment->rgt;
		

		$kmtComment = KT::comment();
		$kmtComment->bind($new, false, ['fromMigration' => true]);

		$state = $kmtComment->save();

		if (!$state) {
			return false;
		}

		return $kmtComment;
	}

	private function saveChildren($ebid, $kmtid, $relDiff, $depth)
	{
		$depth++;

		$children = $this->getChildren($ebid);

		foreach ($children as $child) {
			$child->lft += $relDiff;
			$child->rgt += $relDiff;
			$child->parent_id = $kmtid;
			$child->depth = $depth;

			$newComment = $this->save($child);

			if (!$newComment) {
				return $this->ajax->fail('save:' . $child->id);
			}

			if ($this->migrateLikes) {

				$state = $this->saveLikes($child->id, $newComment->id);
				
				if (!$state) {
					return $this->ajax->fail('savelikes:' . $child->id);
				}
			}

			$state = $this->saveChildren($child->id, $newComment->id, $relDiff, $depth);

			if (!$state) {
				return $this->ajax->fail('savechildren:' . $child->id);
			}

			// Add this to migrators table
			$this->addRecord('com_easyblog', $newComment->id, $child->id);
		}

		return true;
	}

	public function getCategoryPosts($category = '')
	{
		if (!empty($category)) {

			$query = 'SELECT `id` FROM `#__easyblog_post`';
			
			if ($category != 'all') {
				$query .= ' WHERE `category_id`=' . $this->db->quote($category);
			}

			$query .= ' ORDER BY `id`';

			$this->db->setQuery($query);
			return $this->db->loadResultArray();
		}

		return false;
	}

	public function getComments($options = [])
	{
		$query  = 'SELECT x.*, COUNT(y.id) - 1 AS depth FROM `#__easyblog_comment` AS x';
		$query .= ' INNER JOIN `#__easyblog_comment` AS y';
		$query .= ' ON x.post_id = y.post_id';
		$query .= ' AND x.lft BETWEEN y.lft AND y.rgt';

		$defaultOptions	= [
			'categories' => 'all',
			'post_id' => 'all',
			'depth' => 'all',
			'limit' => 50
		];

		$options = KT::mergeOptions($defaultOptions, $options);

		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__komento_migrators` AS b WHERE b.`external_id` = x.`id` and `component` = ' . $this->db->Quote('com_easyblog');
		$query .= ' )';

		if ($options['categories'] !== 'all') {
			$options['post_id'] = $this->getCategoryPosts($options['categories']);
		}

		if (!empty($options['post_id' ]) && $options['post_id'] !== 'all') {
			$options['post_id'] = (array) $options['post_id'];
			$query .= ' AND x.' . $this->db->namequote('post_id')  . ' IN (' . implode(',', $options['post_id']) . ')';
		}

		$query .= ' AND x.`parent_id` = ' . $this->db->Quote(0);

		$query .= ' GROUP BY x.id';
		$query .= ' ORDER BY x.lft';

		if ($options['depth'] !== 'all') {
			$query = 'SELECT * FROM (' . $query . ') AS x WHERE `depth` = ' . $this->db->quote($options['depth']);
		}

		$query .= ' LIMIT ' . $options['limit'];

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function saveLikes($ebid, $kmtid)
	{
		$query  = 'INSERT INTO `#__komento_actions` (type, comment_id, action_by, actioned)';
		$query .= ' SELECT ' . $this->db->quote('likes') . ' AS type, ' . $this->db->quote($kmtid) . ' AS comment_id, created_by, created FROM `#__easyblog_likes`';
		$query .= ' WHERE `type` = ' . $this->db->quote('comment');
		$query .= ' AND `content_id` = ' . $this->db->quote($ebid);

		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function getChildren($ebid)
	{
		$eb = $this->load($ebid);

		$query  = 'SELECT * FROM (SELECT x.*, COUNT(y.id) - 1 AS depth FROM `#__easyblog_comment` AS x';
		$query .= ' INNER JOIN `#__easyblog_comment` AS y';
		$query .= ' ON x.post_id = y.post_id';
		$query .= ' AND x.lft BETWEEN y.lft AND y.rgt';
		$query .= ' WHERE x.post_id = ' . $this->db->quote($eb->post_id);
		$query .= ' AND y.post_id = ' . $this->db->quote($eb->post_id);
		$query .= ' AND x.lft BETWEEN ' . $this->db->quote($eb->lft) . ' AND ' . $this->db->quote($eb->rgt);
		$query .= ' AND y.lft BETWEEN ' . $this->db->quote($eb->lft) . ' AND ' . $this->db->quote($eb->rgt);
		$query .= ' GROUP BY x.id';
		$query .= ' ORDER BY x.lft) AS x WHERE depth = 1';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function load($id)
	{
		$query = 'SELECT * FROM `#__easyblog_comment` WHERE `id` = ' . $this->db->quote($id);
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}

	public function clearComments()
	{
		$db = KT::db();
		$db->setQuery('DELETE FROM `#__komento_comments` WHERE `component` = ' . $db->quote('com_easyblog'));
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}
}
