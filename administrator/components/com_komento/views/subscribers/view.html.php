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

class KomentoViewSubscribers extends KomentoAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('komento.manage.subscribers');

		$this->heading('COM_KOMENTO_SETTINGS_HEADING_SUBSCRIBERS');

		$selectedExtension = $this->app->getUserStateFromRequest('com_komento.subscribers.filter_component', 'filter_component', '*', 'string');
		$order = $this->app->getUserStateFromRequest('com_komento.subscribers.filter_order', 'filter_order', 'created', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_komento.subscribers.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

		// Get data from the model
		$subscriptionModel = KT::model('subscription');
		$subscribers = $subscriptionModel->getItems();
		$pagination = $subscriptionModel->getPagination();

		foreach ($subscribers as $subscriber) {
			$subscriber = self::process($subscriber);
		}

		JToolBarHelper::title(JText::_('COM_KOMENTO_SUBSCRIBERS'), 'subscribers');
		JToolBarHelper::addNew();
		JToolbarHelper::deleteList();

		$this->set('subscribers', $subscribers);
		$this->set('pagination', $pagination);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);
		$this->set('selectedExtension', $selectedExtension);

		parent::display('subscribers/default/default');
	}

	/**
	 * Renders the form for subscriber
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function form()
	{
		// Check for access
		$this->checkAccess('komento.manage.subscribers');

		$id = $this->input->get('id', 0, 'int');

		$title = 'COM_KOMENTO_SETTINGS_HEADING_NEW_SUBSCRIBER';

		if ($id) {
			$title = 'COM_KOMENTO_SETTINGS_HEADING_EDIT_SUBSCRIBER';
		}

		$this->heading($title);

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::save2new();
		JToolbarHelper::cancel();

		$subscriber = KT::table('Subscription');
		$subscriber->load($id);

		if (!$id) {
			$subscriber->published = true;
		}

		$this->set('subscriber', $subscriber);

		parent::display('subscribers/form/default');
	}

	public function process($row)
	{
		KT::setCurrentComponent($row->component);

		// set extension object
		$row->extension = KT::loadApplication($row->component)->load($row->cid);

		if ($row->extension === false) {
			$row->extension = KT::getErrorApplication($row->component, $row->cid);
		}

		// get permalink
		$row->pagelink = $row->extension->getContentPermalink();

		// set content title
		$row->contenttitle = $row->extension->getContentTitle();

		// set component title
		$row->componenttitle = $row->extension->getComponentName();

		return $row;
	}
}
