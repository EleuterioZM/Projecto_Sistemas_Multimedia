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

class KomentoViewThemes extends KomentoAdminView
{
	/**
	 * Renders the editor to allow user to edit the theme file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.themes');

		$this->heading('COM_KOMENTO_TOOLBAR_TITLE_THEMES_EDITOR');

		$element = 'wireframe';
		$id = $this->input->get('id', '', 'default');

		// Get a list of theme files from this template file
		$model = KT::model('Themes');
		$files = $model->getFiles('wireframe');

		$item = null;

		if ($id) {
			$item = $model->getFile($id, $element, true);

			JToolBarHelper::apply('saveFile');

			$item->notes = "";

			if ($item->modified) {
				JToolBarHelper::trash('revert', JText::_('COM_KOMENTO_REVERT_CHANGES'), false);

				// attach file override's notes.
				$item->notes = $model->getFileNotes($id);
			}
		}

		// Use codemirror editor
		$editor = FH::getEditor('codemirror');

		$this->set('id', $id);
		$this->set('editor', $editor);
		$this->set('item', $item);
		$this->set('element', $element);
		$this->set('files', $files);

		parent::display('themes/default/default');
	}

	/**
	 * Allows site admin to insert custom css codes
	 *
	 * @since   3.0.13
	 * @access  public
	 */
	public function custom()
	{
		$this->heading('COM_KT_TITLE_THEMES_CUSTOM_CSS');

		$editor = FH::getEditor('codemirror');

		JToolbarHelper::apply('saveCustomCss');
		JToolbarHelper::cancel();
		
		$model = KT::model('Themes');
		$path = $model->getCustomCssTemplatePath();
		$contents = '';

		if (JFile::exists($path)) {
			$contents = file_get_contents($path);
		}

		$this->set('contents', $contents);
		$this->set('editor', $editor);

		parent::display('themes/custom/default');
	}
}