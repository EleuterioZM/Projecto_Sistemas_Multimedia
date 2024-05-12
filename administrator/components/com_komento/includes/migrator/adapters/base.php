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

class KomentoMigratorBase
{
	public function __construct()
	{
		$this->db = KT::db();
		$this->ajax = KT::ajax();
	}

	public function addRecord($component, $internalId, $externalId)
	{
		$migrator = KT::table('Migrators');
		$migrator->set('component', $component);
		$migrator->set('internal_id', $internalId);
		$migrator->set('external_id', $externalId);

		return $migrator->store();
	}

	public function pushKomentoComment($base, $diff, $component, $cid)
	{
		$query  = 'UPDATE `#__komento_comments`';
		$query .= ' SET `lft` = `lft` + ' . $diff . ', `rgt` = `rgt` + ' . $diff;
		$query .= ' WHERE `component` = ' . $this->db->quote($component);
		$query .= ' AND `cid` = ' . $this->db->quote($cid);
		$query .= ' AND `lft` >= ' . $base;

		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function getKomentoInsertNode($date, $component, $cid)
	{
		$query  = 'SELECT * FROM `#__komento_comments`';
		$query .= ' WHERE `component` = ' . $this->db->quote($component);
		$query .= ' AND `cid` = ' . $this->db->quote($cid);
		$query .= ' AND `created` > ' . $this->db->quote($date);
		$query .= ' AND `parent_id` = ' . $this->db->quote(0);
		$query .= ' ORDER BY `created` LIMIT 1';

		$this->db->setQuery($query);

		return $this->db->loadObject();
	}

	public function populateChildBoundaries($child, $parent_id, $component, $cid)
	{
		$model = KT::model('comments');

		$latest = $model->getLatestComment($component, $cid, $parent_id);

		$parent = KT::getTable('comments');
		$parent->load($parent_id);

		//adding new child comment
		$lft = $parent->lft + 1;
		$rgt = $parent->lft + 2;
		$node = $parent->lft;

		if (!empty($latest)) {
			$lft = $latest->rgt + 1;
			$rgt = $latest->rgt + 2;
			$node = $latest->rgt;
		}

		$model->updateCommentSibling($component, $cid, $node);

		$child->lft = $lft;
		$child->rgt = $rgt;

		return $child;
	}

	public function getSupportedComponents()
	{
		static $supportedComponents = [];

		if (empty($supportedComponents)) {
			$components = array_values(KT::components()->getAvailableComponents());

			foreach ($components as $component) {
				$supportedComponents[] = $this->db->quote($component);
			}
		}

		return $supportedComponents;
	}

}
