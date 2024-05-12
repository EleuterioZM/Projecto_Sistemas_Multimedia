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

class KomentoControllerLanguages extends KomentoController
{
	/**
	 * Purges the cache of language items
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function purge()
	{
		// Check for request forgeries here
		FH::checkToken();

		// Get the current view
		$view = $this->getCurrentView();

		$model = KT::model('Languages');
		$model->purge();

		$this->info->set(JText::_('COM_KOMENTO_LANGUAGES_PURGED_SUCCESSFULLY') , KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=languages');
	}

	/**
	 * Allows caller to remove languages
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function uninstall()
	{
		// Check for request forgeries here
		FH::checkToken();

		// Get the list of items to be deleted
		$ids = $this->input->get('cid', [], 'array');

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = KT::table('Language');
			$table->load($id);

			if (!$table->isInstalled()) {
				$table->delete();
				continue;
			}

			$table->uninstall();
			$table->delete();
		}

		$this->info->set('COM_KOMENTO_LANGUAGES_UNINSTALLED_SUCCESS', KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=languages');
	}

	/**
	 * Installs a language file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function install()
	{
		// Check for request forgeries here
		FH::checkToken();

		// Get the language id's to install
		$ids = $this->input->get('cid', [], 'array');

		if (!$ids) {
			$this->info->set(JText::_('COM_KOMENTO_LANGUAGES_INVALID_ID_PROVIDED'), KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=languages');
		}

		foreach ($ids as $id) {
			$table = KT::table('Language');
			$table->load($id);

			$table->install();
		}

		$this->info->set(JText::_('COM_KOMENTO_LANGUAGES_INSTALLED_SUCCESSFULLY') , KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=languages');
	}

	/**
	 * Updates the site with the latest language files
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function update()
	{
		// Check for request forgeries here
		FH::checkToken();

		$languages = $this->input->get('languages', [], 'array');

		// Go through each of the languages now
		foreach ($languages as $language) {

			$language = (object) $language;

			// Check if the language was previously installed thorugh our system.
			// If it does, load it instead of overwriting it.
			$table = KT::table('Language');
			$exists = $table->load(['locale' => $language->locale]);

			// We do not want to bind the id
			unset($language->id);

			// Since this is the retrieval, the state should always be disabled
			if (!$exists) {
				$table->state = KOMENTO_STATE_UNPUBLISHED;
			}

			// If the language file has been installed, we want to check the last updated time
			if ($exists && $table->state == KOMENTO_LANGUAGES_INSTALLED) {

				// Then check if the language needs to be updated. If it does, update the ->state to KOMENTO_LANGUAGES_NEEDS_UPDATING
				// We need to check if the language updated time is greater than the local updated time
				$languageTime = strtotime($language->updated);
				$localLanguageTime = strtotime($table->updated);

				if ($languageTime > $localLanguageTime && $table->state == KOMENTO_LANGUAGES_INSTALLED) {
					$table->state	= KOMENTO_LANGUAGES_NEEDS_UPDATING;
				}
			}

			// Set the title
			$table->title = $language->title;
			$table->locale = $language->locale;
			$table->translator = $language->translator;
			$table->updated = $language->updated;
			$table->progress = $language->progress;

			// Update the table with the appropriate params
			$params = new JRegistry();

			$params->set('download', $language->download);
			$params->set('md5', $language->md5);
			$table->params = $params->toString();

			$table->store();
		}
		$view = $this->getCurrentView();

		return $view->call(__FUNCTION__, $languages);
	}
}
