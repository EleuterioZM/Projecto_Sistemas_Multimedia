<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/base.php');

class KomentoMigratorZoo extends KomentoMigratorBase
{
	public $published;

	public function getPosts($categoryIds)
	{
		$postIds = $this->getCategoryPosts($categoryIds);
		return $this->ajax->resolve($postIds);
	}

	public function migrate($itemId, $publishState)
	{
		// Get all the parent comments from Zoo
		$zooComments = $this->getComments([
			'parent_id' => 0, 
			'item_id' => $itemId
		]);

		if (empty($zooComments)) {
			return $this->ajax->resolve();
		}

		$break = 0;
		$count = 0;

		foreach ($zooComments as $zooComment) {

			$komentoInsertNode = false;

			if ($break === 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($zooComment->created, 'com_zoo', $zooComment->item_id);
			}

			$base = 1;

			if ($break === 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, 'com_zoo', $zooComment->item_id);
			} else {

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment('com_zoo', $zooComment->item_id);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$zooComment->parent_id = 0;
			$zooComment->depth = 0;
			$zooComment->lft = $base;
			$zooComment->rgt = $base + 1;

			$this->published = $publishState == 'inherit' ? $zooComment->state : $publishState;

			// Save the comment object
			$kmtComment = $this->save($zooComment);

			if (!$kmtComment) {
				return $this->ajax->fail('save:' . $zooComment->id);
			}

			$count++;

			// Save the children
			$state = $this->saveChildren($zooComment->id, $kmtComment->id, $zooComment->item_id, 0);

			if (!$state) {
				return $this->ajax->fail('savechildren:' . $zooComment->id);
			}
		}

		$this->ajax->append('[data-progress-status]', JText::sprintf('COM_KOMENTO_MIGRATORS_MIGRATED_ARTICLE_COMMENTS_TOTAL', $count, $itemId));

		return $this->ajax->resolve($count);
	}

	public function save($comment)
	{
		// Create a new comment object
		$new['component'] = 'com_zoo';
		$new['cid'] = $comment->item_id;
		$new['comment'] = $comment->content;
		$new['name'] = $comment->author;
		$new['email'] = $comment->email;
		$new['url'] = $comment->url;
		$new['created'] = $comment->created;
		$new['created_by'] = $comment->user_id;
		$new['published'] = $this->published;
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

	private function saveChildren($oldid, $newid, $cid, $depth)
	{
		$depth++;

		$options = [
			'parent_id'	=> $oldid,
			'item_id'	=> $cid
		];

		$children = $this->getComments($options);

		foreach($children as $child) {
			// populate child comment's lft rgt
			$child = $this->populateChildBoundaries($child, $newid, 'com_zoo', $cid);
			$child->parent_id = $newid;
			$child->depth = $depth;

			$kmtComment = $this->save($child);

			if (!$kmtComment) {
				return $this->ajax->fail('save:' . $child->id);
			}

			$state = $this->saveChildren($child->id, $kmtComment->id, $cid, $depth);
			
			if (!$state) {
				return $this->ajax->fail('savechildren:' . $child->id);
			}
		}

		return true;
	}

	public function getCategoryPosts($category = '')
	{
		if (!empty($category)) {

			$query = 'SELECT DISTINCT `item_id` FROM `#__zoo_category_item`';
			
			if ($category != 'all') {
				$query .= ' WHERE `category_id`=' . $this->db->quote($category);
			}

			$query .= ' ORDER BY `item_id`';

			$this->db->setQuery($query);
			return $this->db->loadResultArray();
		}

		return array();
	}

	public function getComments($options = array())
	{
		$query  = 'SELECT * FROM `#__zoo_comment`';

		$defaultOptions	= [
			'categories' => 'all',
			'item_id' => 'all',
			'parent_id' => 'all'
		];

		$options = KT::mergeOptions($defaultOptions, $options);

		$queryWhere = [];

		if ($options['categories'] !== 'all') {
			$options['item_id'] = $this->getCategoryPosts($options['categories']);
		}

		if ($options['item_id'] !== 'all') {
			$options['item_id'] = (array) $options['item_id'];
			$queryWhere[] = $this->db->namequote('item_id')  . ' IN (' . implode(',', $options['item_id']) . ')';
		}

		if ($options['parent_id'] !== 'all') {
			$queryWhere[] = $this->db->namequote('parent_id') . ' = ' . $this->db->quote($options['parent_id']);
		}

		if (count($queryWhere) > 0) {
			$query .= ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$query .= ' ORDER BY created';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function load($id)
	{
		$query = 'SELECT * FROM `#__zoo_comment` WHERE `id` = ' . $this->db->quote($id);
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}

	public function clearComments()
	{
		$this->db->setQuery('DELETE FROM `#__komento_comments` WHERE `component` = ' . $this->db->quote('com_zoo'));
		$this->db->query();

		$this->ajax->success();
		$this->ajax->send();
	}
}
