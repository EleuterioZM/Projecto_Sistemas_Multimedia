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

use Foundry\Helpers\StringHelper;

class KomentoComment extends KomentoBase
{
	private $table = null;
	public $error = null;

	public $rownumber = null;
	public $likes = null;
	public $dislikes = null;
	public $liked = null;
	public $disliked = null;
	public $reported = null;
	public $pid = null;
	public $childs = null;

	public function __construct($comment = null, $options = [])
	{
		parent::__construct();

		$resetCache = false;
		$cache = true;

		// Determines if the object cache should be cleared.
		if (isset($options['resetCache'])) {
			$resetCache = (bool) $options['resetCache'];
		}

		// Determines if the object should be cached
		if (isset($options['cache'])) {
			$cache = (bool) $options['cache'];
		}

		// we know we want to 'preload' or cache the posts.
		// if ($cache && is_array($comment)) {
		// 	// TODO:: preload comments
		//     $this->loadBatchComments($comment);
		//     return;
		// }

		// The $_comment must always be a table.
		$this->table = KT::table('Comments');

		// If passed in argument is an integer, we load it
		if (is_numeric($comment)) {

			$cacheExists = KT::cache()->exists($comment, 'comment');

			// When the post object has already been loaded before, just reuse it
			if ($cacheExists) {
				$cache = KT::cache()->get($comment, 'comment');

				$this->table = $cache;
				$this->bindProperty($cache);
			}

			// When cache doesn't exist, try to load the post
			if (!$cacheExists && $comment != 0) {
				$this->table->load($comment);
				$this->childs = $this->table->initRepliesCount();
			}
		}

		// If passed in argument is already a comments jtable, just assign it.
		if ($comment instanceof KomentoTableComments) {
			$this->table = $comment;
		}

		if (is_object($comment)) {

			if (!$comment instanceof KomentoTableComments) {
				$this->table = KT::table('Comments');
				$this->table->bind($comment);
			}

			$this->bindProperty($comment);
		}

		// keep a copy of original data
		$this->_original = clone $this->table;

	}

	private function bindProperty($source)
	{
		if (isset($source->rownumber)) { $this->rownumber = $source->rownumber;}
		if (isset($source->liked)) { $this->liked = $source->liked;}
		if (isset($source->disliked)) { $this->disliked = $source->disliked;}
		if (isset($source->reported)) { $this->reported = $source->reported;}
		if (isset($source->pid)) { $this->pid = $source->pid;}
		if (isset($source->childs)) { $this->childs = $source->childs;}
	}

	/**
	 * method to load the comments in batch processing
	 *
	 * @since   3.0
	 * @access  private
	 */
	private function loadBatchComments($comments)
	{
		$ids = [];

		foreach ($comments as $item) {
			if (is_numeric($item)) {
				$ids[] = $item;
			}
		}

		if ($ids) {
			// posts
			$model = KT::model('Comments');
			$comments = $model->loadBatchComments($ids);
		}

		KT::cache()->cacheComments($comments);
	}

