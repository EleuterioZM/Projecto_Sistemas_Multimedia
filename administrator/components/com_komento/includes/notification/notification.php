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

jimport('joomla.mail.helper');

class KomentoNotification extends KomentoBase
{
	public $logoName = 'email-logo.png';

	private $mailfrom = null;
	private $fromname = null;

	public function __construct()
	{
		parent::__construct();

		$jconfig = FH::jconfig();
		
		$this->mailfrom = $jconfig->get('mailfrom', '');
		$this->fromname = $jconfig->get('fromname', '');
	}

	/**
	 * Push the email notification to MailQ
	 * @since	3.0
	 * @access	public
	 */
	public function push($type, $recipientGroups, $options = [])
	{
		if (!empty($options['commentId'])) {
			$comment = KT::comment($options['commentId']);
			$options['comment'] = $comment;
			$options['component'] = $comment->component;
			$options['cid'] = $comment->cid;

			unset($options['commentId']);
		}

		if (!isset($options['component']) || !isset($options['cid'])) {
			return;
		}

		$comment = $options['comment'];

		// Determine the type of the comment
		if ($type === 'new' && $comment->parent_id) {
			$type = 'reply';
		}

		$recipients = [];
		$excludes = [];

		// Whether or not to send notification to actor of this action
		$skipCommentActor = true;
		$batchType = [];

		// recipientGroups here means group of recipients (author, usergroup, subscribers)
		$recipientGroups = explode(',', $recipientGroups);

		// process requested recipientGroups first
		foreach ($recipientGroups as $group) {

			// if group is subscribers or usergroup, we will skip here.
			// because we will process it by batch
			if ($group === 'usergroups' || $group === 'subscribers') {
				$batchType[] = $group;
				continue;
			}

			// since we already pass in usergroup and subscribers into the batchtype
			// this will always call getAuthor or getMe method
			$getRecipientMethod = 'get' . ucfirst(strtolower(trim($group)));
			$exists = method_exists($this, $getRecipientMethod);

			// if the method is not exists, skip.
			if (!$exists) {
				continue;
			}

			if ($getRecipientMethod === 'getMe') {
				$skipCommentActor = false;
			}

			$result = $this->$getRecipientMethod($type, $options);
			$recipients = array_merge($result, $recipients);
		}

		// from what i see this excludes only being used in batch process
		// meaning whatever is retrieved from the above will be excluded in the batch
		$excludes = $recipients;

		// If nothing to process, skip everything
		if (empty($recipients) && $type !== 'pending' && empty($batchType)) {
			return;
		}

		if ($type === 'report') {
			$admins = $this->getAdmins();

			foreach ($admins as $admin) {
				if (isset($recipients[$comment->email]) && $comment->email === $admin->email) {
					$skipCommentActor = false;
				}
			}
		}

		// Do not send to the commentor/actor
		if ($skipCommentActor) {
			$obj = new stdClass();
			$obj->id = $comment->created_by;
			$obj->fullname = $comment->name;
			$obj->email = $comment->email;

			$excludes[$obj->email] = $obj;

			if (isset($recipients[$comment->email]) && $recipients[$comment->email]) {
				unset($recipients[$comment->email]);
			}
		}

		$lang = JFactory::getLanguage();

		// Load English first as fallback
		if ($this->config->get('enable_language_fallback')) {
			$lang->load('com_komento', JPATH_ROOT, 'en-GB', true);
		}

		$lang->load('com_komento', JPATH_ROOT, $lang->getDefault(), true);
		$lang->load('com_komento', JPATH_ROOT, null, true);

		$data = $this->prepareData($type, $options);
		$template = $this->getTemplateNamespace($type, $options);
		$subject = $this->prepareTitle($type, $options);

		// I comment out this section first see if the pending comment email still works
		// // Pending e-mails should also be processed
		// if (!empty($recipients) && $type === 'pending') {
		// 	foreach ($recipients as $recipient) {
		// 		$unsubscribe = false;
		// 		$this->insertMailQueue($subject, $template, $data, $recipient, $unsubscribe, $options);
		// 	}
		// }

		// Storing notifications into mailq
		if (!empty($recipients)) {

			foreach ($recipients as $recipient) {
				$unsubscribe = false;

				// assign unsubscription link into email template
				if (($type === 'reply' || $type === 'comment' || $type === 'new') && isset($recipient->subscriptionid) && $recipient->subscriptionid) {

					$unsubscribeData = [
						'subscriptionid' => $recipient->subscriptionid,
						'component' => $recipient->component,
						'id' => $recipient->id, // user id
						'email' => $recipient->email,
						'cid' => $recipient->cid, // article id
						'token' => md5($recipient->subscriptionid.$recipient->created)
					];

					// Generate the unsubscribe hash
					$hash = base64_encode(json_encode($unsubscribeData));
					$unsubscribe = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&controller=subscriptions&task=unSubscribeFromEmail&data=' . $hash;
				}

				$this->insertMailQueue($subject, $template, $data, $recipient, $unsubscribe, $options);
			}
		}

		// process the email batch
		if ($batchType) {
			
			// Get the User Groups Ids for usergroups notification
			$userGroupIds = $this->getUsergroups($type, true);

			// Determine whether system need to notify to the subscriber
			$notifySubscribers = $this->config->get('notification_to_subscribers');
			
			foreach ($batchType as $batch) {

				// Do not notify to the subscriber if the setting is turn off
				if ($batch === 'subscribers' && !$notifySubscribers) {
					continue;
				}

				$this->addMailqBatch($batch, $options['component'], $options['cid'], $subject, $this->mailfrom, $this->fromname, $template, $data, $userGroupIds, $excludes);
			}
		}
	}

