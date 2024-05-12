<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoSubscription extends KomentoBase
{
	public $type = null;

	// This contains the error message.
	public $error = null;

	public function __construct($type = 'comment')
	{
		parent::__construct();

		$this->type = $type;
	}

	/**
	 * Allows caller to insert a new subscription for a particular item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function add($component, $cid, $data)
	{
		$userid = isset($data['userid']) && $data['userid'] ? $data['userid'] : 0;
		$email = isset($data['email']) && $data['email'] ? $data['email'] : '';
		$interval = isset($data['interval']) && $data['interval'] ? $data['interval'] : 'instant';
		$postcount = isset($data['postcount']) && $data['postcount'] ? $data['postcount'] : 10;

		// perform basic validation.
		$exists = $this->exists($component, $cid, $userid, $email);

		if ($exists) {
			$this->setError('COM_KT_ALREADY_SUBSCRIBED');
			return false;
		}

		// comment id
		$commentId = isset($data['commentId']) && $data['commentId'] ? $data['commentId'] : 0;

		$table = KT::table('Subscription');
		$table->type = $this->type;
		$table->component = $component;
		$table->cid = $cid;
		$table->userid = $userid;
		$table->fullname = isset($data['fullname']) && $data['fullname'] ? $data['fullname'] : '';
		$table->email = $email;
		$table->created = JFactory::getDate()->toSql();
		$table->published = KT_SUBSCRIPTION_PUBLISHED;
		$table->interval = $interval;
		$table->count = $postcount;

		if ($this->config->get('subscription_confirmation')) {
			$table->published = KT_SUBSCRIPTION_PENDING;
		}

		$state = $table->store();

		if (!$state) {
			$this->setError($table->getError());
			return false;
		}

		// lets check if we need to notify the subscriber or not.
		if ($this->config->get('subscription_confirmation')) {
			KT::notification()->push('confirm', 'me', [
				'component' => $component, 
				'cid' => $cid, 
				'subscribeId' => $table->id, 
				'commentId' => $commentId,
				'comment' => KT::comment($commentId)
			]);
		}

		return true;
	}

	/**
	 * Allows caller to remove a subscription
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function remove($component, $cid, $userId = 0, $userEmail = '')
	{
		// perform validation first.
		$exists = $this->exists($component, $cid, $userId, $userEmail);

		if ($exists === null) {
			$this->setError('COM_KOMENTO_SUBSCRIPTION_NOT_FOUND');
			return false;
		}

		$model = KT::model('Subscription');
		$state = $model->unsubscribe($component, $cid, $userId, $userEmail, $this->type);

		if (!$state) {
			$this->setError($model->getError());
			return false;
		}

		return true;
	}

	/**
	 * Determines if a subscription exists on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function exists($component, $cid, $userId = 0, $userEmail = '')
	{
		if (!$userId && !$userEmail) {
			$this->setError('Invalid subscriber details.');
			return false;
		}

		$exists = false;
		$model = KT::model('Subscription');

		if ($userId) {
			$exists = $model->checkSubscriptionExist($component, $cid, $userId, '', $this->type);
		} else {
			// user email to check
			$exists = $model->checkSubscriptionExist($component, $cid, '', $userEmail, $this->type);

		}

		return $exists;
	}


	/**
	 * Sets an error message
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function setError($message = '')
	{
		$this->error = JText::_($message);
	}

	/**
	 * Get an error message
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getError($message = '')
	{
		return $this->error;
	}

	/**
	 * Allows caller to remove a subscription from email
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function removeSubscriptionFromEmail($data)
	{
		// decode that hash data
		$data = base64_decode($data);
		$data = json_decode($data);

		// perform validation first.
		$exists = $this->exists($data->component, $data->cid, $data->id, $data->email);

		if ($exists === null) {
			echo JText::_('COM_KOMENTO_SUBSCRIPTION_NOT_FOUND');
			exit;
		}

		$subscription = KT::table('Subscription');
		$subscription->load($data->subscriptionid);

		// Verify if this user has access to unsubscribe for guest user
		if (!$subscription->id) {
			echo JText::_('COM_KOMENTO_NOT_ALLOWED');
			exit;
		}

		// Ensure that the registered user is allowed to unsubscribe.
		if ($subscription->userid && $this->my->id != $subscription->userid && !KT::isSiteAdmin()) {
			echo JText::_('COM_KOMENTO_NOT_ALLOWED');
			exit;
		}

		// Ensure that unsubscribe token is match
		if ($data->token != md5($subscription->id . $subscription->created)) {
			echo JText::_('COM_KOMENTO_NOT_ALLOWED');
			exit;
		}

		$model = KT::model('Subscription');
		$state = $model->unsubscribe($data->component, $data->cid, $data->id, $data->email, $this->type);

		if (!$state) {
			$errorMessage = $model->getError();
			echo $errorMessage;
			exit;
		}

		// Get the item permalink so that we can redirect user to a proper page
		$model = KT::model('Comments');
		$itemPermalink = $model->getItemPermalink($data->component, $data->cid);

		$this->app->enqueueMessage(JText::_('COM_KOMENTO_UNSUBSCRIBED_SUCCESSFULLY'));
		return $this->app->redirect($itemPermalink);
	
	}


	/**
	 * Method to process email digest notification
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processDigest($max = 5)
	{
		// If the feature is disabled, stop here.
		if (!$this->config->get('email_digest_enabled')) {
			return false;
		}

		$now = FH::date()->toSql();

		$model = KT::model('Subscription');
		$subscribers = $model->getDigestSubscribers($now, $max);

		// nothing to process
		if (! $subscribers) {
			return false;
		}


		$displayDateFormat = 'l, d F Y';

		foreach ($subscribers as $subscriber) {

			$fullname = $subscriber->fullname;
			$email = $subscriber->email;
			$interval = $subscriber->interval;
			$count = $subscriber->count;

			$items = $model->getDigestEmailSubscriptions($now, $email, $count);

			if (!$items) {
				continue;
			}

			$commentsContent = "";
			$repliesContent = "";
			$postsContent = "";


			$postsContainer = [];

			foreach ($items as $item) {

				KT::setCurrentComponent($item->component);

				// set extension object
				$extension = KT::loadApplication($item->component)->load($item->cid);

				if ($extension === false) {
					$extension = KT::getErrorApplication($item->component, $item->cid);
				}

				// prepare the data for ubsubscribe link
				$unsubscribeData = [
					// user id
					'id' => $item->userid,
					// article id
					'cid' => $item->cid,
					'subscriptionid' => $item->id,
					'component' => $item->component,
					'email' => $item->email,
					'token' => md5($item->id . $item->created)
				];

				// Generate the unsubscribe hash
				$hash = base64_encode(json_encode($unsubscribeData));
				$unsubscribeLink = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&controller=subscriptions&task=unSubscribeFromEmail&data=' . $hash;

				$obj = new stdClass();
				$obj->component = $item->component;
				$obj->cid = $item->cid;
				$obj->title = $extension->getContentTitle();
				$obj->link = $extension->getContentPermalink();
				$obj->unlink = $unsubscribeLink;
				$obj->posts = [];

				$idx = $item->component.'_'.$item->cid;

				$postsContainer[$idx] = $obj;
			}

			// get comments for each post subscriptions
			$comments = $model->getDigestComments($items, $now);

			if ($comments) {

				foreach ($comments as $comment) {
					$idx = $comment->component . '_' . $comment->cid;

					$obj = new stdClass();

					$obj->articleTitle = $postsContainer[$idx]->title;
					$obj->articleLink = $postsContainer[$idx]->link;
					$obj->authorName = $comment->name;
					$obj->created = FH::date($comment->created)->format($displayDateFormat);

					$ellipses = FCJString::strlen($comment->comment) > 30 ? '...' : '';
					$obj->comment = FCJString::substr($comment->comment, 0, 30) . $ellipses ;

					$postsContainer[$idx]->posts[] = $obj;
				}

				$icon = rtrim(JURI::root(), '/') . '/media/com_komento/images/icons/comment.png';

				$namespace = "site/emails/subscription.digest.comments";

				$theme = KT::themes();
				$theme->set('articles', $postsContainer);
				$theme->set('icon', $icon);
				$postsContent = $theme->output($namespace);
			}

			if ($postsContent) {

				$subject = JText::sprintf('COM_KT_DIGEST_EMAIL_SUBJECT', FH::date()->format($displayDateFormat), FH::jconfig()->get('sitename'));
				$data = ['content' => $postsContent, 'sitename' => FH::jconfig()->get('sitename')];

				$recipient = new stdClass();
				$recipient->email = $email;
				$recipient->fullname = $fullname;
				$recipient->name = $fullname;

				KT::notification()->insertMailQueue($subject, 'site/emails/subscription.digest', $data, $recipient, false);
			}

			// now update subscriptions sent_out
			$model->updateDigestSentOut($items);
			
		}

	}


}