	/**
	 * Expands a comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function expand()
	{
		$params = $this->getParams();
		$params->set('minimized', 0);

		$this->table->params = $params->toString();
		return $this->table->store();
	}

	/**
	 * Minimizes a comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function minimize()
	{
		$params = $this->getParams();
		$params->set('minimized', 1);

		$this->table->params = $params->toString();
		return $this->table->store();
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function __get($key)
	{
		if (property_exists('KomentoTableComments', $key)) {

			return $this->table->$key;
		}

		if (property_exists($this, $key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Magic method to set properties which don't exist on this object but on the table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function __set($key, $value = '')
	{
		if (property_exists('KomentoTableComments', $key)) {
			$this->table->$key = $value;
		} else {
			$this->$key = $value;
		}
	}

	/**
	 * Binds the given data to the table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function bind($data = array(), $allowBindingId = false, $options = [])
	{
		// Perhaps an object is passed in
		if (!is_array($data)) {
			$data = (array) $data;
		}

		// this is a form post. let clean up the data.
		if (isset($data['Itemid']) && $data['Itemid']) {
			$this->postDataCleanup($data);
		}

		// Do not allow caller to bind id as this is a security issue
		if (isset($data['id']) && !$allowBindingId) {
			unset($data['id']);
		}

		// before normalize, we need to bind the data
		$state = $this->table->bind($data, true);

		// lets genereate the preview column
		if ($this->table->comment) {
			$this->table->preview = KT::parser()->parseComment($this->table->comment);
		}

		if (!isset($options['normalizeData'])) {
			$options['normalizeData'] = true;
		}

		// After binding the data, we also need to normalize the data
		if ($options['normalizeData']) {
			$this->normalizeData($options);
		}

		return $state;
	}

	private function postDataCleanup($data)
	{
		if (isset($data['Itemid'])) {
			unset($data['Itemid']);
		}

		if (isset($data['option'])) {
			unset($data['option']);
		}

		if (isset($data['_ts'])) {
			unset($data['_ts']);
		}

		if (isset($data['format'])) {
			unset($data['format']);
		}

		if (isset($data['no_html'])) {
			unset($data['no_html']);
		}

		if (isset($data['task'])) {
			unset($data['task']);
		}

		if (isset($data['namespace'])) {
			unset($data['namespace']);
		}
	}

	/**
	 * Overrides the parent's store behavior
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function save($options = [])
	{
		// Get any save options if available.
		$this->saveOptions = $options;

		if (!isset($this->saveOptions['isEdited'])) {
			$this->saveOptions['isEdited'] = false;
		}

		if (!isset($this->saveOptions['processPostSave'])) {
			$this->saveOptions['processPostSave'] = true;
		}

		// This allows us to perform necessary logics before the post is really saved
		if (!isset($this->saveOptions['ignorePreSave'])) {
			$this->preSave();
		}

		// Now we can store this in the db
		$state = $this->table->store();

		// Try to store the post.
		if (!$state) {
			$this->setError($this->table->getError());
			return false;
		}

		// This allows us to perform necessary logics after the post is really saved.
		if ($this->saveOptions['processPostSave']) {
			$this->postSave();
		}

		return $state;
	}

	/**
	 * Notify user that has been mentioned in post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function notifyNames()
	{
		$names = KT::string()->detectNames($this->table->comment);

		if (!$names) {
			return;
		}

		// if the avatar integrated with Easysocial, we notify using ES system instead
		if ($this->config->get('layout_avatar_integration') === 'easysocial') {
			KT::easysocial()->notifyNames($names, $this);
			return;
		} 

		$author = $this->getAuthor();

		$data['commentDate'] = $this->getCreatedDate()->format(JText::_('DATE_FORMAT_LC3'));
		$data['commentPermalink'] = $this->getPermalink();;
		$data['commentContent'] = $this->getContent();
		$data['commentAuthorName'] = $author->getName();

		$subject = JText::sprintf('COM_KT_NOTIFICATION_MENTIONED_TITLE', $author->getName(), $this->getItemTitle());
		$template = 'site/emails/comment.mention';

		foreach ($names as $recipient) {
			KT::notification()->insertMailQueue($subject, 'site/emails/comment.mention', $data, $recipient, false);
		}
	}

	/**
	 * Determines if users can reply to this comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canReplyTo()
	{
		// Replies has been disabled or user doesn't have permissions
		if (!$this->config->get('enable_reply') || !$this->profile->allow('reply_comment')) {
			return false;
		}

		// The depth has already reached maximum level, users shouldn't be able to reply to this comment.
		$maximumLevel = $this->config->get('max_threaded_level');

		if ($maximumLevel != 0 && $this->table->depth > ($maximumLevel - 1)) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if users can report this comment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canReport()
	{
		return KT::reports()->isEnabled() && !$this->isMine();
	}

	/**
	 * Determines if the current viewer can manage the comment item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canManage()
	{
		if (KT::isSiteAdmin()) {
			return true;
		}

		// Guests are explicitly disallowed
		if ($this->my->guest) {
			return false;
		}

		if ($this->canEdit() || $this->canDelete() || $this->canFeature() || $this->canUnpublish() || $this->canPublish() || $this->canMinimize()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can edit the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canEdit()
	{
		// Guests are explicitly disallowed
		if ($this->my->guest) {
			return false;
		}

		if (KT::isSiteAdmin()) {
			return true;
		}

		if ($this->access->allow('edit', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can delete the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canDelete()
	{
		// Guests are explicitly disallowed
		if ($this->my->guest) {
			return false;
		}

		if (KT::isSiteAdmin()) {
			return true;
		}

		if ($this->access->allow('delete', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can pin the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canFeature()
	{
		// Guests are explicitly disallowed
		if ($this->my->guest) {
			return false;
		}

		if ($this->parent_id) {
			return false;
		}

		if (KT::isSiteAdmin()) {
			return true;
		}

		if ($this->access->allow('stick', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can minimize a comment
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function canMinimize()
	{
		if (!$this->config->get('enable_minimize')) {
			return false;
		}
		
		if ($this->my->guest) {
			return false;
		}

		if (KT::isSiteAdmin() || $this->access->allow('minimize', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can unpublish the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canUnpublish()
	{
		// Guests are explicitly disallowed
		if ($this->my->guest) {
			return false;
		}

		if (KT::isSiteAdmin()) {
			return true;
		}

		if ($this->access->allow('unpublish', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can submit the comment as spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canSubmitAkismet()
	{
		if ($this->config->get('antispam_akismet_key') && KT::isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can submit the comment as spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canSubmitSpam()
	{
		if (KT::isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the viewer can unpublish the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canPublish()
	{
		if (KT::isSiteAdmin()) {
			return true;
		}

		if ($this->access->allow('publish', $this)) {
			return true;
		}

		return false;
	}

	/**
	 * Ensures that the user is allowed to post comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canPostComments()
	{
		// The user needs to be able to have the appropriate rights
		if (!$this->profile->allow('add_comment')) {
			return false;
		}

		// Ensure that the user isn't flooding
		if ($this->config->get('antispam_flood_control')) {
			$session = KT::session();
			$lastReplied = $session->getLastReplyTime();

			$now = JFactory::getDate();
			$difference = $now->toUnix() - $lastReplied;

			if ($difference && $difference <= $this->config->get('antispam_flood_interval')) {
				$this->setError('COM_KOMENTO_FORM_NOTIFICATION_FLOOD');
				return false;
			}
		}

		return true;
	}

	/**
	 * Publish / Unpublish a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function publish($type = KOMENTO_STATE_PUBLISHED)
	{
		$model = KT::model('Comments');
		$app = $this->getApplication();

		// We know this comment is being published if the current state is KOMENTO_COMMENT_MODERATE
		$isBeingPublished = $this->table->published == KOMENTO_COMMENT_MODERATE && $type === KOMENTO_STATE_PUBLISHED;

		// Unpublish all the childs first
		$childs = $model->getChilds($this->table->id);
		$hasErrors = false;

		if ($childs) {
			foreach ($childs as $childId) {
				$child = KT::comment($childId);

				$state = $child->publish($type);

				if (!$state) {
					$this->setError($child->getError());
					$hasErrors = true;
				}
			}
		}

		// When there are errors unpublish the childs, we should skip this
		if ($hasErrors) {
			return false;
		}

		$now = JFactory::getDate()->toSql();

		$this->table->published = $type;

		// Allow plugins to prevent publish / unpublishing of comments
		$triggerResult = true;
		$triggerArgs = ['component' => $this->table->component, 'cid' => $this->table->cid, 'comment' => &$this];

		if ($type === KOMENTO_STATE_PUBLISHED) {
			$this->table->publish_up = $now;
			$triggerResult = KT::trigger('onBeforePublishComment', $triggerArgs);
		}

		if ($type === KOMENTO_STATE_UNPUBLISHED) {
			$this->table->publish_down = $now;
			$triggerResult = KT::trigger('onBeforeUnpublishComment', $triggerArgs);
		}

		if ($triggerResult === false) {
			$this->setError('Trigger onBeforePublishComment/onBeforeUnpublishComment false');
			return false;
		}

		// Try to save now
		if (!$this->table->store()) {
			$this->setError($this->table->getError());

			return false;
		}

		// Allow plugins to perform actions after the publish / unpublishing of comments
		$triggerArgs = ['component' => $this->table->component, 'cid' => $this->table->cid, 'comment' => &$this];

		if ($type === KOMENTO_STATE_PUBLISHED) {
			KT::trigger('onAfterPublishComment', $triggerArgs);
		}

		if ($type === KOMENTO_STATE_UNPUBLISHED) {
			KT::trigger('onAfterUnpublishComment', $triggerArgs);
		}

		if ($isBeingPublished) {
			$action = $this->table->parent_id ? 'reply' : 'comment';

			// send email
			if ($this->config->get('notification_enable')) {
				if (($action == 'comment' && $this->config->get('notification_event_new_comment')) || ($action == 'reply' && $this->config->get('notification_event_new_reply'))) {
					KT::notification()->push($action, 'subscribers,author,usergroups', ['commentId' => $this->table->id]);
				}
			}

			// Add activity
			$activity = KT::activity()->process($action, $this->table->id);

			// Manually get the attachment of this comment and process the "upload" activity
			$attachments = $this->getAttachments();

			foreach ($attachments as $attachment) {
				KT::activity()->process('upload', $this);
			}
		}

		return true;
	}

	/**
	 * Pins a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function feature()
	{
		$model = KT::model('Comments');
		$state = $model->stick($this->table->id);

		if (!$state) {
			$this->setError($model->getError());
			return false;
		}

		// need to reload the feature flag
		$this->table->sticked = 1;

		return true;
	}

	/**
	 * spam a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function spam()
	{
		$this->table->published = KOMENTO_COMMENT_SPAM;

		// Try to save now
		if (!$this->table->store()) {
			$this->setError($this->table->getError());

			return false;
		}

		// need to clear the report action as well
		$this->removeReport();

		return true;
	}

	/**
	 * flag a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function flag($type = 0)
	{
		$this->table->flag = $type;

		// Try to save now
		if (!$this->table->store()) {
			$this->setError($this->table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Unpins a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function unfeature()
	{
		$model = KT::model('Comments');
		$state = $model->unstick($this->table->id);

		if (!$state) {
			$this->setError($model->getError());
			return false;
		}

		// need to reload the feature flag
		$this->table->sticked = 0;

		return true;
	}

	/**
	 * Normalize the data
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function normalizeData($options = [])
	{
		$fromMigration = isset($options['fromMigration']) ? $options['fromMigration'] : '';

		// Normalize the comment before allowing it to save
		if (is_null($this->table->ip) || !$this->table->ip) {
			$ip = @$_SERVER['REMOTE_ADDR'];

			// Some people might be behind a proxy
			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
				$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$ip = array_pop($ip);
			}

			$this->table->ip = $ip;
		}

		$now = JFactory::getDate()->toSql();

		// Normalize the depth of the comment
		if (is_null($this->table->depth)) {
			$this->table->depth = 0;
		}

		// If user id isn't supplied, try to get the current user
		if (is_null($this->table->created_by) && $this->my->id) {
			$this->table->created_by = (int) $this->my->id;
		}

		// If name isn't specified, use the logged in user's name
		if (!$this->table->name && $this->my->id) {
			$this->table->name = $this->profile->getName();
		}

		// If e-mail isn't specified, use logged in user's email
		if (!$this->table->email && $this->my->id) {
			$this->table->email = $this->profile->email;
		}

		// If creation date isn't supplied, we need to set our own
		if (is_null($this->table->created)) {
			$this->table->created = $now;
		}

		// skip this if the process from migration, because it should respect the publish state from those comment data
		if (!$fromMigration) {
			
			// Default publishing state is enabled
			$this->table->published = 1;

			// Check if the poster's ip address matches any blacklisted ip addresses so that we can enforce moderation state on it
			$blacklist = trim($this->config->get('blacklist_ip', ''));

			if (!empty($blacklist) && $this->table->ip) {
				$blacklist = explode(',', $blacklist);

				if (in_array($this->table->ip, $blacklist)) {
					$this->table->published = 2;
				}
			}

			// Determines if the post should be moderated first
			if ($this->profile->toModerate()) {
				$this->table->published = 2;
			}
		}

		// Honeypot traps
		if ($this->isHoneypotTrapped()) {
			$this->table->published = KOMENTO_COMMENT_SPAM;
			$this->setParam('spam', KT_SPAM_HONEYPOT);
		}

		// Cleantalk antispam detection
		if (KT::cleantalk()->isEnabled() && $this->getCleanTalkSpamLevel() == KOMENTO_CLEANTALK_POSSIBLE_SPAM) {
			$this->table->published = KOMENTO_COMMENT_SPAM;
			$this->setParam('spam', KT_SPAM_CLEANTALK);
		}

		// Akismet antispam detection
		if (KT::akismet()->isEnabled() && $this->isAkismetSpam()) {
			$this->table->published = KOMENTO_COMMENT_SPAM;
			$this->setParam('spam', KT_SPAM_AKISMET);
		}

		// Ensure that the modified date is to the latest time
		if (!$this->table->modified && $this->table->published) {
			$this->table->modified = $now;
		}

		// Ensure that the publish_up date is set
		if (!$this->table->publish_up && $this->table->published) {
			$this->table->publish_up = $now;
		}
	}

	/**
	 * Pre process the comment before we save it.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function preSave()
	{
		KT::trigger('onBeforeSaveComment', ['component' => $this->table->component, 'cid' => $this->table->cid, 'comment' => &$this]);
		
		if ($this->isNew()) {
			// Calculate and update comment boundaries
			$model = KT::model('Comments');
			$model->updateCommentLftRgt($this->table);
		}

		// updating the depth if needed
		if ($this->parent_id) {
			$parent = KT::table('Comments');
			$parent->load($this->parent_id);

			$this->depth = $parent->depth + 1;
		}

		// Whenever a comment moved from other cid, we reset the parent to 0
		if (isset($this->saveOptions['previousCid']) && $this->saveOptions['previousCid'] > 0) {
			$this->parent_id = 0;
		}

		return true;
	}

	/**
	 * Post saving method happens after a comment is stored on the table.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function postSave()
	{
		// Process attachments
		if (isset($this->saveOptions['processAttachments']) && $this->saveOptions['processAttachments']) {
			$attachments = $this->input->get('attachments', '', 'default');

			if (is_array($attachments) && !empty($attachments)) {

				$file = KT::file();

				foreach ($attachments as $attachment) {
					$state = $file->attach($attachment, $this->table->id);

					if ($state) {
						KT::activity()->process('upload', $this);
					}
				}
			}
		}

		// Mentions
		if ($this->config->get('enable_mention')) {
			$this->notifyNames();
		}

		// Updating comment lft/rgt for when article id is changed
		if (isset($this->saveOptions['previousCid']) && $this->saveOptions['previousCid'] > 0) {

			// Move childs over
			$this->updateChildsArticle($this->saveOptions['previousCid']);

			// fix current cid
			$this->fixStructure();

			// fix previous cid
			$this->fixStructure($this->saveOptions['previousCid']);
		}

		// notification to article's subscribers.

		// now we add the comment subscriber here.
		// We need to port the codes from controllers/subscriptions.php into a subscriptions library
		$subscribe = false;

		// // Determines if we should be subscribing the user to the thread
		if ($this->config->get('subscription_auto')) {
			$subscribe = true;
		} else {
			// need to get from form post.
			$subscribe = $this->input->get('subscribe', false, 'default');
		}

		

		if ($this->config->get('enable_subscription') && $subscribe && $this->table->email) {

			$data = [
				'userid' => $this->table->created_by,
				'fullname' => $this->table->name,
				'email' => $this->table->email,
				'commentId' => $this->table->id
			];

			// get user's interval settings. if email digest enabled
			if ($this->config->get('email_digest_enabled')) {

				$defaultInterval = $this->config->get('email_digest_interval');
				$defaultPostCount = 10;

				if ($this->table->created_by) {
					$model = KT::model('Subscription');
					$subscriptions = $model->getUserSubscriptions($this->table->created_by, ['limit' => 1]);
					if ($subscriptions) {
						$sub = $subscriptions[0];

						$defaultInterval = $sub->interval;
						$defaultPostCount = $sub->count;
					}
				}

				$data['interval'] = $defaultInterval;
				$data['postcount'] = $defaultPostCount;
			}

			KT::subscription()->add($this->table->component, $this->table->cid, $data);
		}

		// Set reply datetime in session for flood control
		$session = KT::session();

		if ($this->config->get('antispam_flood_control')) {
			$session->setReplyTime();
		}

		KT::trigger('onAfterSaveComment', ['component' => $this->table->component, 'cid' => $this->table->cid, 'comment' => &$this]);

		// Add activity
		$action = $this->table->parent_id ? 'reply' : 'comment';
		KT::activity()->process($action, $this);

		// Send notifications
		if ($this->config->get('notification_enable')) {

			$isNew = !$this->saveOptions['isEdited'];

			if ($this->isPublished() && $isNew && (($action == 'comment' && $this->config->get('notification_event_new_comment')) || ($action == 'reply' && $this->config->get('notification_event_new_reply')))) {
				KT::notification()->push($action, 'author,usergroups,subscribers', ['commentId' => $this->table->id]);
			}

			if ($this->isPending() && $this->config->get('notification_event_new_pending')) {
				$notifyGroups = 'usergroups';

				if ($this->config->get('notification_event_new_pending_author')) {
					$notifyGroups = 'author,usergroups';
				}

				KT::notification()->push('pending', $notifyGroups, ['commentId' => $this->table->id]);
			}
		}
	}

	public function updateChildsArticle($previousCid)
	{
		$model = KT::model('Comments');
		$model->updateChildsArticle($this->table, $previousCid);
	}
	
	public function fixStructure($oldCid = 0)
	{
		$model = KT::model('comments');

		$cid = $oldCid > 0 ? $oldCid : $this->table->cid;
		$parents = $model->getRootParents($this->table->component, $cid);

		// Set boundary to start from 1
		$boundary = 1;

		// Fix all the parent first
		foreach ($parents as $parent) {
			$model->fixItemStructure($parent, $boundary, 0);
		}

		// Now we start fixing the childrens
		foreach($parents as $parent) {
			$model->fixChildStructure($parent);
		}
	}

	/**
	 * Retrieves the application plugin for the unique item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getApplication()
	{
		return KT::loadApplication($this->table->component)->load($this->table->cid);
	}

	/**
	 * Retrieve attachments within a comment
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getAttachments()
	{
		static $cache = [];

		if (isset($cache[$this->id])) {
			return $cache[$this->id];
		}


		$items = [];

		$cacheExists = KT::cache()->exists($this->id, 'attachment');
		
		if ($cacheExists) {
			$items = KT::cache()->get($this->id, 'attachment');
		} 

		if (!$cacheExists) {
			$model = KT::model('Uploads');
			$items = $model->getAttachments($this->id);
		}

		$cache[$this->id] = [];

		if ($items) {
			
			$acl = KT::acl();

			foreach ($items as $item) {
				// To satisfy the display from Foundry, we need to standardize these props
				$item->name = $item->filename;
				$item->icon = $item->getIconType();
				$item->title = $item->filename;
				$item->image = $item->isImage() ? $item->getLink() : false;
				$item->size = $item->getSize();
				$item->download = $item->getLink();
				$item->canDelete = $acl->allow('delete_attachment', $this);

				$cache[$this->id][] = $item;
			}
		}

		return $cache[$this->id];
	}

	/**
	 * Retrieves the date object for the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getCreatedDate()
	{
		$date = FH::date($this->created, true);

		return $date;
	}

	/**
	 * method port from jtable. to retrieve comment's author.
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getAuthor()
	{
		if (!$this->created_by) {
			$author = KT::user(0);
		} else {
			$author = KT::user($this->created_by);
		}

		return $author;
	}

	/**
	 * Method to retrive author display name based on the config
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getAuthorName()
	{
		if (!$this->created_by) {
			return $this->name;
		}

		return $this->getAuthor()->getName();
	}

	/**
	 * Method to retrive author display name based on the config
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getAuthorEmail()
	{
		if (!$this->created_by) {
			return $this->email;
		}

		return $this->getAuthor()->email;
	}

	/**
	 * Method to retrive author display name based on the config
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getAuthorWebsite()
	{
		if (!$this->created_by) {
			return $this->url;
		}

		return $this->getAuthor()->email;
	}

	/**
	 * Get parent author profile link
	 *
	 * @since   3.1
	 * @access  public
	 */
	public function getParentAuthorLink()
	{
		static $parentAuthorLinks = [];

		if (!isset($parentAuthorLinks[$this->parent_id])) {
			$parentComment = $this->getParent();
			$author = $parentComment->getAuthor();

			$parentAuthorLinks[$this->parent_id] = $author->getProfileLink($parentComment->getAuthorEmail(), $parentComment->getAuthorWebsite());
		}

		return $parentAuthorLinks[$this->parent_id];
	}