	/**
	 * Insert into the mail queue
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function insertMailQueue($subject, $template, $data, $recipient, $unsubscribe, $options = [])
	{
		if (!empty($options)) {
			$triggerResult = KT::trigger('onBeforeSendNotification', ['component' => $options['component'], 'cid' => $options['cid'], 'recipient' => &$recipient]);
			
			if ($triggerResult === false) {
				return false;
			}
		}
		
		$validEmail = JMailHelper::isEmailAddress($recipient->email);

		if (empty($recipient->email) || !$validEmail) {
			return false;
		}

		$contents = $this->getTemplateContents($template, $data, ['recipient' => $recipient], $unsubscribe);

		$table = KT::table('mailq');
		$table->mailfrom = $this->mailfrom;
		$table->fromname = $this->fromname;
		$table->recipient = $recipient->email;
		$table->subject = $subject;
		$table->body = $contents;
		$table->created = FH::date()->toSql();
		$table->type = 'html';
		$table->status = 0;

		$result = $table->store();

		return $result;
	}

	/**
	 * Add to mailq by batch
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function addMailqBatch($type, $component, $cid, $subject, $mailfrom, $fromname, $template, $data, $userGroupIds = [], $exclude = [])
	{
		$db = KT::db();
		$insertDate = FH::date()->toSql();

		if ($type == 'usergroups' && !$userGroupIds) {
			// empty user groups. nothing to send.
			return true;
		}

		$excludeIds = [];

		if ($exclude) {
			foreach ($exclude as $targetEmail => $item) {
				if ($item->id) {
					$excludeIds[] = $item->id;
				}
			}
		}

		$query = "insert into `#__komento_mailq` (`recipient`, `mailfrom`, `fromname`, `subject`, `created`, `type`, `status`, `template`, `data`, `params`)";

		$query .= " SELECT a.`email`," . $db->Quote($mailfrom) . "," . $db->Quote($fromname) . "," . $db->Quote($subject) . "," . $db->Quote($insertDate) . ", " . $db->Quote('html') . "," . $db->Quote('0');
		$query .= ", " . $db->Quote($template) . "," . $db->Quote(json_encode($data));


		if ($type === 'subscribers') {

			$query .= ", concat('{\"subscriptionid\":\"', a.`id`, '\",\"component\":\"', a.`component`, '\", \"id\":\"', a.userid,'\",\"name\":\"', a.`fullname`,'\",\"email\":\"', a.`email`, '\",\"cid\":\"', a.`cid`, '\", \"token\":\"', MD5(concat(a.id,a.created)) , '\"}')";
			$query .= " from `#__komento_subscription` as a";

			$query .= " where a.`component` = " . $db->Quote($component);
			$query .= " and a.`cid` = " . $db->Quote($cid);
			$query .= " and a.`published` = " . $db->Quote(1);

			// send to subscribers who interval set to instant only. #490
			$query .= " and a.`interval` = " . $db->Quote('instant');

			if ($excludeIds) {
				$query .= "	and a.`userid` NOT IN (" . implode(',', $excludeIds) . ")";
			}

			// Exclude subscribers those are exist in usegroup also
			// to avoid dupicate emails
			if ($userGroupIds) {
				$query .= " AND NOT EXISTS (";
				$query .= " SELECT ag.`user_id` FROM `#__user_usergroup_map` as ag";
				$query .= " WHERE ag.`user_id` = a.`userid`";
				$query .= " AND ag.`group_id` IN (" . implode(',', $userGroupIds) . ")";
				$query .= " )";
			}
		}

		if ($type === 'usergroups') {

			$query .= ", concat('{\"id\":\"', a.id,'\",\"name\":\"', a.`name`,'\",\"email\":\"', a.`email`, '\"}')";

			$query .= " from `#__users` as a";
			$query .= " inner join `#__user_usergroup_map` as ag on a.`id` = ag.`user_id`";
			$query .= " where a.`block` = " . $db->quote(0);
			$query .= " and ag.`group_id` IN (" . implode(',', $userGroupIds) . ")";

			if ($excludeIds) {
				$query .= "	and a.`id` NOT IN (" . implode(',', $excludeIds) . ")";
			}
	
			// Get super admin group id
			$saIds = KT::getSAIds();

			// Exclude super admin that turn off the system email
			$query .= " AND a.`id` NOT IN (";
			$query .= " SELECT u.`id` FROM `#__users` as u";
			$query .= " inner join `#__user_usergroup_map` as ug on u.`id` = ug.`user_id`";
			$query .= " where ug.`group_id` IN(" . implode(',', $saIds) . ") and u.`sendEmail` = " . $db->Quote(0);
			$query .= " )";
		}

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Retrieve the contents of the template file
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTemplateContents($namespace, $data, $params = [], $unsubscribe = false, $templatePreview = false)
	{
		$contents = $this->getTemplateBuffer($namespace, $data, $params, $templatePreview);
		
		$logo = $this->getLogo();

		$theme = KT::themes();
		$theme->set('unsubscribe', $unsubscribe);
		$theme->set('contents', $contents);
		$theme->set('logo', $logo);
		$theme->set('templatePreview', $templatePreview);

		$output = $theme->output('site/emails/template');

		return $output;
	}

	/**
	 * Retrieves the content from the template
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getTemplateBuffer($namespace, $data, $params = [], $templatePreview = false)
	{
		$theme = KT::themes();

		FH::loadLanguage('com_komento');

		foreach ($data as $key => $val) {
			$theme->set($key, $val);
		}

		$lipsum = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";

		$theme->set('data', $data);
		$theme->set('options', $params);
		$theme->set('document', $this->doc);
		$theme->set('templatePreview', $templatePreview);
		$theme->set('lipsum', $lipsum);

		$contents = $theme->output($namespace);

		return $contents;
	}

	/**
	 * Method to prepare the data used by email template.
	 *
	 * @since	3.0
	 * @access	private
	 */
	private function prepareData($type, $options)
	{
		$comment = $options['comment'];
		$data = [];

		// New method to get item's title
		$data['contentTitle'] = $comment->getItemTitle();

		// Temporarily commenting this out in v4. If nobody complains in the next version, we can remove this.
		// // Legacy method
		// if (isset($comment->contenttitle)) {
		// 	$data['contentTitle'] = $options['comment']->contenttitle;
		// }

		// New method to get the item permalink
		$data['contentPermalink'] = $comment->getItemPermalink();
		$commentPermalink = $comment->getPermalink();

		if ($type !== 'confirm') {

			// Temporarily commenting this out in v4. If nobody complains in the next version, we can remove this.
			// // Legacy method
			// if (isset($comment->pagelink)) {
			// 	$data['contentPermalink'] = $options['comment']->pagelink;
			// 	$commentPermalink = $data['contentPermalink'] . '#comment-' . $options['comment']->id;
			// }

			$profile = KT::user($comment->created_by);

			$data['commentAuthorName'] = $comment->name;
			$data['commentAuthorAvatar'] = $profile->getAvatar();
		}

		$data['commentDate'] = $comment->getCreatedDate()->format(JText::_('DATE_FORMAT_LC3'));
		$data['commentPermalink'] = $commentPermalink;
		$data['commentContent'] = $comment->getContent();

		if ($type === 'confirm') {
			$application = KT::loadApplication($options['component']);
			$application->load($options['cid']);

			$data['contentPermalink'] = $application->getContentPermalink();

			// If the content title is empty, we get from the application
			if (!$data['contentTitle']) {
				$data['contentTitle'] = $application->getContentTitle();
			}

			$subscribeTable = KT::table('subscription');
			$subscribeTable->load($options['subscribeId']);

			$profile = KT::user($subscribeTable->userid);

			$hashkeys = KT::table('hashkeys');
			$hashkeys->uid = $options['subscribeId'];
			$hashkeys->type = 'subscribe';
			$hashkeys->store();

			$key = $hashkeys->key;
			$returnURL = '&return=' . base64_encode($data['contentPermalink']);

			$data['confirmLink'] = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&task=confirmSubscription&token=' . $key . $returnURL;
		}

		// Pending moderation and submission for moderation
		if (in_array($type, ['pending', 'moderate'])) {
			$hashkeys = KT::getTable('hashkeys');
			$hashkeys->uid = $comment->id;
			$hashkeys->type = 'comment';
			$hashkeys->store();

			$key = $hashkeys->key;

			$data['attachments'] = $comment->getAttachments();
			$data['approveLink'] = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&task=approveComment&token=' . $key;
			$data['rejectLink'] = rtrim(JURI::root(), '/') . '/index.php?option=com_komento&task=rejectComment&token=' . $key;
			$data['commentAuthorEmail'] = $comment->email;
			$data['commentRating'] = $comment->getRatings();
		}

		// Reports
		if ($type === 'report') {
			$action = KT::getTable('actions');
			$action->load($options['actionId']);
			$actionUser = $action->action_by;

			$data['actionUser'] = KT::user($actionUser);
		}

		return $data;
	}

