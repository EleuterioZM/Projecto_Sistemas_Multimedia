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

class KomentoControllerThemes extends KomentoController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'store');
		$this->registerTask('save', 'store');
	}

	/**
	 * Saves the contents of a theme file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function saveFile()
	{
		$this->checkAccess('komento.manage.themes');

		FH::checkToken();

		$id = $this->input->get('id', '', 'default');
		$contents = $this->input->get('contents', '', 'raw');
		$element = 'wireframe';

		$model = KT::model('Themes');
		$file = $model->getFile($id, $element);

		// Save the file now
		$state = $model->write($file, $contents);

		if (!$state) {
			$this->info->set(JText::sprintf('COM_KOMENTO_THEMES_SAVE_ERROR', $file->override), KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=themes&element=' . $element . '&id=' . $id);
		}

		// Document the changes
		// add file override notes.
		$model->addFileNotes($file, $this->input->get('notes', '', 'default'));

		$this->info->set(JText::sprintf('COM_KOMENTO_THEMES_SAVE_SUCCESS', $file->override), KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=themes&element=' . $element . '&id=' . $id);
	}

	/**
	 * Allows caller to revert a theme file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function revert()
	{
		$this->checkAccess('komento.manage.themes');
		
		FH::checkToken();

		$element = 'wireframe';
		$id = $this->input->get('id', '', 'default');
		$contents = $this->input->get('contents', '', 'raw');
		
		$model = KT::model('Themes');
		$file = $model->getFile($id, $element);

		// Save the file now
		$state = $model->revert($file);

		if (!$state) {
			$this->info->set(JText::sprintf('COM_KOMENTO_THEMES_DELETE_ERROR', $file->override), KOMENTO_MSG_ERROR);
			$this->app->redirect('index.php?option=com_komento&view=themes&element=' . $element . '&id=' . $id);
		}

		// remove file override notes.
		$model->removeFileNotes($file);

		$this->info->set(JText::sprintf('COM_KOMENTO_THEMES_DELETE_SUCCESS', $file->override), KOMENTO_MSG_SUCCESS);
		$this->app->redirect('index.php?option=com_komento&view=themes&element=' . $element . '&id=' . $id);
	}

	/**
	 * Save custom.css
	 *
	 * @since   3.0.13
	 * @access  public
	 */
	public function saveCustomCss()
	{
		FH::checkToken();
		
		$model = KT::model('themes');
		$path = $model->getCustomCssTemplatePath();

		$contents = $this->input->get('contents', '', 'raw');

		JFile::write($path, $contents);

		$this->info->set(JText::sprintf('COM_KT_THEMES_CUSTOM_CSS_SAVE_SUCCESS', $path), KOMENTO_MSG_SUCCESS);

		$redirect = 'index.php?option=com_komento&view=themes&view=themes&layout=custom';

		return $this->app->redirect($redirect);
	}	
}