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

class KomentoControllerSubscribers extends KomentoController
{
	public function __construct()
	{
		parent::__construct();

		$this->registerTask('save', 'save');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	/**
	 * Saves a new subscriber
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function save()
	{
		FH::checkToken();

		$this->checkAccess('komento.manage.subscribers');
			
		// If this is being edited
		$id = $this->input->get('id', 0, 'int');

		// Default redirection url
		$return = 'index.php?option=com_komento&view=subscribers';

		$data = $this->input->getArray();

		$subscriber = KT::table('Subscription');

		if ($id) {
			$subscriber->load($id);
		}

		$subscriber->bind($data);

		$valid = $subscriber->validate();

		$errorRedirect = 'index.php?option=com_komento&view=subscribers&layout=form';
		$errorRedirect .= $id ? '&id=' . $id : '';

		if (!$valid) {
			$this->info->set($subscriber->getError(), KOMENTO_MSG_ERROR);
			return $this->app->redirect($errorRedirect);
		}

		// Check subscriber already subscribed
		$isSubscribed = $subscriber->isSubscribed();
		if ($isSubscribed) {
			$this->info->set($subscriber->getError(), KOMENTO_MSG_ERROR);
			return $this->app->redirect($errorRedirect);
		}

		$subscriber->type = 'comment';
		
		$task = $this->getTask();

		$state = $subscriber->store();

		if (!$state) {
			$this->info->set($subscriber->getError(), KOMENTO_MSG_ERROR);
			return $this->app->redirect($errorRedirect);
		}

		if ($task === 'apply') {
			$return = 'index.php?option=com_komento&view=subscribers&layout=form&id=' . $subscriber->id;
		}

		if ($task ==='save') {
			$return = 'index.php?option=com_komento&view=subscribers';
		}

		if ($task === 'save2new') {
			$return = 'index.php?option=com_komento&view=subscribers&layout=form';
		}

		$this->info->set('COM_KOMENTO_SUBSCRIBERS_STORE_SUCCESS', KOMENTO_MSG_SUCCESS);

		return $this->app->redirect($return);
	}

	/**
	 * Deletes a list of selected subscribers
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function remove()
	{
		FH::checkToken();

		$ids = $this->input->get('cid', [], 'int');

		if (!$ids) {
			throw FH::exception('COM_KOMENTO_SUBSCRIBERS_SUBSCRIBER_INVALID_ID', 500);
		}

		$model = KT::model('Subscription');
		$model->remove($ids);

		$this->info->set('COM_KOMENTO_SUBSCRIBERS_SUBSCRIBER_REMOVED', KOMENTO_MSG_SUCCESS);

		return $this->app->redirect('index.php?option=com_komento&view=subscribers');
	}
}
