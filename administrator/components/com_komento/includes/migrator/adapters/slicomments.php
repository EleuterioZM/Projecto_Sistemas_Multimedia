<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/base.php');

class KomentoMigratorSliComments extends KomentoMigratorBase
{
	public function getPosts($categoryIds)
	{
		$postIds = $this->getCategoryPosts($categoryIds);
		return $this->ajax->resolve($postIds);
	}

	public function getCategoryPosts($category = '')
	{
		if (!empty($category)) {
			$query = 'SELECT `id` FROM `#__content`';

			if ($category != 'all') {
				$query .= ' WHERE `catid`=' . $this->db->quote($category);
			}

			$query .= ' ORDER BY `id`';

			$this->db->setQuery($query);
			return $this->db->loadResultArray();
		}

		return array();
	}

	public function migrate($itemId, $publishState)
	{
		// Get all the parent comments from k2
		$SliComments = $this->getComments(array('article_id' => $itemId));

		if (empty($SliComments)) {
			return $this->ajax->resolve();
		}

		$break = 0;
		$count = 0;

		foreach ($SliComments as $SliComment) {
			
			$komentoInsertNode = false;

			if ($break == 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($SliComment->commentDate, 'com_content', $SliComment->article_id);
			}

			$base = 1;

			if ($break == 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, 'com_content', $SliComment->article_id);
			} else {
				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment('com_content', $SliComment->article_id);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$SliComment->parent_id = 0;
			$SliComment->depth = 0;
			$SliComment->lft = $base;
			$SliComment->rgt = $base + 1;

			$published = $publishState == 'inherit' ? $SliComment->status : $publishState;

			// Save
			// Create a new comment object
			$new['component'] = 'com_content';
			$new['cid'] = $SliComment->article_id;
			$new['comment'] = $SliComment->raw;
			$new['name'] = $SliComment->name;
			$new['email'] = $SliComment->email;
			$new['created'] = $SliComment->created;
			$new['created_by'] = $SliComment->user_id;
			$new['published'] = $published;
			$new['parent_id'] = $SliComment->parent_id;
			$new['depth'] = $SliComment->depth;
			$new['lft'] = $SliComment->lft;
			$new['rgt'] = $SliComment->rgt;
			
			$kmtComment = KT::comment();
			$kmtComment->bind($new, false, array('fromMigration' => true));
			$state = $kmtComment->save();

			if ($state === false) {
				return $this->ajax->fail('save:' . $SliComment->id);
			}

			$count++;
		}

		$this->ajax->append('[data-progress-status]', JText::sprintf('COM_KOMENTO_MIGRATORS_MIGRATED_ARTICLE_COMMENTS_TOTAL', $count, $itemId));

		return $this->ajax->resolve($count);
	}

	public function getComments($options = array())
	{
		$query  = 'SELECT * FROM `#__slicomments`';

		$defaultOptions	= array(
			'categories' => 'all',
			'article_id' => 'all'
		);
		$options = KT::mergeOptions($defaultOptions, $options);

		$queryWhere = array();

		if ($options['categories'] !== 'all') {
			$options['article_id'] = $this->getCategoryPosts($options['categories']);
		}

		if ($options['article_id'] !== 'all') {
			$options['article_id'] = (array) $options['article_id'];
			$queryWhere[] = $this->db->namequote('article_id')  . ' IN (' . implode(',', $options['article_id']) . ')';
		}

		if (count($queryWhere) > 0) {
			$query .= ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$query .= ' ORDER BY created';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
}
