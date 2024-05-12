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

class KomentoViewMigrators extends KomentoAdminView
{
	public function migrateComments()
	{
		$publishState = $this->input->get('publishState', '');
		$component = $this->input->get('component', '', 'string');
		$getPosts = $this->input->get('getPosts', false, 'boolean');

		if (!$component) {
			die('Invalid migration');
		}

		if ($publishState === 'unpublished') {
			$publishState = 0;
		}

		if ($publishState === 'published') {
			$publishState = 1;
		}

		if ($component === 'com_easyblog') {	
			$categoryId = $this->input->get('categoryId');
			$migrateLikes = $this->input->get('migrateLikes', '');

			$migrator = KT::migrator()->getAdapter('easyblog');

			$migrator->migrate($categoryId, $publishState, $migrateLikes);
		}

		if ($component === 'com_k2') {
			$categoryId = $this->input->get('categoryId');

			$migrator = KT::migrator()->getAdapter('k2');

			$migrator->migrate($categoryId, $publishState);			
		}

		if ($component === 'com_zoo') {
			$categoryId = $this->input->get('categoryId');
			$itemId = $this->input->get('itemId', '');

			$migrator = KT::migrator()->getAdapter('zoo');

			if ($getPosts) {
				$migrator->getPosts($categoryId);
			} else {
				$migrator->migrate($itemId, $publishState);
			}
		}

		if ($component === 'custom') {
			$data = $this->input->get('data', [], 'array');
			$task = $this->input->get('task', 'migrate');

			$migrator = KT::migrator()->getAdapter('custom');
			$migrator->$task($data);			
		}

		if ($component === 'jacomment') {
			$selectedComponent = $this->input->get('selectedComponent');

			$migrator = KT::migrator()->getAdapter('jacomment');
			$migrator->migrate($publishState, $selectedComponent);
		}

		if ($component === 'jcomments') {
			$selectedComponent = $this->input->get('selectedComponent');
			$migrateLikes = $this->input->get('migrateLikes', '');

			$migrator = KT::migrator()->getAdapter('jcomments');

			$migrator->migrate($publishState, $migrateLikes, $selectedComponent);
		}

		if ($component === 'rscomments') {
			$selectedComponent = $this->input->get('selectedComponent');
			$itemId = $this->input->get('itemId', '');

			$migrator = KT::migrator()->getAdapter('rscomments');

			if ($getPosts) {
				$migrator->getPosts($selectedComponent);
			} else {
				$migrator->migrate($itemId, $publishState, $selectedComponent);
			}
		}

		if ($component === 'slicomments') {
			$categoryId = $this->input->get('categoryId');
			$itemId = $this->input->get('itemId', '');

			$migrator = KT::migrator()->getAdapter('slicomments');

			if ($getPosts) {
				$migrator->getPosts($categoryId);
			} else {
				$migrator->migrate($itemId, $publishState);
			}
		}

		if ($component === 'jlexcomment') {
			$migrateLikes = $this->input->get('migrateLikes', '');

			$migrator = KT::migrator()->getAdapter('jlexcomment');
			$migrator->migrate($publishState, $migrateLikes);
		}
	}

	public function getColumns()
	{
		$tableName = $this->input->get('tableName');

		$columns = KT::db()->getColumns($tableName);

		$html = '';

		foreach ($columns as $column) {
			$html .= '<option value="' . $column . '">' . $column . '</option>';
		}

		return $this->ajax->resolve($html);
	}
}

