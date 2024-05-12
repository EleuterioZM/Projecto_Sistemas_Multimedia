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

class KomentoMigratorJComments extends KomentoMigratorBase
{
	public $published;
	public $migrateLikes;
	public $selectedComponent;

	public function getComponentSelection()
	{
		$components = $this->getUniqueComponents();
		$selection = [];

		foreach ($components as $component) {
			$selection[$component] = KT::loadApplication($component)->getComponentName();
		}

		return $selection;
	}

	public function getUniqueComponents()
	{
		$where = ' WHERE `object_group` IN (' . implode(',', $this->getSupportedComponents()) . ')';
		$query = 'SELECT DISTINCT `object_group` FROM `#__jcomments`' . $where . ' ORDER BY `object_group`';
		
		$this->db->setQuery($query);
		return $this->db->loadResultArray();
	}

	public function getUniquePostId($objectGroup = '')
	{
		$query = 'SELECT DISTINCT `object_id` FROM `#__jcomments`';
		$query .= ' WHERE ' . $this->db->namequote('object_group') . ' = ' . $this->db->quote($objectGroup);
		$query .= ' ORDER BY `object_id`';

		$this->db->setQuery($query);

		return $this->db->loadResultArray();
	}


	public function migrate($publishState, $migrateLikes = false, $selectedComponent)
	{
		$this->migrateLikes = $migrateLikes;
		$this->selectedComponent = $selectedComponent;

		$options = [
			'parent' => 0, 
			'object_group' => $selectedComponent
		];

		// First, we get the total comments to migrate
		$total = $this->getTotalComments($options);

		// add the limits
		$options['limit'] = 50;

		// get all comments from the specified itemid
		$items = $this->getComments($options);

		$balance = $total - count($items);

		$status = '';

		if (empty($items)) {
			return $this->ajax->resolve(false, JText::_('COM_KT_MIGRATORS_NO_MIGRATED_ITEM'));
		}

		$break = 0;

		foreach ($items as $jComment) {
			$komentoInsertNode = false;

			if ($break === 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($jComment->date, $selectedComponent, $jComment->object_id);
			}
			
			$base = 1;

			if ($break === 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, $selectedComponent, $jComment->object_id);
			} else {

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment($selectedComponent, $jComment->object_id);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$jComment->parent = 0;
			$jComment->depth = 0;
			$jComment->lft = $base;
			$jComment->rgt = $base + 1;

			$this->published = $publishState == 'inherit' ? $jComment->published : $publishState;

			// Replace <br> tag to nl
			$jComment->comment = str_replace(array("<br />"), "\n", $jComment->comment);

			// Replace those [youtube][/youtube] tag to [video]
			$jComment->comment = str_replace(["[youtube]", "[/youtube]"], ["[video]", "[/video]"], $jComment->comment);			

			$kmtComment = $this->save($jComment);

			if (!$kmtComment) {
				return $this->ajax->fail('Saving Failed: Comment ID:' . $jComment->id);
			}

			if ($this->migrateLikes) {
				if ($this->saveLikes($jComment->id, $kmtComment->id) === false) {
					return $this->ajax->fail('savelikes:' . $jComment->id);
				}
			}

			if ($this->saveChildren($jComment->id, $kmtComment->id, 0, $jComment->object_id) === false) {
				return $this->ajax->fail('Saving Child Comment Failed:' . $jComment->id);
			}

			// Add this to migrators table
			$this->addRecord('jcomment', $kmtComment->id, $jComment->id);
			
			$status .= JText::sprintf('COM_KT_MIGRATOR_MIGRATED_COMMENTS', $jComment->id, $kmtComment->id) . '<br />';
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
		$new['component'] = $this->selectedComponent;
		$new['cid'] = $comment->object_id;
		$new['comment'] = $comment->comment;
		$new['name'] = $comment->name;
		$new['email'] = $comment->email;
		$new['url'] = $comment->homepage;
		$new['created'] = $comment->date;
		$new['created_by'] = $comment->userid;
		$new['published'] = $this->published;
		$new['parent_id'] = $comment->parent;
		$new['depth'] = $comment->depth;
		$new['lft'] = $comment->lft;
		$new['rgt'] = $comment->rgt;
		$new['ip'] = $comment->ip;
		
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
		$query = 'SELECT COUNT(1) FROM `#__jcomments` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND b.`component` = ' . $this->db->Quote('jcomment');
		$query .= ' )';
		$query .= ' AND ' . $this->db->nameQuote('parent') . '=' . $this->db->Quote(0);

		if ($options['object_group'] !== 'all') {
			$query .= ' AND ' . $this->db->nameQuote('object_group') . ' = ' . $this->db->quote($options['object_group']);
		}

		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		return $total;
	}

	public function getComments($options = [])
	{
		$defaultOptions	= [
			'level' => 'all',
			'object_group' => 'all',
			'object_id' => 'all',
			'thread_id' => 'all',
			'parent' => 'all',
			'limit' => 0
		];

		$options = KT::mergeOptions($defaultOptions, $options);

		$query = 'SELECT * FROM `#__jcomments` as a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND b.`component` = ' . $this->db->Quote('jcomment');
		$query .= ' )';

		if ($options['level'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('level') . ' = ' . $this->db->quote($options['level']);
		}

		if ($options['object_group'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('object_group') . ' = ' . $this->db->quote($options['object_group']);
		}

		if ($options['object_id'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('object_id') . ' = ' . $this->db->quote($options['object_id']);
		}

		if ($options['thread_id'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('thread_id') . ' = ' . $this->db->quote($options['thread_id']);
		}

		if ($options['parent'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('parent') . ' = ' . $this->db->quote($options['parent']);
		}

		$query .= ' ORDER BY date';

		if (isset($options['limit']) && $options['limit']) {
			$query .= ' LIMIT ' . $options['limit'];
		}

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function saveLikes($oldid, $newid)
	{
		$query  = 'INSERT INTO `#__komento_actions` (type, comment_id, action_by, actioned)';
		$query .= ' SELECT ' . $this->db->quote('likes') . ' AS type, ' . $this->db->quote($newid) . ' AS comment_id, `userid`, `date` FROM `#__jcomments_votes`';
		$query .= ' WHERE `value` = ' . $this->db->quote('1');
		$query .= ' AND `commentid` = ' . $this->db->quote($oldid);

		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function saveChildren($oldid, $newid, $depth, $objId)
	{
		$depth++;

		$options = [
			'parent' => $oldid,
			'object_group' => $this->selectedComponent
		];

		$children = $this->getComments($options);

		foreach ($children as $child) {
			// populate child comment's lft rgt
			$child = $this->populateChildBoundaries($child, $newid, 'jcomments', $objId);
			$child->parent = $newid;
			$child->depth = $depth;

			$kmtComment = $this->save($child);

			if (!$kmtComment) {
				return $this->ajax->fail('save:' . $child->id);
			}

			if ($this->migrateLikes) {

				$state = $this->saveLikes($child->id, $kmtComment->id);

				if (!$state) {
					return $this->ajax->fail('savelikes:' . $child->id);
				}
			}

			$state = $this->saveChildren($child->id, $kmtComment->id, $depth, $objId);
			
			if (!$state) {
				return $this->ajax->fail('savechildren:' . $child->id);
			}
		}

		return true;
	}
}
