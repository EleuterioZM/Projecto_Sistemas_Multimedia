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

KT::import('admin:/views/views');

class KomentoViewLanguages extends KomentoAdminView
{
	/**
	 * Renders the language listings
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.languages');

		$this->heading('COM_KOMENTO_HEADING_LANGUAGES', 'COM_KOMENTO_DESCRIPTION_LANGUAGES');

		// Check if there's any data on the server
		$model = KT::model('Languages');
		$model->initStates();

		$initialized = $model->initialized();

		if (!$initialized) {
			return parent::display('languages/initialize/default');
		}

		// Add Joomla buttons
		JToolbarHelper::custom('discover', 'refresh', '', JText::_('COM_KOMENTO_TOOLBAR_BUTTON_FIND_UPDATES'), false);
		JToolbarHelper::custom('purge', 'trash', '', JText::_('COM_KOMENTO_TOOLBAR_BUTTON_PURGE_CACHE'), false);
		JToolbarHelper::divider();
		JToolbarHelper::custom('install', 'upload' , '' , JText::_('COM_KOMENTO_TOOLBAR_BUTTON_INSTALL_OR_UPDATE'));
		JToolbarHelper::custom('uninstall', 'remove', '', JText::_('COM_KOMENTO_TOOLBAR_BUTTON_UNINSTALL'));

		// Get filter states.
		$ordering = $this->input->get('ordering', $model->getState('ordering'), 'cmd');
		$direction = $this->input->get('direction', $model->getState('direction'), 'cmd');
		$limit = $model->getState('limit');
		$published = $model->getState('published');

		// Get the list of languages now
		$languages = $model->getLanguages();

		foreach ($languages as &$language) {
			$translators = json_decode($language->translator);
			$language->translator = $translators;
		}

		$pagination	= $model->getPagination();

		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('languages', $languages);
		$this->set('pagination', $pagination);

		parent::display('languages/default/default');
	}

	/**
	 * Discover languages from our server
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function discover()
	{
		$this->heading('COM_KOMENTO_HEADING_LANGUAGES', 'COM_KOMENTO_DESCRIPTION_LANGUAGES');

		return parent::display('languages/initialize/default');
	}
}
