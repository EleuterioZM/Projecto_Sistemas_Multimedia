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

class KomentoViewDashboard extends KomentoView
{
	/**
	 * Renders the dashboard layout for admins
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		$userLib = KT::user();
		$userId = $userLib->id;

		// Do not allow guest user to access this page
		if (!$userId) {

			$returnURL = JURI::root();

			// since Google webmaster tool accept these redirection instead of just display 500 error when the guest user access. #226
			$this->app->enqueueMessage(JText::_('COM_KOMENTO_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'), 'error');
			return $this->app->redirect($returnURL);
		}

		$model = KT::model('Comments');

		$filter = $this->input->get('filter', 'all', 'word');
		$limitstart = $this->input->get('limitstart', 0, 'int');
		$sort = $this->config->get('default_sort', 'latest');
		$published = 'all';

		if ($filter === 'pending') {
			$published = KOMENTO_COMMENT_MODERATE;
		}

		if ($filter === 'spam') {
			$published = KOMENTO_COMMENT_SPAM;
		}

		$options = [
			'limit'	=> 5,
			'published'	=> $published,
			'threaded' => 0,
			'sort' => $sort,
			'limitstart' => $limitstart
		];

		$isModerator = $userLib->isModerator();

		if (!$isModerator) {
			$options['userid'] = $userId;
		}

		if ($filter == 'reports') {
			$reportsModel = KT::model('reports', ['initState' => true]);
			$comments = $reportsModel->getItems();
			$pagination = $reportsModel->getPagination();
		} else {
			$comments = $model->getComments('all', '', $options);
			$pagination = $model->getPagination();
		}

		$comments = KT::formatter('comment', $comments);

		// Get total number of pending comments
		$totalPending = $model->getTotalPending();

		// Get total number of spams
		$totalSpams = $model->getTotalSpams();

		// Get total reported comments
		$totalReports = $model->getTotalReported();

		$components = $model->getUniqueComponents();

		// Determine if the current logged in user whether have the permission to delete comment
		$canDeleteComment = $userLib->canDeleteComment();

		// determine whether it should appear that action bar on the user dashboard
		$showActionBar = $isModerator || $canDeleteComment ? true : false;

		$returnURL = base64_encode(JRoute::_('index.php?option=com_komento&view=dashboard&filter=' . $filter, false));

		$this->set('totalReports', $totalReports);
		$this->set('totalSpams', $totalSpams);
		$this->set('totalPending', $totalPending);
		$this->set('components', $components);
		$this->set('pagination', $pagination);
		$this->set('comments', $comments);
		$this->set('filter', $filter);
		$this->set('isModerator', $isModerator);
		$this->set('canDeleteComment', $canDeleteComment);
		$this->set('showActionBar', $showActionBar);
		$this->set('returnURL', $returnURL);

		parent::display('dashboard/default');
	}


	

	/**
	 * GDPR download
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function download()
	{
		// Do not allow guest user to access this page
		if (!$this->my->id || !$this->config->get('enable_gdpr_download', false)) {

			$returnURL = JURI::root();

			// since Google webmaster tool accept these redirection instead of just display 500 error when the guest user access. #226
			$this->app->enqueueMessage(JText::_('COM_KOMENTO_NOT_ALLOWED_ACCESS_IN_THIS_SECTION'), 'error');
			return $this->app->redirect($returnURL);
		}

		$doc = JFactory::getDocument();
		$doc->setTitle(JText::_('COM_KT_PAGE_TITLE_DOWNLOAD_DATA'));

		$download = KT::table('Download');
		$download->load(array('userid' => $this->my->id));

		$this->set('download', $download);

		return parent::display('dashboard/download/default');
	}

	/**
	 * Download the file
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function downloaddata()
	{
		if (!$this->config->get('enable_gdpr_download')) {
			throw FH::exception('COM_KT_GDPR_DOWNLOAD_DISABLED', 404);
		}

		if (!$this->my->id) {
			throw FH::exception('COM_KT_PLEASE_LOGIN_INFO', 500);
		}

		$download = KT::table('Download');
		$exists = $download->load(array('userid' => $this->my->id));

		if (!$exists || !$download->isReady()) {
			throw FH::exception('COM_KT_GDPR_DOWNLOAD_INVALID_ID', 404);
		}

		return $download->showArchiveDownload();
	}
}
