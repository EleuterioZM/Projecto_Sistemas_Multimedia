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

class KomentoControllerSettings extends KomentoController
{
	/**
	 * Saves the settings
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function apply()
	{
		FH::checkToken();

		$layout = $this->input->get('current', '', 'word');
		$active = strtolower($this->input->get('tab', '', 'word'));

		// Get the settings model
		$model = KT::model('Settings');

		// Get the post data from the form
		$post = $this->input->post->getArray();

		$this->cleanup($post);

		foreach ($post as $index => &$value) {
			if (is_array($value)) {
				$value = array_filter($value);
			}
		}

		// Save custom logo for emails
		if (isset($post['custom_email_logo']) && $post['custom_email_logo']) {

			// Get logo
			$file = $this->input->files->get('email_logo', '');

			// Store logo
			if (!empty($file['tmp_name'])) {
				$model->updateEmailLogo($file);
			}
		}

		if (KT::isFreeVersion()) {
			// get paid feature settings
			$paidFeatures = KT::getPaidSettings();

			foreach ($paidFeatures as $key) {
				unset($post[$key]);
			}
		}

		$state = $model->save($post);

		$message = $state ? JText::_('COM_KOMENTO_SETTINGS_STORE_SUCCESS') : JText::_('COM_KOMENTO_SETTINGS_STORE_ERROR');
		$type = $state ? KOMENTO_MSG_SUCCESS : KOMENTO_MSG_ERROR;

		// Set info
		$this->info->set($message, $type);

		// Clear the component's cache
		$cache = JFactory::getCache('com_komento');
		$cache->clean();

		$redirect = 'index.php?option=com_komento&view=settings';

		if ($layout) {
			$redirect .= '&layout=' . $layout;
		}

		if ($active) {
			$redirect .= '&tab=' . $active;
		}

		$this->app->redirect($redirect);
	}

	/**
	 * Clean up the post data
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function cleanup(&$post)
	{
		unset($post['active']);
		unset($post['activechild']);
		unset($post['current']);
		unset($post['controller']);
		unset($post['option']);
		unset($post['task']);
		unset($post['component']);

		$token = FH::token();
		unset($post[$token]);
	}

	public function cancel()
	{
		$this->app->redirect('index.php?option=com_komento');
	}

	public function save()
	{
		$this->apply();
	}

	/**
	 * Allows user to import settings file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function import()
	{
		FH::checkToken();

		// Get the file data
		$file = $this->input->files->get('file');


		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			$this->info->set('Please specify a valid JSON export file', 'error');
			return $this->app->redirect('index.php?option=com_komento&view=settings');
		}

		// Get the path to the temporary file
		$path = $file['tmp_name'];
		$contents = file_get_contents($path);

		$params = json_decode($contents);

		if (!$params) {
			$this->info->set('The JSON file is corrupted or does not have the appropriate format', 'error');
			return $this->app->redirect('index.php?option=com_komento&view=settings');			
		}

		// Load the configuration
		$table = KT::table('Configs');
		$table->load([
			'name' => 'config'
		]);

		$table->params = $contents;

		$table->store();

		$this->info->set('COM_KT_SETTINGS_IMPORT_SUCCESS', 'success');
		return $this->app->redirect('index.php?option=com_komento&view=settings');
	}

	/**
	 * Delete email logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function restoreEmailLogo()
	{
		$notification = KT::notification();
		$notification->restoreEmailLogo();

		return KT::ajax()->resolve();
	}
}
