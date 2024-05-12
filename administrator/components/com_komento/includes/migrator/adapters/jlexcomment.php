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

class KomentoMigratorJlexcomment extends KomentoMigratorBase
{
	public $published;
	public $migrateLikes;
	public $component;

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


	public function migrate($publishState, $migrateLikes = false)
	{
		$this->migrateLikes = $migrateLikes;

		// get the parent first
		$options = [
			'parent' => 0,
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

		foreach ($items as $jlexComment) {
			$komentoInsertNode = false;
			$this->component = 'com_' . $jlexComment->com_name;

			if ($break === 0) {
				$komentoInsertNode = $this->getKomentoInsertNode($jlexComment->created_time, $this->component, $jlexComment->com_id);
			}
			
			$base = 1;

			if ($break === 0 && $komentoInsertNode) {
				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->pushKomentoComment($base, $diff, $this->component, $jlexComment->com_id);
			} else {

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = KT::model('comments')->getLatestComment($this->component, $jlexComment->com_id);

				if ($komentoLatestComment) {
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$jlexComment->parent = 0;
			$jlexComment->depth = 0;
			$jlexComment->lft = $base; // 1
			$jlexComment->rgt = $base + 1; // 2

			$this->published = $publishState == 'inherit' ? $jlexComment->published : $publishState;

			// Process comment to use bbcode format
			$jlexComment->comment = $this->processCommentContent($jlexComment->comment);

			$kmtComment = $this->save($jlexComment);

			if (!$kmtComment) {
				return $this->ajax->fail('Saving Failed: Comment ID:' . $jlexComment->id);
			}

			if ($this->migrateLikes) {
				if ($this->saveLikes($jlexComment->id, $kmtComment->id) === false) {
					return $this->ajax->fail('savelikes:' . $jlexComment->id);
				}
			}

			if ($this->saveChildren($jlexComment->id, $kmtComment->id, 0, $jlexComment->com_id) === false) {
				return $this->ajax->fail('Saving Child Comment Failed:' . $jlexComment->id);
			}

			// Add this to migrators table
			$this->addRecord('jlexcomment', $kmtComment->id, $jlexComment->id);
			
			$status .= JText::sprintf('COM_KT_MIGRATOR_MIGRATED_COMMENTS', $jlexComment->id, $kmtComment->id) . '<br />';
		}

		$hasMore = false;

		if ($balance) {
			$hasMore = true;
		}

		return $this->ajax->resolve($hasMore, $status);
	}

	public function processCommentContent($comment)
	{
		// Replace <br> tag to nl
		$comment = str_replace(array("<br>"), "\n", $comment);
		
		// Replace those [youtube][/youtube] tag to [video]
		$comment = str_replace(["[youtube]", "[/youtube]"], ["[video]", "[/video]"], $comment);

		// Replace those <b>, </b> tag to [b]
		$comment = str_replace(["<b>", "</b>"], ["[b]", "[/b]"], $comment);
		$comment = str_replace(["<i>", "</i>"], ["[i]", "[/i]"], $comment);
		$comment = str_replace(["<u>", "</u>"], ["[u]", "[/u]"], $comment);

		return $comment;
	}

	public function processAttachment($jlexCommentId, $komentoId)
	{
		// get the file path
		$query = 'SELECT * FROM `#__jlexcomment_media`';
		$query .= ' WHERE ' . $this->db->namequote('comment_id') . ' = ' . $this->db->quote($jlexCommentId);

		$this->db->setQuery($query);

		$attachments = $this->db->loadObjectList();
		
		if (!$attachments) {
			return;
		}

		foreach ($attachments as $attachment) {
			$fileItem = [];
			$fileItem['type'] = $attachment->fileType;
			$fileItem['size'] = $attachment->fileSize;
			$fileItem['name'] = $attachment->fileName;
			$fileItem['tmp_name'] = JPATH_ROOT . '/' . $attachment->path;

			$file = KT::file();
			$id = $file->upload($fileItem);
			$state = $file->attach($id, $komentoId);
		}
	}

	public function save($comment, $parentId = 0)
	{
		// Create a new comment object
		$new['component'] = $this->component;
		$new['cid'] = $comment->com_id;
		
		$name = $comment->guest_name;
		$email = $comment->guest_email;

		// Load komento user
		if ($comment->created_by) {
			$user = KT::user($comment->created_by);

			$name = $user->getName();
			$email = $user->email;
		}

		$new['name'] = $name;
		$new['email'] = $email;
		$new['created'] = $comment->created_time;
		$new['created_by'] = $comment->created_by;
		$new['published'] = $this->published;
		$new['parent_id'] = $parentId !== 0 ? $parentId : $comment->parent_id;
		$new['depth'] = $comment->depth;
		$new['lft'] = $comment->lft;
		$new['rgt'] = $comment->rgt;
		$new['ip'] = $comment->ip_address;

		// Process stickers
		$this->processStickers($comment);

		// Process Mention
		$this->processMentions($comment);

		$new['comment'] = $comment->comment;

		$kmtComment = KT::comment();
		$kmtComment->bind($new, false, ['fromMigration' => true]);
		
		$state = $kmtComment->save();

		if (!$state) {
			return false;
		}

		// process attachment
		$this->processAttachment($comment->id, $kmtComment->id);

		return $kmtComment;
	}

	public function processStickers(&$comment)
	{
		// get stickers used in the comment
		$query = 'SELECT `sticker_id` FROM `#__jlexcomment`';
		$query .= ' WHERE ' . $this->db->namequote('id') . ' = ' . $this->db->quote($comment->id);

		$this->db->setQuery($query);

		$sticker = $this->db->loadResult();
		
		// Get sticker data
		$query = 'SELECT * FROM `#__jlexcomment_sticker`';
		$query .= ' WHERE ' . $this->db->namequote('id') . ' = ' . $this->db->quote($sticker);

		$this->db->setQuery($query);
		$sticker = $this->db->loadObject();

		if ($sticker) {
			$comment->comment = $comment->comment . ' [img]' . rtrim(JURI::root(), '/') . '/' . $sticker->path2file . '[/img] ';
		}
	}

	public function processMentions(&$comment)
	{
		$extendedlatinPattern = "\\x{0c0}-\\x{0ff}\\x{100}-\\x{1ff}\\x{180}-\\x{27f}";
		$arabicPattern = "\\x{600}-\\x{6FF}";
		$pattern = '/{u-[0-9]+[\,]+[' . $extendedlatinPattern . $arabicPattern . 'A-Za-z0-9][' . $extendedlatinPattern . $arabicPattern . 'A-Za-z0-9_\-\.\s\,\&]+}/ui';

		$text = $comment->comment;
		$text = html_entity_decode($text);

		preg_match_all($pattern, $text, $matches);

		if (!isset($matches[0]) || !$matches[0]) {
			return false;
		}

		$result = $matches[0];

		$users = [];

		foreach ($result as $mention) {
			preg_match('/{u-[0-9]+\,(.*?)}/', $mention, $match);

			if (!isset($matches[0]) || !$matches[0]) {
				continue;
			}

			$search = array($match[0]);
			$replace = '@' . $match[1] . '­';
			$text = str_ireplace($search, $replace, $text);
			$comment->comment = $text;
		}
	}

	/**
	 * Get total items need to be migrated
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function getTotalComments($options = [])
	{
		$query = 'SELECT COUNT(1) FROM `#__jlexcomment` AS a';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND b.`component` = ' . $this->db->Quote('jlexcomment');
		$query .= ' )';
		$query .= ' AND ' . $this->db->nameQuote('parent_id') . '=' . $this->db->Quote(0);

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

		$query = 'SELECT a.*, c.`com_name`, c.`com_id` FROM `#__jlexcomment` as a';
		$query .= ' INNER JOIN `#__jlexcomment_obj` as c on a.obj_id = c.id';
		$query .= ' WHERE NOT EXISTS (';
		$query .= ' SELECT `external_id` FROM `#__komento_migrators` AS b';
		$query .= ' WHERE b.`external_id` = a.`id`';
		$query .= ' AND b.`component` = ' . $this->db->Quote('jlexcomment');
		$query .= ' )';
		

		if ($options['level'] !== 'all') {
			$query .= ' AND ' . $this->db->namequote('level') . ' = ' . $this->db->quote($options['level']);
		}

		if ($options['object_group'] !== 'all') {
			$query .= ' AND object_group = ' . $this->db->quote($options['object_group']);
		}

		if ($options['object_id'] !== 'all') {
			$query .= ' AND object_id = ' . $this->db->quote($options['object_id']);
		}

		if ($options['thread_id'] !== 'all') {
			$query .= ' AND thread_id = ' . $this->db->quote($options['thread_id']);
		}

		if ($options['parent'] !== 'all') {
			$query .= ' AND a.parent_id = ' . $this->db->quote($options['parent']);
		}

		$query .= ' ORDER BY a.created_time';

		if (isset($options['limit']) && $options['limit']) {
			$query .= ' LIMIT ' . $options['limit'];
		}

		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	public function saveLikes($oldid, $newid)
	{
		// we process upvote first
		$query  = 'INSERT INTO `#__komento_actions` (type, comment_id, action_by, actioned)';
		$query .= ' SELECT ' . $this->db->quote('likes') . ' AS type, ' . $this->db->quote($newid) . ' AS comment_id, `created_by`, `created_time` FROM `#__jlexcomment_vote`';
		$query .= ' WHERE `point` = ' . $this->db->quote('1');
		$query .= ' AND `comment_id` = ' . $this->db->quote($oldid);

		$this->db->setQuery($query);
		return $this->db->query();

		// next is downvote
		$query  = 'INSERT INTO `#__komento_actions` (type, comment_id, action_by, actioned)';
		$query .= ' SELECT ' . $this->db->quote('dislikes') . ' AS type, ' . $this->db->quote($newid) . ' AS comment_id, `created_by`, `created_time` FROM `#__jlexcomment_vote`';
		$query .= ' WHERE `point` = ' . $this->db->quote('-1');
		$query .= ' AND `comment_id` = ' . $this->db->quote($oldid);

		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function saveChildren($oldid, $newid, $depth, $objId)
	{
		$depth++;

		$options = [
			'parent' => $oldid
		];

		$children = $this->getComments($options);

		foreach ($children as $child) {
			// populate child comment's lft rgt
			$child = $this->populateChildBoundaries($child, $newid, $this->component, $objId);
			$child->parent = $newid;
			$child->depth = $depth;

			// Process comment to use bbcode format
			$child->comment = $this->processCommentContent($child->comment);

			$kmtComment = $this->save($child, $newid);

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
