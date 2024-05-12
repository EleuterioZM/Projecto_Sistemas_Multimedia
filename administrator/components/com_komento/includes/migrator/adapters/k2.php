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

class KomentoMigratorK2 extends KomentoMigratorBase
{
	public function migrate($categoryId, $publishState)
	{
		// First, we get the total comments to migrate
		$total = $this->getTotalComments(['categories' => $categoryId]);

		// Get all the parent comments limit by 10 for the first batch
		$items = $this->getComments([
			'depth' => 0, 
			'categories' => $categoryId, 
			'limit' => 50
		]);

		$balance = $total - count($items);

		$status = '';

		// If there is nothing to load, just skip
		if (empty($items)) {
			return $this->ajax->resolve(false, JText::_('COM_KT_MIGRATORS_NO_MIGRATED_ITEM'));			
		}

		$break = 0;

		foreach ($items as $k2Comment) {
			$komentoInsertNode = false;

			if ($break === 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($k2Comment->commentDate, 'com_k2', $k2Comment->itemID);
			}

			$base = 1;

			if ($break === 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, 'com_k2', $k2Comment->itemID);
			} else {
				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment('com_k2', $k2Comment->itemID);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$k2Comment->parent_id = 0;
			$k2Comment->depth = 0;
			$k2Comment->lft = $base;
			$k2Comment->rgt = $base + 1;

			$this->published = $publishState == 'inherit' ? $k2Comment->published : $publishState;

			$kmtComment = $this->save($k2Comment);

			if (!$kmtComment) {
				return $this->ajax->fail('Saving Failed: Comment ID:' . $k2Comment->id);
			}

			// Add this to migrators table
			$this->addRecord('com_k2', $kmtComment->id, $k2Comment->id);
			
			$status .= JText::sprintf('COM_KT_MIGRATOR_MIGRATED_COMMENTS', $k2Comment->id, $kmtComment->id) . '<br />';
		}

		$hasMore = false;

		if ($balance) {
			$hasMore = true;
		}

		return $this->ajax->resolve($hasMore, $status);
	}

	public function save($comment)
	{
		// Create a new comment object
		$new['component'] = 'com_k2';
		$new['cid'] = $comment->itemID;
		$new['comment'] = $comment->commentText;
		$new['name'] = $comment->userName;
		$new['email'] = $comment->commentEmail;
		$new['url'] = $comment->commentURL;
		$new['created'] = $comment->commentDate;
		$new['created_by'] = $comment->userID;
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

	/**
	 * Get total items need to be migrated
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function getTotalComments($options = [])
	{
		$query = 'SELECT COUNT(1) FROM `#__k2_comments` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND b.`component` = ' . $this->db->Quote('com_k2');
		$query .= ' )';

		if ($options['categories'] !== 'all') {
			$itemIds = $this->getCategoryPosts($options['categories']);
			$query .= ' AND ' . $this->db->nameQuote('itemID') . ' IN (' . implode(',', $itemIds) . ')';
		}

		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		return $total;
	}

	public function getCategoryPosts($category = '')
	{
		if (!empty($category)) {

			$query = 'SELECT `id` FROM `#__k2_items`';
			
			if ($category != 'all') {
				$query .= ' WHERE `catid`=' . $this->db->quote($category);
			}

			$query .= ' ORDER BY `id`';

			$this->db->setQuery($query);
			return $this->db->loadResultArray();
		}

		return [];
	}

	public function getComments($options = array())
	{
		$query  = 'SELECT * FROM `#__k2_comments` as a';

		$defaultOptions	= [
			'categories' => 'all',
			'itemID' => 'all'
		];

		$options = KT::mergeOptions($defaultOptions, $options);
		
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND `component` = ' . $this->db->Quote('com_k2');
		$query .= ' )';

		if ($options['categories'] !== 'all') {
			$options['itemID'] = $this->getCategoryPosts($options['categories']);
		}

		if ($options['itemID'] !== 'all') {
			$options['itemID'] = (array) $options['itemID'];
			$query .= ' AND ' . $this->db->namequote('itemID')  . ' IN (' . implode(',', $options['itemID']) . ')';
		}

		$query .= ' ORDER BY commentDate';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function load($id)
	{
		$query = 'SELECT * FROM `#__k2_comments` WHERE `id` = ' . $this->db->quote($id);
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}

	public function clearComments()
	{
		$this->db->setQuery('DELETE FROM `#__komento_comments` WHERE `component` = ' . $this->db->quote('com_k2'));
		$this->db->query();

		$this->ajax->success();
		$this->ajax->send();
	}
}
