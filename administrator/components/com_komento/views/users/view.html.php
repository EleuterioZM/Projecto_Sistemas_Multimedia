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

class KomentoViewUsers extends KomentoAdminView
{
	/**
	 * Renders the users listing view
	 *
	 * @since	3.1.4
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.users');

		$this->heading('COM_KT_HEADING_USERS', 'COM_KT_DESCRIPTION_USERS');

		JToolbarHelper::custom('requestArchiveUserData', 'default', '', JText::_('COM_KT_TOOLBAR_TITLE_BUTTON_REQUEST_DATA'));

		// Get the current search query
		$search = $this->app->getUserStateFromRequest('com_komento.users.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering options
		$order = $this->app->getUserStateFromRequest('com_komento.users.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_komento.users.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		$model = KT::model('Users');
		$users = $model->getUsers();

		$pagination = $model->getPagination();

		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertUser', 'string');

		$this->set('browse', $browse);
		$this->set('browsefunction', $browsefunction);
		$this->set('search', $search);
		$this->set('users', $users);
		$this->set('pagination', $pagination);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('users/default');
	}
}