	/**
	 * Retrieves the parsed contents of the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getContent($truncate = false, $insertEllipses = false)
	{
		static $result = [];

		$key = $this->id . (int) $truncate . (int) $insertEllipses;

		if (!isset($result[$key])) {
			$contents = $this->table->preview;

			if (!$contents) {
				$contents = KT::parser()->parseComment($this->table->comment);
			}

			if ($truncate) {

				// the content in preview column might have html tags that are being htmlentities, and we need to decode the htmlentities back to it original character
				// before calling strip_tags #520
				$contents = html_entity_decode($contents);

				$insertEllipses = FCJString::strlen($contents) > $truncate;

				$contents = FCJString::substr(strip_tags($contents), 0, $truncate);

				if ($insertEllipses) {
					$contents .= JText::_('COM_KOMENTO_ELLIPSES');
				}
			}

			$result[$key] = $contents;
		}

		return $result[$key];
	}

	/**
	 * Retrieves the address posted with the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAddress($charactersLimit = null)
	{
		if (is_null($charactersLimit)) {
			return $this->table->address;
		}

		return FCJString::substr($this->table->address, 0, $charactersLimit) . JText::_('COM_KOMENTO_ELLIPSES');
	}

	/**
	 * Generates the css class for the comment wrapper
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getCustomCss()
	{
		$classes = [];
		$classes[] = 'kmt-item';

		$author = $this->getAuthor();
		$app = $this->getApplication();

		// Usergroup CSS classes
		if ($author->guest) {
			$classes[] = $this->config->get('layout_css_public');
		} else {
			$classes[] = $this->config->get('layout_css_registered');
		}

		if ($author->isAdmin()) {
			$classes[] = $this->config->get('layout_css_admin');
		}

		if ($this->created_by == $app->getAuthorId()) {
			$classes[] = $this->config->get('layout_css_author');
		}

		$groups = $author->getUsergroups();

		if (is_array($groups) && !empty($groups)) {
			foreach ($groups as $group) {
				$classes[] = 'kt-group-' . $group;
			}
		}

		$custom = implode(' ', $classes);

		return $custom;
	}

	/**
	 * Retrieves the indentation styling
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getIndentStyling($spacer = 'margin', $customDepth = null)
	{
		if (!$this->depth || !$this->config->get('enable_threaded')) {
			return;
		}

		$depth = $this->table->depth;

		if (!is_null($customDepth) && $customDepth) {
			$depth = $customDepth;
		}

		$total = $this->config->get('thread_indentation') * $depth;
		$margin = $spacer . '-left';

		if ($this->doc->getDirection() == 'rtl') {
			$margin = $spacer . '-right';
		}

		$style = $margin . ':' . $total . 'px;';

		return $style;
	}

	/**
	 * Retrieves the content item's title
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getItemTitle()
	{
		$app = $this->getApplication();
		$title = $app->getContentTitle();

		return $title;
	}

	/**
	 * Retrieves the content item's state
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getItemState()
	{
		$app = $this->getApplication();
		$state = $app->getContentState();

		return $state;
	}

	/**
	 * Retrieves the component's title
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getComponentTitle()
	{
		$app = $this->getApplication();
		$title = $app->getComponentName();

		return $title;
	}

	/**
	 * Retrieves the date object for the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getModifiedDate()
	{
		$date = FH::date($this->modified);

		return $date;
	}

	/**
	 * Retrieves the content item's permalink
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getItemPermalink()
	{
		$app = $this->getApplication();
		$permalink = $app->getContentPermalink();

		return $permalink;
	}

	/**
	 * Generates the permalink for the comment.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getPermalink()
	{
		static $permalinks = [];

		$idx = $this->id;

		if (!isset($permalinks[$idx])) {

			$limitperpage = $this->config->get('max_comments_per_page');
			$defaultsort = $this->config->get('default_sort', 'oldest');
			$sort = $this->input->get('sort', '', 'default');

			$model = KT::model('Comments');
			$pageInfo = '';

			if (! isset($this->rownumber) || !$this->rownumber) {
				// we need to manually count the page info.
				$rownumber = $model->getRowNumber($this->id, $defaultsort);
				if ($rownumber) {
					$this->rownumber = $rownumber;
				}
			}

			$pageInfo = '';

			if (!is_null($this->rownumber) && $this->rownumber) {
				$startpage = $this->rownumber / $limitperpage;
				$startpage = ceil($startpage);

				if ($startpage > 1) {
					$pageInfo = ',' . ($startpage - 1) * $limitperpage;
				}

				if ($sort && $sort != $defaultsort) {
					$pageInfo = ($pageInfo) ? $pageInfo . ',' . $sort : ',0,' . $sort;
				}
			}


			$pid = '';

			if (isset($this->pid) && $this->pid) {
				$parentId = $this->pid;
				$pid = ',' . $parentId;
			} else if (is_null($this->pid) && $this->depth) {

				// if this condition meet, thats means this comment object did not go through formatter.
				// let try to get manually
				$parentId = $model->getParents($this->id, true);

				if ($parentId && $parentId != $this->id) {
					$this->pid = $parentId;
					$pid = ',' . $parentId;
				} else {
					$pid = ',0';
				}

			} else {
				$pid = ',0';
			}

			// $permalinks[$idx] = $this->getItemPermalink() . '#comment-' . base64_encode($this->id . $pageInfo);
			$permalinks[$idx] = $this->getItemPermalink() . '#comment-' . $this->id . $pid . $pageInfo;
		}

		return $permalinks[$idx];
	}


	/**
	 * Retrieves the params
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getParams()
	{
		static $data = [];

		if (!isset($data[$this->id])) {
			$registry = new JRegistry($this->params);

			$data[$this->id] = $registry;
		}
		

		return $data[$this->id];
	}

	/**
	 * Retrieves the parent comment if this comment is a reply
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getParent()
	{
		static $parents = [];

		if (! isset($parents[$this->parent_id])) {
			$parents[$this->parent_id] = KT::comment($this->parent_id);
		}

		return $parents[$this->parent_id];
	}

	/**
	 * Retrieves a list of replies for a particular comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getReplies()
	{
		static $items = [];

		if (!isset($items[$this->id])) {

			if (KT::cache()->exists($this->id, 'replies')) {

				$replies = KT::cache()->get($this->id, 'replies');

				$comments = [];

				if ($replies) {
					foreach ($replies as $reply) {

						$comment = KT::comment($reply->id);

						// Get actions likes
						if (KT::cache()->exists($comment->id, 'likes')) {
							$comment->likes = KT::cache()->get($comment->id, 'likes');
						} else {
							$comment->likes = $actionsModel->countAction('likes', $comment->id);
						}

						// Get actions dislikes
						if (KT::cache()->exists($comment->id, 'dislikes')) {
							$comment->dislikes = KT::cache()->get($comment->id, 'dislikes');
						} else {
							$comment->dislikes = $actionsModel->countAction('dislikes', $comment->id);
						}

						$comments[] = $comment;
					}
				}

				$items[$this->id] = $comments;

			} else {

				$model = KT::model('Comments');
				$ids = $model->getChilds($this->id);

				$comments = [];

				if ($ids) {
					// preload
					KT::comment($ids);

					foreach ($ids as $id) {
						$comments[] = KT::comment($id);
					}
				}

				$items[$this->id] = $comments;
			}

		}

		return $items[$this->id];
	}

	public static function convertOrphanitem($id)
	{
		$config = KT::getConfig();

		$comment = KT::getTable('comments');
		$comment->load($id);
		$comment->created_by = $config->get('orphanitem_ownership');
		$comment->store();

		return true;
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
	 * Sets a value in the comment params
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function setParam($key, $value)
	{
		$params = $this->getParams();
		$params->set($key, $value);

		$this->params = $params->toString();
		
		return $params;
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
	 * Validates the current comment object
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function validate($data)
	{
		// Check for name field
		$requireName = ($this->config->get('show_name') == 2 && $this->config->get('require_name') == 2) || ($this->my->guest && $this->config->get('show_name') > 0 && $this->config->get('require_name') == 1);

		if (!$this->table->name && $requireName) {
			$this->setError('COM_KOMENTO_VALIDATE_NAME_REQUIRED');
			return false;
		}

		// Check for email field
		$requireEmail = ($this->config->get('show_email') == 2 && $this->config->get('require_email') == 2) || ($this->my->guest && $this->config->get('show_email') > 0 && $this->config->get('require_email') == 1);

		if (!$this->table->email && $requireEmail) {
			$this->setError('COM_KOMENTO_VALIDATE_EMAIL_REQUIRED');
			return false;
		}

		// Check for website field
		$requireUrl = ($this->config->get('show_website') == 2 && $this->config->get('require_website') == 2) || ($this->my->guest && $this->config->get('show_website') > 0 && $this->config->get('require_website') == 1);

		if (!$this->table->url && $requireUrl) {
			$this->setError('COM_KOMENTO_VALIDATE_WEBSITE_REQUIRED');
			return false;
		}

		// Check for terms and conditions acceptance
		if (KT::form()->requireTerms() && $data['tnc'] == 'false') {

			$this->setError('COM_KOMENTO_VALIDATE_TERMS_REQUIRED');
			return false;
		}

		// Ensure that the e-mail is valid against their defined e-mail pattern
		if ($this->table->email) {
			// $pattern = '/S+@S+/';
			$pattern = '/^([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]+)$/i';

			if (!preg_match($pattern, $this->table->email)) {
				$this->setError('COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID');
				return false;
			}
		}

		$this->table->comment = KT::parser()->removeEmptyBBcodes($this->table->comment);
		$this->table->comment = trim($this->table->comment);

		// Ensure that the comment isn't empty
		if ($this->table->comment === '') {
			$this->setError('COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED');
			return false;
		}

		// Minimum characters check
		if ($this->config->get('antispam_min_length_enable') && FCJString::strlen($this->table->comment) < $this->config->get('antispam_min_length')) {
			$this->setError(JText::sprintf('COM_KOMENTO_FORM_VALIDATION_TOO_SHORT', $this->config->get('antispam_min_length')));
			return false;
		}

		// Maximum characters check
		if ($this->config->get('antispam_max_length_enable') && FCJString::strlen($this->table->comment) > $this->config->get('antispam_max_length')) {
			$this->setError(JText::sprintf('COM_KOMENTO_FORM_VALIDATION_TOO_LONG', $this->config->get('antispam_max_length')));
			return false;
		}

		// Captcha checks
		$captcha = KT::captcha();

		if ($this->config->get('antispam_captcha_enable') && KT::form()->requireCaptcha() && $captcha) {
			$options = [
				'recaptcha_response_field' => isset($data['recaptchaResponse']) ? $data['recaptchaResponse'] : '',
				'captcha-response' => isset($data['captchaResponse']) ? $data['captchaResponse'] : '',
				'captcha-id' => isset($data['captchaId']) ? $data['captchaId'] : ''
			];

			if (!$captcha->verify($options)) {
				$error = $captcha->getError();

				$this->setError($error);
				return false;
			}
		}

		$blockedWords = $this->config->get('blocked_words');
		$hasBlockedWords = StringHelper::hasBlockedWords($blockedWords, $data['comment']);

		if ($hasBlockedWords) {
			$this->setError(JText::sprintf('COM_KT_CONTAINS_BLOCKED_WORD_MESSAGE', $hasBlockedWords, $blockedWords));

			return false;
		}

		return true;
	}

	/**
	 * Validate the antispams
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function validateSpam()
	{
		// Honeypot spam detection
		if ($this->isHoneypotTrapped() && $this->config->get('antispam_honeypot_rejection_type') === 'high') {
			$this->setError('Comment submitted');
			return false;
		}

		// CleanTalk antispam detection
		if (KT::cleantalk()->isEnabled() && ($this->getCleanTalkSpamLevel() == KOMENTO_CLEANTALK_SPAM)) {
			$this->setError('COM_KOMENTO_FORM_NOTIFICATION_CLEANTALK_SPAM');
			return false;
		}

		$akismetRejection = $this->config->get('antispam_akismet_rejection_type');

		// Akismet antispam detection
		if (KT::akismet()->isEnabled() && $akismetRejection == 'high' && $this->isAkismetSpam()) {
			$this->setError('COM_KOMENTO_FORM_NOTIFICATION_AKISMET_SPAM');
			return false;
		}

		return true;
	}

	/**
	 * Check for cleantalk spam
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function getCleanTalkSpamLevel()
	{
		$cleantalk = KT::cleantalk();
		$isSpam = $cleantalk->validate($this);

		if ($isSpam) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the spam type for the comment if it was caught by the antispam filters
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getSpamType()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			$params = $this->getParams();
			$cache[$this->id] = $params->get('spam', false);
		}

		return $cache[$this->id];
	}

	/**
	 * Check for akismet spam
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function isAkismetSpam()
	{
		$akismet = KT::akismet();
		$isSpam = $akismet->isSpam($this);

		if ($isSpam) {
			return true;
		}

		return false;
	}

	/**
	 * Check for honeypot traps
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function isHoneypotTrapped()
	{
		$config = KT::config();
		
		if (!$config->get('antispam_honeypot_enabled')) {
			return false;
		}

		$honeypot = KT::honeypot();
		$trapped = $honeypot->isTrapped();

		return $trapped;
	}


	/**
	 * Determines if the comment has childs
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function hasReplies()
	{
		static $items = [];

		if (!isset($items[$this->id])) {

			if (isset($this->childs)) {
				$items[$this->id] = $this->childs;
			} else {
				$items[$this->id] = $this->getRepliesCount();
			}
		}

		return $items[$this->id];
	}


	public function getRepliesCount()
	{
		$repliesCnt = 0;

		if (is_null($this->childs)) {

			$boundary = ($this->rgt - $this->lft) - 1;

			if (!$this->parent_id && $boundary > 0) {
				$repliesCnt = floor($boundary / 2);
			}

			$this->childs = $repliesCnt;
		}

		return $this->childs;
	}

	/**
	 * Determines if a comment has location
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function hasLocation()
	{
		if ($this->address || ($this->address && $this->latitude && $this->longitude)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if a comment has geometry location
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function hasGeometryLocation()
	{
		if ($this->address && $this->latitude && $this->longitude) {
			return true;
		}

		return false;
	}

	/**
	 * Get Map url based on setting
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getMapUrl()
	{
		if ($this->config->get('location_service_provider') === 'osm') {
			return '//www.openstreetmap.org/?mlat=' . $this->latitude . '&mlon=' . $this->longitude . '#map=15/' . $this->latitude . '/' . $this->longitude;
		}

		return '//maps.google.com/maps?z=15&q=' . $this->latitude . ',' . $this->longitude;
	}

	/**
	 * Determines if the comment is unpublished
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isUnpublished()
	{
		return $this->table->published == KOMENTO_COMMENT_UNPUBLISHED;
	}

	/**
	 * Determines if the comment is pending
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isPending()
	{
		return $this->table->published == KOMENTO_COMMENT_MODERATE;
	}

	/**
	 * Determines if the comment is minimized
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public function isMinimized()
	{
		static $items = [];

		if (!isset($items[$this->id])) {
			$params = $this->getParams();
			$items[$this->id] = $params->get('minimized', false);
		}

		return $items[$this->id];
	}

	/**
	 * Determines if the comment is new
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isNew()
	{
		return $this->_original->id ? false : true;
	}

	/**
	 * Determines if this is a parent comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isParent()
	{
		return !$this->parent_id;
	}

	/**
	 * Determines if the comment is spam
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isSpam()
	{
		return $this->table->published == KOMENTO_COMMENT_SPAM;
	}

	/**
	 * Determines if the comment is reported
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isReport()
	{
		$model = KT::model('Actions');
		$state = $model->countAction('report', $this->table->id);

		return $state;
	}

	/**
	 * Determines if the comment is published
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isPublished()
	{
		return $this->table->published == KOMENTO_COMMENT_PUBLISHED;
	}

	/**
	 * Determines if the comment is a pinned comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isFeatured()
	{
		return $this->table->sticked > 0;
	}

	/**
	 * Determines if the comment has been edited before
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isEdited()
	{
		return $this->table->modified_by > 0;
	}

	/**
	 * Allows caller to delete a comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function delete()
	{
		// @onBeforeDeleteComment
		$args = array('component' => $this->table->component, 'cid' => $this->table->cid, 'comment' => &$this);
		$result = KT::trigger('onBeforeDeleteComment', $args);

		if ($result === false) {
			$this->setError('Trigger onBeforeDeleteComment false');
			return false;
		}

		$state = $this->table->delete();

		if (!$state) {
			$this->setError('Comment delete failed');
			return false;
		}

		// @onAfterDeleteComment
		KT::trigger('onAfterDeleteComment', $args);

		// Always delete the replies no matter what.
		$model = KT::model('Comments');
		$model->deleteChilds($this->table->id);

		// Clear activities
		$activitiesModel = KT::model('Activity');
		$activitiesModel->delete($this->table->id);

		// Clear actions
		$actionsModel = KT::model('Actions');
		$actionsModel->removeAction('all', $this->table->id, 'all');

		// Process activities
		KT::activity()->process('remove', $this);

		// Remove attachments
		KT::file()->clearAttachments($this->table->id);

		return true;
	}

	/**
	 * Generates an export-able data that is safe for viewing
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function export()
	{
		$data = (object) get_object_vars($this->table);

		return $data;
	}

	/**
	 * Allows caller to remove reports from a particular comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeReport()
	{
		$model = KT::model('Actions');
		$state = $model->removeAction('report', $this->table->id);

		return $state;
	}

	/**
	 * Retrieve commment ratings
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getRatings()
	{
		return $this->table->ratings / 2;
	}

	/**
	 * Determine whether the comment is ownself.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isMine()
	{
		if ((int) $this->created_by === (int) $this->my->id) {
			return true;
		}

		return false;
	}
}
