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

class KomentoViewMailq extends KomentoAdminView
{
	/**
	 * Renders the mailer queue from the system
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.mailq');

		$this->heading('COM_KOMENTO_HEADING_EMAIL_ACTIVITIES', 'COM_KOMENTO_DESCRIPTION_EMAIL_ACTIVITIES');

		JToolbarHelper::publishList('publish', JText::_('COM_KOMENTO_TOOLBAR_TITLE_BUTTON_MARK_SENT'));
		JToolbarHelper::unpublishList('unpublish', JText::_('COM_KOMENTO_TOOLBAR_TITLE_BUTTON_MARK_PENDING'));
		JToolbarHelper::trash('purgeSent', JText::_('COM_KOMENTO_TOOLBAR_TITLE_BUTTON_PURGE_SENT'), false);
		JToolbarHelper::trash('purgePending', JText::_('COM_KOMENTO_TOOLBAR_TITLE_BUTTON_PURGE_PENDING'), false);
		JToolbarHelper::trash('purgeAll', JText::_('COM_KOMENTO_TOOLBAR_TITLE_BUTTON_PURGE_ALL'), false);

		// Get the model
		$model = KT::model('Mailq', ['initState' => true]);

		$emails = $model->getItemsWithState();
		$pagination = $model->getPagination();
		$published = $model->getState('filter_publish');
		$search = $model->getState('search');
		$limit = $model->getState('limit');
		$ordering = $model->getState('ordering');
		$direction = $model->getState('direction');

		// Determine the last execution time of the cronjob if there is
		$cronLastExecuted = $this->config->get('cron_last_execute', '');

		if ($cronLastExecuted) {
			$cronLastExecuted = JFactory::getDate($cronLastExecuted)->format(JText::_('DATE_FORMAT_LC2'));
		}

		$this->set('cronLastExecuted', $cronLastExecuted);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);
		$this->set('search', $search);
		$this->set('limit', $limit);
		$this->set('published', $published);
		$this->set('emails', $emails);
		$this->set('pagination', $pagination);

		parent::display('mailq/default');
	}

	/**
	 * Previews an email
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		$mailer = KT::table('Mailq');
		$mailer->load($id);

		// Load front end language file
		FH::loadLanguage('com_komento');

		if ($mailer->template && !$mailer->body) {
			$mailer->body = $mailer->processTemplateContent();
		}

		echo $mailer->body;
		exit;
	}

	/**
	 * Renders the list of e-mail templates
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function editor()
	{
		$this->checkAccess('komento.manage.emails');

		$this->heading('COM_KOMENTO_HEADING_EMAIL_TEMPLATES');

		JToolbarHelper::deleteList('', 'reset', JText::_('COM_KOMENTO_EMAILS_RESET_DEFAULT'));

		$currentFilter = $this->app->getUserStateFromRequest('com_komento.spools.editor.filter_state', 'filter_editor_state', '', 'word');

		$model = KT::model('Emails');
		$files = $model->getFiles(['filter' => $currentFilter]);

		$this->set('files', $files);
		$this->set('currentFilter', $currentFilter);

		return parent::display('mailq/editor/default');
	}

	/**
	 * Renders the editor for email template
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function editFile()
	{
		$this->checkAccess('komento.manage.emails');

		JToolBarHelper::title(JText::_('COM_KOMENTO_EMAILS_EDITING_TITLE'), 'emails');

		JToolBarHelper::apply('saveFile');
		JToolBarHelper::cancel();

		$this->hideSidebar();

		$this->heading('COM_KOMENTO_EMAILS_EDITING_TITLE', 'COM_KOMENTO_EMAILS_EDITING_TITLE_DESC');

		$base = $this->input->get('base', 0, 'int');
		$file = $this->input->get('file', '', 'default');
		$file = urldecode($file);

		$model = KT::model("Emails");
		$absolutePath = $model->getFolder($base) . $file;

		$file = $model->getFileObject($absolutePath, true, $base);

		// Always use codemirror
		$editor = FH::getEditor('codemirror');

		$this->set('editor', $editor);
		$this->set('file', $file);

		return parent::display('mailq/editfile/default');
	}


	/**
	 * Renders the template preview
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function templatePreview()
	{
		$file = $this->input->get('file', '', 'default');
		$file = str_ireplace('.php', '', ltrim(urldecode($file), '/'));

		$namespace = 'site/emails/' . $file;

		$notification = KT::notification();
		$output = $notification->getTemplateContents($namespace, [], [], false, true);

		echo $output;exit;
	}
}
