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

class KomentoMigratorJAComment extends KomentoMigratorBase
{
	public $published;
	public $selectedComponent;

	public function getComponentSelection()
	{
		$components = $this->getUniqueComponents();
		$selection = array();

		foreach ($components as $component) {
			$selection[] = JHtml::_('select.option', $component, KT::loadApplication($component)->getComponentName());
		}

		return $selection;
	}

	public function getUniqueComponents()
	{
		$where = ' WHERE `option` IN (' . implode(',', $this->getSupportedComponents()) . ')';
		$query = 'SELECT DISTINCT `option` FROM `#__jacomment_items`' . $where . ' ORDER BY `option`';

		$this->db->setQuery($query);
		return $this->db->loadResultArray();
	}

	public function migrate($publishState, $selectedComponent)
	{
		$this->selectedComponent = $selectedComponent;

		$options = [
			'parentid' => 0, 
			'option' => $selectedComponent
		];

		// First, we get the total comments to migrate
		$total = $this->getTotalComments($options);

		// get all comments from the specified itemid
		$items = $this->getComments($options);

		$balance = $total - count($items);

		$status = '';

		// If there is nothing to load, just skip
		if (empty($items)) {
			return $this->ajax->resolve(false, JText::_('COM_KT_MIGRATORS_NO_MIGRATED_ITEM'));			
		}

		$break = 0;

		foreach ($items as $jaComment) {
			$komentoInsertNode = false;

			if ($break == 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($jaComment->date, $selectedComponent, $jaComment->contentid);
			}
			
			$base = 1;

			if ($break == 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, $selectedComponent, $jaComment->contentid);
			} else {

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment($selectedComponent, $jaComment->contentid);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$jaComment->parentid = 0;
			$jaComment->depth = 0;
			$jaComment->lft = $base;
			$jaComment->rgt = $base + 1;

			$this->published = $publishState == 'inherit' ? $jaComment->published : $publishState;

			$kmtComment = $this->save($jaComment);

			if (!$kmtComment) {
				return $this->ajax->fail('Saving Failed: Comment ID:' . $jaComment->id);
			}

			if ($this->saveChildren($jaComment->id, $kmtComment->id, 0, $jaComment->contentid) === false) {
				return $this->ajax->fail('Saving Child Comment Failed:' . $jaComment->id);
			}

			// Add this to migrators table
			$this->addRecord('jacomment', $kmtComment->id, $jaComment->id);
			
			$status .= JText::sprintf('COM_KT_MIGRATOR_MIGRATED_COMMENTS', $jaComment->id, $kmtComment->id) . '<br />';

		}

		$hasMore = false;

		if ($balance) {
			$hasMore = true;
		}

		return $this->ajax->resolve($hasMore, $status);
	}

	public function saveChildren($oldid, $newid, $depth, $contentid)
	{
		$depth++;

		$options = [
			'parentid' => $oldid,
			'option' => $this->selectedComponent
		];

		$children = $this->getComments($options);

		foreach ($children as $child) {
			// populate child comment's lft rgt
			$child = $this->populateChildBoundaries($child, $newid, $depth, $contentid);
			$child->parent = $newid;
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
		$new['cid'] = $comment->contentid;
		$new['comment'] = $comment->comment;
		$new['name'] = $comment->name;
		$new['email'] = $comment->email;
		$new['url'] = $comment->website;
		$new['created'] = $comment->date;
		$new['created_by'] = $comment->userid;
		$new['published'] = $this->published;
		$new['parent_id'] = $comment->parentid;
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
	public function getTotalComments($options)
	{
		$query = 'SELECT COUNT(1) FROM `#__jacomment_items` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__komento_migrators` AS b WHERE b.`external_id` = a.`id` AND b.`component` = ' . $this->db->Quote('jacomment');
		$query .= ' )';
		$query .= ' AND ' . $this->db->nameQuote('parentid') . '=' . $this->db->Quote(0);

		if ($options['option'] !== 'all') {
			$query .= ' AND ' . $this->db->nameQuote('option') . ' = ' . $this->db->quote($options['option']);
		}

		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		return $total;
	}

	public function getComments($options = [])
	{
		$defaultOptions	= [
			'option' => 'all',
			'contentid' => 'all',
			'parentid' => 'all'
		];
		
		$options = KT::mergeOptions($defaultOptions, $options);

		$query = 'SELECT * FROM `#__jacomment_items` as a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT external_id FROM `#__komento_migrators` AS b WHERE b.`external_id` = a.`id` AND b.`component` = ' . $this->db->Quote('jacomment');
		$query .= ' )';

		if ($options['option'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('option') . ' = ' . $this->db->quote($options['option']);
		}

		if ($options['contentid'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('contentid') . ' = ' . $this->db->quote($options['contentid']);
		}

		if ($options['parentid'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('parentid') . ' = ' . $this->db->quote($options['parentid']);
		}

		$query .= ' ORDER BY date';

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
}
