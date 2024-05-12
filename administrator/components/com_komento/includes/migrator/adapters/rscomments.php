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

class KomentoMigratorRSComments extends KomentoMigratorBase
{
	public $published;
	public $selectedComponent;
	public $itemId;

	public function getUniqueComponents()
	{
		$where = ' WHERE `option` IN (' . implode(',', $this->getSupportedComponents()) . ')';
		$query = 'SELECT DISTINCT `option` FROM `#__rscomments_comments`' . $where . ' ORDER BY `option`';
		
		$this->db->setQuery($query);
		return $this->db->loadResultArray();
	}

	public function getComponentSelection()
	{
		$components = $this->getUniqueComponents();
		$selection = array();

		foreach ($components as $component) {
			$selection[] = JHtml::_('select.option', $component, KT::loadApplication($component)->getComponentName());
		}

		return $selection;
	}

	public function getPosts($selectedComponent)
	{
		$postIds = $this->getUniquePostId($selectedComponent);
		return $this->ajax->resolve($postIds);
	}

	public function getUniquePostId($selectedComponent = '')
	{
		$query = 'SELECT DISTINCT `id` FROM `#__rscomments_comments`';
		$query .= ' WHERE ' . $this->db->namequote('option') . ' = ' . $this->db->quote($options['option']);
		$query .= ' ORDER BY `option`, `id`';

		$this->db->setQuery($query);

		return $this->db->loadResultArray();
	}

	public function migrate($itemId, $publishState, $selectedComponent)
	{
		$this->selectedComponent = $selectedComponent;
		$this->itemId = $itemId;

		$options = array(
			'parent_id' => 0,
			'option' => $this->itemId
		);

		// get all comments from the specified itemid
		$rsComments = $this->getComments($options);

		if (empty($rsComments)) {
			return $this->ajax->resolve();
		}

		$break = 0;
		$count = 0;

		foreach ($rsComments as $rsComment) {
			$komentoInsertNode = false;

			if ($break == 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($rsComment->date, $selectedComponent, $rsComment->option);
			}
			
			$base = 1;

			if ($break == 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, $selectedComponent, $rsComment->option);
			} else {

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment($selectedComponent, $rsComment->option);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$rsComment->parent_id = 0;
			$rsComment->depth = 0;
			$rsComment->lft = $base;
			$rsComment->rgt = $base + 1;

			$this->published = $publishState == 'inherit' ? $rsComment->published : $publishState;

			$kmtComment = $this->save($rsComment);

			if ($kmtComment === false) {
				return $this->ajax->fail('save:' . $rsComment->id);
			}

			$count++;

			if ($this->saveChildren($rsComment->id, $kmtComment->id, 0) === false) {
				return $this->ajax->fail('savechildren:' . $rsComment->id);
			}

		}

		$this->ajax->append('[data-progress-status]', JText::sprintf('COM_KOMENTO_MIGRATORS_MIGRATED_ARTICLE_COMMENTS_TOTAL', $count, $itemId));

		return $this->ajax->resolve($count);
	}

	public function getComments($options = array())
	{
		$defaultOptions	= array(
			'option' => 'all',
			'id' => 'all'
		);

		$options = KT::mergeOptions($defaultOptions, $options);

		$query = 'SELECT * FROM `#__rscomments_comments`';

		$queryWhere = array();

		if ($options['option'] !== 'all') {
			$queryWhere[] = $this->db->namequote('option') . ' = ' . $this->db->quote($options['option']);
		}

		if ($options['id'] !== 'all') {
			$queryWhere[] = $this->db->namequote('id') . ' = ' . $this->db->quote($options['id']);
		}

		if (count($queryWhere) > 0) {
			$query .= ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$query .= ' ORDER BY date';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function saveChildren($oldid, $newid, $depth)
	{
		$depth++;

		$options = array(
			'parent' => $oldid,
			'option' => $this->itemId
		);

		$children = $this->getComments($options);

		foreach($children as $child)
		{
			// populate child comment's lft rgt
			$child = $this->populateChildBoundaries($child, $newid, $depth);
			$child->parent_id = $newid;
			$child->depth = $depth;

			$kmtComment = $this->save($child);

			if (!$kmtComment) {
				return $this->ajax->fail('save:' . $child->id);
			}

			$state = $this->saveChildren($child->id, $kmtComment->id, $depth);
			
			if (!$state) {
				return $this->ajax->fail('savechildren:' . $child->id);
			}
		}

		return true;
	}

	public function save($comment)
	{
		// Create a new comment object
		$new['component'] = $this->selectedComponent;
		$new['cid'] = $comment->option;
		$new['comment'] = $comment->comment;
		$new['name'] = $comment->name;
		$new['email'] = $comment->email;
		$new['url'] = $comment->website;

		// careful because rscomments save their datetime in unix format
		$new['created']	= FH::date($comment->date)->toSql();
		$new['created_by'] = $comment->uid;
		$new['published'] = $this->published;
		$new['parent_id'] = $comment->parent_id;
		$new['depth'] = $comment->depth;
		$new['lft'] = $comment->lft;
		$new['rgt'] = $comment->rgt;
		
		$kmtComment = KT::comment();
		$kmtComment->bind($new, false, array('fromMigration' => true));
		
		$state = $kmtComment->save();

		if (!$state) {
			return false;
		}

		return $kmtComment;
	}
}