	/**
	 * Determines the template file to be used in the mail
	 *
	 * @since	3.0
	 * @access	public
	 */
	private function getTemplateNamespace($type = 'new')
	{
		// Default file would be new comment template
		$file = 'comment.new';

		if ($type === 'reply') {
			$file = 'comment.reply';
		}

		if ($type === 'report') {
			$file = 'comment.report';
		}

		if ($type === 'pending' || $type === 'moderate') {
			$file = 'comment.moderate';
		}

		if ($type === 'confirm') {
			$file = 'subscription.confirm';
		}

		$file = 'site/emails/' . $file;

		return $file;
	}

	/**
	 * Prepares the e-mail title
	 *
	 * @since	3.0
	 * @access	public
	 */
	private function prepareTitle($type = 'new', $options = [])
	{
		$subject = JText::_('COM_KOMENTO_NOTIFICATION_NEW_COMMENT_SUBJECT');

		if ($type === 'pending' || $type === 'moderate') {
			$subject = JText::_('COM_KOMENTO_NOTIFICATION_PENDING_COMMENT_SUBJECT');
		}

		if ($type === 'confirm') {
			$subject = JText::_('COM_KOMENTO_NOTIFICATION_CONFIRM_SUBSCRIPTION_SUBJECT');
		}

		if ($type === 'report') {
			$subject = JText::_('COM_KOMENTO_NOTIFICATION_REPORT_COMMENT_SUBJECT');
		}

		if ($type === 'reply') {
			$subject = JText::_('COM_KOMENTO_NOTIFICATION_NEW_REPLY_SUBJECT');
		}

		$title = '';

		if (isset($options['component']) && isset($options['cid'])) {
			$app = KT::loadApplication($options['component'])->load($options['cid']);

			$title = '(' . $app->getContentTitle() . ')';
		}

		$subject .= ' ' . $title;

		return $subject;
	}

