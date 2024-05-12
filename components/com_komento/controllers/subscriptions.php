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

require_once(__DIR__ . '/base.php');

class KomentoControllerSubscriptions extends KomentoControllerBase
{
	/**
	 * Allows caller to unsubscribe from a thread
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unsubscribe()
	{
		FH::checkToken();

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');

		if (!$component || !$cid) {
			throw FH::exception('COM_KOMENTO_UNSUBSCRIBE_FAILED', 500);
		}

		// remove subscription
		$lib = KT::subscription();
		$state = $lib->remove($component, $cid, $this->my->id, $this->my->email);

		// Get the return url
		$returnUrl = $this->getReturnUrl();

		if (!$state) {

			$errorMessage = $lib->getError();
			if (!$errorMessage) {
				$errorMessage = JText::_('COM_KOMENTO_ERROR');
			}

			$this->app->enqueueMessage($errorMessage, 'error');
			return $this->app->redirect($returnUrl);
		}

		$this->app->enqueueMessage(JText::_('COM_KOMENTO_UNSUBSCRIBED_SUCCESSFULLY'));
		return $this->app->redirect($returnUrl);
	}

	/**
	 * Allows caller to subscribe to a thread
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function subscribe()
	{
		FH::checkToken();

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');
		$interval = $this->input->get('interval', '', 'string');


		if (!$component || !$cid) {
			throw FH::exception('COM_KOMENTO_UNSUBSCRIBE_FAILED', 500);
		}

		$name = $this->my->name;
		$email = $this->my->email;

		if ($this->my->guest) {
			// get from form post.
			$name = $this->input->get('name', '', 'default');
			$email = $this->input->get('email', '', 'default');
		}

		$lib = KT::subscription();

		if ($lib->exists($component, $cid, $this->my->id, $email)) {
			throw FH::exception('COM_KT_ALREADY_SUBSCRIBED', 500);
		}

		$data = [
			'userid' => $this->my->id,
			'fullname' => $name,
			'email' => $email
		];

		if ($interval) {
			$data['interval'] = $interval;
		}

		$state = $lib->add($component, $cid, $data);

		if (!$state) {
			throw FH::exception($lib->getError(), 500);
		}

		// Once this is done, redirect back to the original page
		$returnUrl = $this->getReturnUrl();

		$message = JText::_('COM_KT_SUBSCRIBE_SUCCESS');

		$this->app->enqueueMessage($message);
		return $this->app->redirect($returnUrl);
	}

	/**
	 * Allows caller to unsubscribe from the email
	 *
	 * @since	3.0.13
	 * @access	public
	 */
	public function unSubscribeFromEmail()
	{
		$data = $this->input->get('data', '', 'raw');

		$state = KT::subscription()->removeSubscriptionFromEmail($data);

		return $state;
	}
}
