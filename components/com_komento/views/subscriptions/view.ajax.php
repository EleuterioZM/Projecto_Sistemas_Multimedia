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

class KomentoViewSubscriptions extends KomentoView
{
	/**
	 * Renders the subscribe dialog
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function subscribe()
	{
		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');


		// check if user has any subscription before or not to get the interval
		$defaultInterval = '';
		$defaultPostCount = 10;

		if ($this->my->id) {
			$model = KT::model('Subscription');
			$subscriptions = $model->getUserSubscriptions($this->my->id, ['limit' => 1]);

			if ($subscriptions) {
				$sub = $subscriptions[0];

				$defaultInterval = $sub->interval;
				$defaultPostCount = $sub->count;
			}
		}

		$intervalOptions = ['instant' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_INSTANT', 'daily' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_DAILY', 'weekly' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_WEEKLY', 'monthly' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_MONTHLY'];

		$postCounts = ['5','10', '15', '20', '25', '30'];
		$postCountOptions = [];
		foreach($postCounts as $num) {
			$postCountOptions[$num] = $num;
		}

		$theme = KT::themes();
		$theme->set('component', $component);
		$theme->set('cid', $cid);
		$theme->set('intervalOptions', $intervalOptions);
		$theme->set('postCountOptions', $postCountOptions);
		$theme->set('defaultInterval', $defaultInterval);
		$theme->set('defaultPostCount', $defaultPostCount);

		$output = $theme->output('site/subscriptions/dialogs/form');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the unsubscribe dialog
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function confirmUnsubscribe()
	{
		// Only logged in users can access this
		if (!$this->my->id) {
			die();
		}

		$component = $this->input->get('component', '', 'cmd');
		$cid = $this->input->get('cid', 0, 'int');

		$model = KT::model('Subscription');
		$subscriptionId = $model->getSubscriptionId($component, $cid, $this->my->id);

		$subscription = KT::table('Subscription');
		$subscription->load($subscriptionId);

		$theme = KT::themes();
		$theme->set('subscription', $subscription);
		$theme->set('component', $component);
		$theme->set('cid', $cid);
		$output = $theme->output('site/subscriptions/dialogs/unsubscribe');

		return $this->ajax->resolve($output);
	}


	/**
	 * Renders the unsubscribe dialog in dashboard
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function dashboardConfirmUnsubscribe()
	{
		FH::checkToken();

		$cids = $this->input->get('cids', 0, 'array');

		if (!$cids || !$this->my->id) {
			return $this->ajax->reject('Invalid data.');
		}

		$valid = true;

		foreach ($cids as $cid) {
			$sub = KT::table('Subscription');
			$sub->load($cid);

			if (!$sub->id || $sub->userid != $this->my->id) {
				$valid = false;
				break;
			}
		}

		if (!$valid) {
			return $this->ajax->reject('Invalid subscription data.');
		}

		$theme = KT::themes();
		$output = $theme->output('site/subscriptions/dialogs/unsubscribe.batch');

		return $this->ajax->resolve($output);
	}

	/**
	 * Unsubscribe user from selected posts.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function dashboardUnsubscribe()
	{
		FH::checkToken();

		$cids = $this->input->get('cids', 0, 'array');

		if (!$cids || !$this->my->id) {
			return $this->ajax->reject('Invalid data.');
		}

		foreach ($cids as $cid) {
			$sub = KT::table('Subscription');
			$sub->load($cid);

			if ($sub->id && $sub->userid == $this->my->id) {
				$sub->delete();
			}
		}

		return $this->ajax->resolve();
	}


	/**
	 * Update user's subscription interval
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateInterval()
	{
		FH::checkToken();

		$interval = $this->input->get('interval', '', 'string');
		$userid = $this->input->get('uid', 0, 'int');

		if (!$interval || !$userid) {
			return $this->ajax->reject('Invalid data.');
		}

		$availableInteval = ['instant', 'daily', 'weekly', 'monthly'];

		if (!in_array($interval, $availableInteval)) {
			return $this->ajax->reject('Invalid data.');
		}


		$model = KT::model('Subscription');
		$model->updateUserSubscriptions($userid, 'interval', $interval);

		return $this->ajax->resolve();
	}

	/**
	 * Update user's subscription posts count
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updatePostCount()
	{
		FH::checkToken();

		$postcount = $this->input->get('postcount', 10, 'int');
		$userid = $this->input->get('uid', 0, 'int');

		if (!$postcount || !$userid) {
			return $this->ajax->reject('Invalid data.');
		}

		$model = KT::model('Subscription');
		$model->updateUserSubscriptions($userid, 'count', $postcount);

		return $this->ajax->resolve();
	}
}