	/**
	 * Retrieves current logged in user
	 * 
	 * @since	3.0
	 * @access	public
	 */
	public function getMe($type, $options)
	{
		$obj = new stdClass();
		$my = JFactory::getUser();

		if (empty($my->id)) {
			if ($type === 'confirm' && isset($options['subscribeId'])) {
				$subscribeTable = KT::getTable('subscription');
				$subscribeTable->load($options['subscribeId']);

				$obj->id = 0;
				$obj->fullname = $subscribeTable->fullname;
				$obj->email = $subscribeTable->email;

				return [$obj->email => $obj];
			}

			return [];
		}

		$obj->id = $my->id;
		$obj->fullname = JText::_($my->name);
		$obj->email = $my->email;

		return [$my->email => $obj];
	}

	/**
	 * Retrieves author of the content
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAuthor($type, $options)
	{
		if (!$this->config->get('notification_to_author')) {
			return [];
		}

		$application = KT::loadApplication($options['component'])->load($options['cid']);

		if ($application === false) {
			$application = KT::getErrorApplication($options['component'], $options['cid']);
		}

		$userid = $application->getAuthorId();

		$obj = new stdClass();
		$user = JFactory::getUser($userid);
		$obj->id = $user->id;
		$obj->fullname = JText::_($user->name);
		$obj->email = $user->email;

		return [$user->email => $obj];
	}

	/**
	 * Retrieves a list of subscribers on the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getSubscribers($type, $options)
	{
		if (!$this->config->get('notification_to_subscribers')) {
			return [];
		}

		// Normalize options
		$component = isset($options['component']) ? $options['component'] : '';
		$cid = isset($options['cid']) ? $options['cid'] : '';
		$usersOnly = isset($options['usersOnly']) ? $options['usersOnly'] : false;

		if (!$component || !$cid) {
			return [];
		}

		$sql = KT::sql();
		$sql->select('#__komento_subscription')
			->column('id', 'subscriptionid')
			->column('component')
			->column('userid', 'id')
			->column('fullname')
			->column('email')
			->column('cid')
			->column('created')
			->where('component', $options['component'])
			->where('cid', $options['cid'])
			->where('published', 1);

		if ($usersOnly) {
			$sql->where('userid', '', '!=');
		}

		$subscribers = $sql->loadObjectList();

		$result = [];

		if (!$subscribers) {
			return $result;
		}

		foreach ($subscribers as $subscriber) {
			$result[$subscriber->email] = $subscriber;
		}

		return $result;
	}

	/**
	 * Retrieves a list of users by user groups
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getUsergroups($type, $gidOnly = false)
	{
		$config = KT::getConfig();

		$gids = '';

		switch($type)
		{
			case 'confirm':
				break;
			case 'pending':
			case 'moderate':
				$gids = $config->get('notification_to_usergroup_pending');
				break;
			case 'report':
				$gids = $config->get('notification_to_usergroup_reported');
				break;
			case 'reply':
				$gids = $config->get('notification_to_usergroup_reply');
				break;
			case 'comment':
			case 'new':
			default:
				$gids = $config->get('notification_to_usergroup_comment');
				break;
		}

		if (!empty($gids)) {
			if (!is_array($gids)) {
				$gids = explode(',', $gids);
			}

			if ($gidOnly) {
				return $gids;
			}

			$users = [];
			$ids = [];

			foreach ($gids as $gid) {
				$results = JAccess::getUsersByGroup($gid);

				foreach ($results as $id) {

					$tmp = JFactory::getUser($id);

					$user = [
						'id' => $tmp->id,
						'fullname' => $tmp->name,
						'email' => $tmp->email
					];

					$users[$tmp->email] = (object) $user;

				}
			}

			return $users;
		}

		return [];
	}

	/**
	 * Retrieve a list of admin emails
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAdmins()
	{
		if (!$this->config->get('notification_to_admins')) {
			return [];
		}


		$saUsersIds	= KT::getSAUsersIds();

		$sql = KT::sql();
		$sql->select('#__users')
			->column('id')
			->column('name', 'fullname')
			->column('email');

		if ($saUsersIds) {
			$sql->where('id', $saUsersIds, 'in');
		}

		$sql->where('sendEmail', '1');

		$admins	= $sql->loadObjectList();
		$result = [];

		if (!$admins) {
			return $result;
		}

		foreach ($admins as $admin) {
			$result[$admin->email] = $admin;
		}

		return $result;
	}

	/**
	 * Retrieves e-mail logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function getLogo($forceDefault = false)
	{
		$logo = rtrim(JURI::root(), '/') . '/media/com_komento/images/' . $this->logoName;

		if ($forceDefault) {
			return $logo;
		}

		if ($this->hasOverrideLogo() && $this->config->get('custom_email_logo')) {
			$override = $this->getOverridePath();
			$override = rtrim(JURI::root(), '/') . $override;
			return $override;
		}

		return $logo;
	}

	/**
	 * Determine if custom logo is exists
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function hasOverrideLogo()
	{
		$path = JPATH_ROOT . $this->getOverridePath();

		if (JFile::exists($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Get override path for email logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function getOverridePath()
	{
		// Get current template
		$defaultJoomlaTemplate = FH::getCurrentTemplate();

		$path = '/templates/' . $defaultJoomlaTemplate . '/html/com_komento/emails/' . $this->logoName;

		return $path;
	}

	/**
	 * Store email logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function storeEmailLogo($file)
	{
		// Do not proceed if image doesn't exist.
		if (empty($file) || !isset($file['tmp_name'])) {
			return false;
		}

		$source = $file['tmp_name'];

		$path = JPATH_ROOT . $this->getOverridePath();

		// Try to upload the image
		$state = JFile::upload($source, $path);

		if (!$state) {
			$this->setError(JText::_('COM_KOMENTO_EMAIL_LOGO_UPLOAD_ERROR'));
			return false;
		}

		return true;
	}

	/**
	 * Restore Email Logo
	 *
	 * @since	3.0.7
	 * @access	public
	 */
	public function restoreEmailLogo()
	{
		if (!$this->hasOverrideLogo()) {
			return false;
		}

		// Get override path
		$path = JPATH_ROOT . $this->getOverridePath();

		// Let's delete it
		JFile::delete($path);

		return true;
	}
}
