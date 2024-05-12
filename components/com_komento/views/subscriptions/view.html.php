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

		$limitstart = $this->input->get('limitstart', 0, 'int');
		$sort = $this->config->get('default_sort', 'oldest');
		$defaultInterval = '';
		$defaultPostCount = 10;

		$options = [
			'limit'	=> 5,
			'limitstart' => $limitstart
		];

		$model = KT::model('Subscription');

		$subscriptions = $model->getUserSubscriptions($userId, $options);
		$pagination = $model->getPagination();

		if ($subscriptions) {

			$sub = $subscriptions[0];

			$defaultInterval = $sub->interval;
			$defaultPostCount = $sub->count;

			$subscriptions = KT::formatter('subscription', $subscriptions);
		}

		$components = $model->getUniqueComponents();

		// determine whether it should appear that action bar on the user dashboard
		$showActionBar = true;

		$returnURL = base64_encode(JRoute::_('index.php?option=com_komento&view=subscriptions', false));

		// interval options.
		$intervalOptions = ['instant' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_INSTANT', 'daily' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_DAILY', 'weekly' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_WEEKLY', 'monthly' => 'COM_KT_SUBSCRIPTIONS_INTERVAL_MONTHLY'];

		$postCounts = ['5','10', '15', '20', '25', '30'];
		$postCountOptions = [];
		foreach($postCounts as $num) {
			$postCountOptions[$num] = $num;
		}

		$this->set('components', $components);
		$this->set('pagination', $pagination);
		$this->set('subscriptions', $subscriptions);
		$this->set('showActionBar', $showActionBar);
		$this->set('returnURL', $returnURL);
		$this->set('intervalOptions', $intervalOptions);
		$this->set('postCountOptions', $postCountOptions);

		$this->set('defaultInterval', $defaultInterval);
		$this->set('defaultPostCount', $defaultPostCount);

		parent::display('subscriptions/default');
	}
}
