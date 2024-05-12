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

class KomentoViewSettings extends KomentoAdminView
{
	static $extension;

	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.settings');

		$layout = $this->getLayout() == 'default' ? 'general' : $this->getLayout();

		$this->heading('COM_KOMENTO_SETTINGS_HEADING_' . strtoupper($layout));
		
		JToolBarHelper::title(JText::_('COM_KOMENTO_CONFIGURATION'));
		JToolBarHelper::apply();

		// Get active tab
		$active = $this->input->get('tab', '', 'word');
		$tabs = $this->getTabs($layout, $active);
		$goto = $this->input->get('goto', '', 'cmd');

		$this->set('goto', $goto);
		$this->set('tabs', $tabs);
		$this->set('active', $active);
		$this->set('layout', $layout);

		parent::display('settings/default');
	}

	/**
	 * Retrieves a list of available tabs for a particular settings
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTabs($layout, $active)
	{
		$tabs = [];
		$i = 0;
		$hasActive = false;

		$path = JPATH_ADMINISTRATOR . '/components/com_komento/themes/default/settings/' . $layout;

		if (!JFolder::exists($path)) {
			return $tabs;
		}

		$files = JFolder::files($path, '.php', false);

		foreach ($files as $file) {
			if ($file == 'default.php' || $file == 'component.php') {
				continue;
			}

			$name = str_ireplace('.php', '', $file);

			$obj = new stdClass();
			$obj->id = $name;
			$obj->title = JText::_('COM_KOMENTO_SETTINGS_TAB_' . strtoupper($name));
			$obj->active = ($name == 'general' && !$active) || $active === $obj->id;
			$obj->namespace = 'admin/settings/' . $layout . '/' . $name;

			$tabs[$name] = $obj;

			$i++;
		}

		// Here we check for specific component integration
		if ($layout == 'integrations') {
			
			// Get available components
			$components = KT::components()->getAvailableComponents();

			foreach ($components as $component) {
				$componentObj = KT::loadApplication($component);

				$name = $componentObj->component . '_settings';
				$categories = $componentObj->getCategories();

				// Get extra integration settings (if any)
				$componentSettings = $componentObj->getComponentSettings();

				if (empty($categories) && empty($componentSettings)) {
					continue;
				}
						
				$obj = new stdClass();
				$obj->id = $name;
				$obj->title = $componentObj->getComponentName();
				$obj->active = ($name == 'general' && !$active) || $active === $obj->id;
				$obj->namespace = 'admin/settings/' . $layout . '/component';
				$obj->categories = $categories;
				$obj->componentSettings = $componentSettings;

				$tabs[$name] = $obj;

				$i++;
			}
		}

		// Sort items manually. Always place "General" as the first item
		if (isset($tabs['general'])) {
		
			$general = $tabs['general'];

			unset($tabs['general']);

			array_unshift($tabs, $general);
		} else {
			// First tab should always be highlighted
			$firstIndex = array_keys($tabs);
			$firstIndex = $firstIndex[0];

			if ($active) {
				$tabs[$firstIndex]->active = $active === $tabs[$firstIndex]->id;
			} else {
				$tabs[$firstIndex]->active = true;
			}
		}

		return $tabs;
	}

	/**
	 * Rebuilds the search database
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function rebuildSearch()
	{
		$this->heading('Rebuild Search', 'Utility to rebuild the search database');

		$file = KT_DEFAULTS . '/menus.json';
		$contents = file_get_contents($file);

		$menus = json_decode($contents);

		$items = [];

		foreach ($menus as $menu) {
			if (!isset($menu->view) || $menu->view != 'settings') {
				continue;
			}

			foreach ($menu->childs as $child) {
				if ($child->url->view !== 'settings') {
					continue;
				}

				$items[] = $child->url->layout;
			}
		}

		$this->set('items', $items);

		return parent::display('settings/search.rebuild');
	}
}