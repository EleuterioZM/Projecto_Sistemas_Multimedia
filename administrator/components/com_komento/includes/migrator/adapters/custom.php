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

class KomentoMigratorCustom extends KomentoMigratorBase
{
	public function migrate($options = [])
	{
		$comments = $this->getData($options);

		if (!$comments) {
			return $this->ajax->resolve('noitem');
		}

		$count = count($comments);

		foreach($comments as $comment) {
			
			if ($comment->cid == 'notavailable') {
				continue;
			}

			$comment->parent_id = 0;

			if (isset($options['componentFilter']) && $options['componentFilter']) {
				$comment->component = $options['componentFilter'];
			}

			$kmtComment = KT::comment();
			$kmtComment->bind($comment);
			
			$kmtComment->save();

			$this->ajax->append('[data-progress-status]', JText::sprintf('COM_KOMENTO_MIGRATORS_CUSTOM_MIGRATED_COMMENTS', $kmtComment->id));
		}

		$newStart = $options['start'] + $count;

		return $this->ajax->resolve($newStart);
	}

	public function getData($options)
	{
		$query = 'SELECT ';

		$columns = [];

		if ($options['contentid'] != 'notavailable') {
			$columns[] = $this->db->namequote($options['contentid']) . ' AS `cid`';
		}

		if ($options['comment'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['comment']) . ' AS `comment`';
		}

		if ($options['date'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['date']) . ' AS `created`';
		}

		if ($options['authorid'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['authorid']) . ' AS `created_by`';
		}

		if ($options['name'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['name']) . ' AS `name`';
		}

		if ($options['email'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['email']) . ' AS `email`';
		}

		if (isset($options['homepage']) && $options['homepage'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['homepage']) . ' AS `url`';
		}

		if ($options['published'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['published']) . ' AS `published`';
		}

		if ($options['ip'] != 'notavailable') {
			$columns[] = $this->db->nameQuote($options['ip']) . ' AS `ip`';
		}

		$query .= implode(',', $columns);
		$query .= ' FROM ' . $this->db->nameQuote($options['table']);

		if ($options['date'] != 'notavailable') {
			$query .= ' ORDER BY ' . $this->db->nameQuote($options['date']);
		}

		if (!isset($options['start'])) {
			$options['start'] = 0;
		}

		if ($options['cycle'] != 0) {
			$query .= ' LIMIT ' . $options['start'] . ', ' . $options['cycle'];
		}

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();
		return $result;
	}

	public function getStatistic($options)
	{
		$totalComments = $this->getCount($options);

		return $this->ajax->resolve($totalComments);
	}

	public function getCount($options)
	{
		$query = 'SELECT COUNT(1) FROM ' . $this->db->nameQuote($options['table']);

		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
}
