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

KT::import('admin:/views/views');

class KomentoViewDownloads extends KomentoAdminView
{
	/**
	 * Renders the download request view
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.download');

		// Set page heading
		$this->heading('COM_KT_HEADING_DOWNLOAD_REQUEST', 'COM_KT_DESCRIPTION_DOWNLOAD_REQUEST');

		JToolbarHelper::deleteList('', 'removeRequest');
		JToolBarHelper::custom('purgeAll','purgeAll','icon-32-unpublish.png', 'COM_KT_TOOLBAR_TITLE_BUTTON_PURGE_ALL', false);

		$model = KT::model('Download');
		$requests = $model->getRequests();
		$pagination = $model->getPagination();

		$this->set('requests', $requests);
		$this->set('pagination', $pagination);

		parent::display('downloads/default');
	}

	/**
	 * Allows viewer to download data from backend
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function downloaddata()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = KT::table('Download');
		$table->load($id);

		return $table->showArchiveDownload();
	}
}
