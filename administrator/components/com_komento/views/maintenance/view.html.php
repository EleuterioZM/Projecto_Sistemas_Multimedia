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

class KomentoViewMaintenance extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('komento.manage.maintenance');

		$this->heading('COM_KOMENTO_HEADING_MAINTENANCE_DEFAULT');

		if ($this->input->get('success', 0, 'int')) {
			KT::setMessage(JText::_('COM_KOMENTO_MAINTENANCE_SUCCESSFULLY_EXECUTED_SCRIPT'), 'success');
		}

		JToolbarHelper::custom('form', 'refresh', '', JText::_('COM_KOMENTO_MAINTENANCE_EXECUTE_SCRIPTS'));

		// filters
		$selectedVersion = $this->app->getUserStateFromRequest('com_komento.maintenance.filter_version', 'filter_version', 'all', 'cmd');
		$order = $this->app->getUserStateFromRequest('com_komento.maintenance.filter_order', 'filter_order', 'version', 'cmd');
		$orderDirection	= $this->app->getUserStateFromRequest('com_komento.maintenance.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$versions = [];

		$model = KT::model('Maintenance');
		$model->setState('version', $selectedVersion);
		$model->setState('ordering', $order);
		$model->setState('direction', $orderDirection);

		$scripts = $model->getItems();
		$pagination = $model->getPagination();
		$currentVersions = $model->getVersions();
		$versions = [];

		foreach ($currentVersions as $version) {
			$versions[$version] = $version;
		}

		$limit = $model->getState('limit');

		$this->set('limit', $limit);
		$this->set('version', $selectedVersion);
		$this->set('scripts', $scripts);
		$this->set('versions', $versions);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('pagination', $pagination);

		parent::display('maintenance/default');
	}

	/**
	 * Displays maintenance scripts execution page
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function form()
	{
		$this->checkAccess('komento.manage.maintenance');

		$this->heading('COM_KOMENTO_HEADING_MAINTENANCE_DEFAULT');

		$cids = $this->input->get('cid', array(), 'var');

		$scripts = KT::model('Maintenance')->getItemByKeys($cids);

		$this->set('scripts', $scripts);

		parent::display('maintenance/form');
	}

	/**
	 * Displays maintenance database page
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function database($tpl = null)
	{
		// Check for access
		$this->checkAccess('komento.manage.maintenance');

		$this->heading('COM_KOMENTO_HEADING_MAINTENANCE_DATABASE');

		parent::display('maintenance/database');
	}
}
