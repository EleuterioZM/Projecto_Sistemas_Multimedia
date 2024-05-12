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
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('komento.manage.migrators');

		$this->heading('COM_KOMENTO_HEADING_MIGRATORS');

		$layout = $this->getLayout();

		if ($layout === 'default') {
			$layout = 'custom';
		}

		$contents = $this->getContent($layout);

		$this->set('layout', $layout);
		$this->set('contents', $contents);

		parent::display('migrators/default');
	}

	/**
	 * Retrieve the contents of the migrator
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getContent($layout)
	{
		$this->heading('COM_KOMENTO_MIGRATORS_' . strtoupper($layout));

		$theme = KT::themes();
		$categories = '';

		// Some layouts, we don't need categories selection
		$excludeLayouts = [
			'jcomments', 
			'jacomment', 
			'rscomments',
			'jlexcomment'
		];
				
		$lib = KT::components();
		$installed = $lib->isInstalled('com_' . $layout);

		if (!$installed && $layout !== 'custom') {
			return $theme->output('admin/migrators/unavailable');
		}

		if ($layout !== 'custom' && !in_array($layout, $excludeLayouts)) {

			$appName = 'com_' . $layout;

			// Slicomment has a com_content categories selection
			if ($layout === 'slicomments') {
				$appName = 'com_content';
			}

			$componentObj = KT::loadApplication($appName);

			$categories = $componentObj->getCategories();
			
			if ($categories) {
				$categories = $this->getCategoriesDropdown($categories);
			}
		}
// dump($categories);
		// prepare table selection for custom migrator
		$tables = KT::db()->getTables();

		$tableOptions = [];

		foreach ($tables as $table) {
			$tableOptions[$table] = $table;
		}

		$components = KT::components()->getAvailableComponents();
		$componentOptions = [];

		foreach ($components as $component) {
			$componentOptions[$component] = $component;
		}
			
		$theme->set('layout', $layout);
		$theme->set('tableOptions', $tableOptions);
		$theme->set('componentOptions', $componentOptions);
		$theme->set('categories', $categories);
		$output = $theme->output('admin/migrators/adapters/' . $layout);

		return $output;
	}

	/**
	 * Renders categories for migrators
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCategoriesDropdown($categories)
	{
		$options = [
			'all' => 'COM_KOMENTO_MIGRATORS_CATEGORIES_DROPDOWN_ALL'
		];

		foreach ($categories as $category) {
			$categoryTitle = FH::normalize($category, 'name', FH::normalize($category, 'title', ''));
			$options[$category->id] = $categoryTitle;
		}
		
		return $options;
	}
}
