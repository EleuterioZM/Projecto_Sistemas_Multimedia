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

class KomentoEasySocial
{
	private $file = null;

	public function __construct()
	{
		FH::loadLanguage('com_komento');

		$this->file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
		$this->config = KT::config();
		$this->my = JFactory::getUser();
		$this->doc = JFactory::getDocument();
	}

	/**
	 * Determines if EasySocial is installed on the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function exists()
	{
		static $loaded = false;

		if ($loaded) {
			return true;
		}

		if (!JFile::exists($this->file)) {
			return false;
		}

		require_once($this->file);

		$loaded = true;

		return true;
	}

	/**
	 * Initializes EasySocial
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function init()
	{
		static $loaded = null;

		if (!is_null($loaded)) {
			return $loaded;
		}

		if (!$this->exists() || $this->doc->getType() != 'html') {
			$loaded = false;

			return false;
		}

		// Prior to EasySocial 2.0
		if ($this->isLegacy()) {
			$doc = ES::document();
			$doc->init();

			$page = ES::page();
			$page->processScripts();
		} else {
			ES::initialize();
		}

		// Load EasySocial's language files
		ES::language()->loadSite();

		$loaded = true;

		return $loaded;
	}

	/**
	 * Determines if this is EasySocial prior to 2.x
	 *
	 * @since	2.0.11
	 * @access	public
	 */
	public function isLegacy()
	{
		if (!$this->exists()) {
			return;
		}

		// Get the current version.
		$local = ES::getLocalVersion();

		$legacy = version_compare($local, '2.0.0') == -1 ? true : false;

		return $legacy;
	}

	/**
	 * Get the Komento Comments app table object
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getApp()
	{
		static $app = null;

		if (!isset($app)) {

			$options = ['element' => 'comments', 'type' => 'apps', 'group' => 'user'];

			$table = ES::table('App');
			$state = $table->load($options);

			if (!$state) {
				$app = false;
			} else {
				$app = $table;
			}
		}

		return $app;
	}

	/**
	 * Assign badge to a user
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function assignBadge($action, KomentoComment $comment)
	{
		if (!$this->exists() || !$this->config->get('enable_easysocial_badges')) {
			return false;
		}

		$badge = ES::badges();

		if ($action == 'comment') {
			$badge->log('com_komento', 'komento.add.comment', $comment->created_by, JText::_('COM_KOMENTO_EASYSOCIAL_BADGES_ADD_COMMENTS_MESSAGE'));
		}

		if ($action == 'reply') {
			$badge->log('com_komento', 'komento.reply.comment', $comment->created_by, JText::_('COM_KOMENTO_EASYSOCIAL_BADGES_REPLY_COMMENTS_MESSAGE'));
		}

		if ($action == 'like') {
			$badge->log('com_komento', 'komento.like.comment', $this->my->id, JText::_('COM_KOMENTO_EASYSOCIAL_BADGES_LIKE_COMMENTS_MESSAGE'));
		}

		if ($action == 'report') {
			$badge->log('com_komento', 'komento.report.comment', $this->my->id, JText::_('COM_KOMENTO_EASYSOCIAL_BADGES_REPORT_COMMENTS_MESSAGE'));
		}

		if ($action == 'upload') {
			$badge->log('com_komento', 'komento.upload.attachments', $comment->created_by, JText::_('COM_KOMENTO_EASYSOCIAL_BADGES_UPLOAD_COMMENTS_MESSAGE'));
		}

		return true;
	}

	/**
	 * Assign points for a particular activity
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function assignPoints($action, KomentoComment $comment, $actor)
	{
		if (!$this->exists() || !$this->config->get('enable_easysocial_points')) {
			return false;
		}

		$author = ES::user($comment->created_by);
		$points	= ES::points();

		// Get applications author of the item
		$app = $comment->getApplication();
		$authorId = $app->getAuthorId();

		if ($action == 'comment') {
			// Assign points for the article author
			$points->assign('komento.add.comment.article.author', 'com_komento', $authorId);
		}

		if ($action == 'comment' || $action == 'reply') {
			$rule = 'komento.add.comment';

			if ($action == 'reply') {
				$rule = 'komento.reply.comment';
			}

			if (method_exists($points, 'getParams')) {
				$params	= $points->getParams($rule, 'com_komento');
				$length	= FCJString::strlen($comment->comment);
				$state 	= false;

				if ($params && $params->get('min')) {
					$min = isset($params->get('min')->value) ? $params->get('min')->value : $params->get('min')->default;
					
					if ($length > $min || $min == 0) {
						$points->assign($rule, 'com_komento', $comment->created_by);
					}
				} else {
					$points->assign($rule, 'com_komento', $comment->created_by);
				}
			} else {
				$points->assign($rule, 'com_komento', $comment->created_by);
			}
		}

		if ($action == 'like') {
			$points->assign('komento.like.comment', 'com_komento', $actor->id);
			$points->assign('komento.comment.liked', 'com_komento', $comment->created_by);
		}

		if ($action == 'unlike') {
			$points->assign('komento.unlike.comment', 'com_komento', $actor->id);
			$points->assign('komento.comment.unliked', 'com_komento', $comment->created_by);
		}

		if ($action == 'report') {
			$points->assign('komento.report.comment', 'com_komento', $actor->id);
			$points->assign('komento.comment.reported', 'com_komento', $comment->created_by);
		}

		if ($action == 'unreport') {
			$points->assign('komento.unreport.comment', 'com_komento', $actor->id);
			$points->assign('komento.comment.unreported', 'com_komento', $comment->created_by);
		}

		if ($action == 'stick') {
			$points->assign('komento.comment.sticked', 'com_komento', $comment->created_by);
		}

		if ($action == 'unstick') {
			$points->assign('komento.comment.unsticked', 'com_komento', $comment->created_by);
		}

		if ($action == 'remove') {
			$points->assign('komento.comment.removed', 'com_komento', $comment->created_by);
			$points->assign('komento.remove.comment.article.author', 'com_komento', $authorId);
		}

		if ($action == 'upload') {
			$points->assign('komento.upload.attachments', 'com_komento', $comment->created_by);
		}

		return true;
	}

	/**
	 * Creates a new stream for new comment post
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function createStream($action, &$comment)
	{
		if (!$this->exists()) {
			return false;
		}

		// Make sure this is a new comment
		if ($action == 'comment' && !$comment->isNew()) {
			return false;
		}

		$my = ES::user();

		$stream = ES::stream();
		$template = $stream->getTemplate();
		$template->setContext($comment->id, 'komento');
		$template->setVerb($action);
		$template->setPublicStream('core.view');
		$template->setType('full');

		if ($action == 'like') {

			$options = ['actor_id' => $my->id, 'actor_type' => SOCIAL_TYPE_USER, 'context_type' => 'komento', 'context_id' => $comment->id, 'verb' => 'like'];

			$table = ES::table('StreamItem');
			$state = $table->load($options);

			if ($state) {
				return false;
			}

			$template->setActor($my->id, SOCIAL_TYPE_USER);
			$template->setType('mini');
		}

		if ($action == 'comment') {
			$template->setActor($comment->created_by, SOCIAL_TYPE_USER);
		}

		if ($action == 'reply') {
			$template->setActor($comment->created_by, SOCIAL_TYPE_USER);
		}

		$state = $stream->add($template);

		if ($state) {
			$table = ES::table('StreamItem');
			$table->load(['actor_id' => $my->id, 'actor_type' => SOCIAL_TYPE_USER, 'context_type' => 'komento', 'context_id' => $comment->id, 'verb' => 'comment']);

			$params = $comment->getParams();
			$data = $params->get('social');

			if (!$data) {
				$data = new stdClass();
			}

			$data->stream = $table->uid;

			$params->set('social', $data);
			$comment->params = $params->toString();

			$comment->save(['processPostSave' => false, 'ignorePreSave' => true]);
		}

		return $state;
	}

	/**
	 * Synchronizes comments between Komento and EasySocial
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function syncComments($action, KomentoComment $comment)
	{
		if (!$this->config->get('enable_easysocial_sync_comment')) {
			return false;
		}

		$params = $comment->getParams();
		$social = $params->get('social', '');

		if ($action === 'reply') {

			if (empty($social)) {
				$this->createComment($comment, $social);
			} else {
				$this->editComment($comment, $social);
			}
			
		}

		if ($action === 'remove' && $this->config->get('enable_easysocial_sync_comment')) {
			$this->removeComment($comment);
		}
	}

	/**
	 * When synchronizing comments is enabled we need to inject the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function editComment(&$comment, $socialParams = '')
	{
		if (!$this->exists()) {
			return false;
		}

		$parent = $comment->getParent();
		$parentParams = $parent->getParams();
		$social = $parentParams->get('social');

		$socialComment = ES::table('Comments');

		if (isset($socialParams->target)) {
			$socialComment->load($socialParams->target);
			$socialComment->comment = $comment->comment;
			$socialComment->store();
		}

		if (isset($socialParams->source)) {
			$socialComment->load($socialParams->source);
			$socialComment->comment = $comment->comment;
			$socialComment->store();
		}
	}

	/**
	 * When synchronizing comments is enabled we need to inject the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function createComment(&$comment, $socialParams = '')
	{
		if (!$this->exists()) {
			return false;
		}

		$parent = $comment->getParent();
		$parentParams = $parent->getParams();
		$social = $parentParams->get('social');

		// If the comment is not linked to a stream item, we can't synchronize the comment
		if (empty($social->stream)) {
			return false;
		}

		$socialComment = ES::table('Comments');
		$streamId = $social->stream;

		// If there is a target, means this is replying
		if (isset($social->target) && $social->target) {
			$socialComment->parent = $social->target;
		}

		// Create EasySocial comments
		$socialComment->element = 'komento.user.create';
		$socialComment->created_by = $comment->created_by;
		$socialComment->comment = $comment->comment;
		$socialComment->uid = $streamId;
		$socialComment->stream_id = $streamId;
		$socialCommentParams = new JRegistry();
		$socialCommentParams->set('url', ESR::stream(['layout' => 'item', 'id' => $streamId]));
		$socialCommentParams->set('komento', (object) ['source' => $comment->id]);

		$socialComment->params = $socialCommentParams->toString();

		$state = $socialComment->store();

		// Once we stored the comments on EasySocial, we now need to link
		if ($state) {
			$social->target = $socialComment->id;

			$parentParams->set('social', $social);

			$comment->params = $parentParams->toString();

			$comment->save(['processPostSave' => false]);
		}
	}

	/**
	 * Removes a comment on easysocial
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeComment(KomentoComment $comment)
	{
		if (!$this->exists()) {
			return false;
		}

		// Try to delete any existing stream item from EasySocial
		ES::stream()->delete($comment->id, 'komento', $comment->created_by);

		$params = $comment->getParams();
		$social = $params->get('social', '');

		if (!empty($social->target)) {
			$commentTable = ES::table('comments');
			$state = $commentTable->load($social->target);

			if ($state) {
				$commentTable->delete();
			}
		}

		if (!empty($social->source)) {
			$commentTable = ES::table('comments');
			$state = $commentTable->load($social->source);

			if ($state) {
				$commentTable->delete();
			}
		}
	}

	/**
	 * Synchronizes the likes between a komento comment item and easysocial stream item
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function syncLikes($action, KomentoComment $comment)
	{
		if (!$this->config->get('enable_easysocial_sync_like')) {
			return false;
		}

		if ($action == 'like') {
			$this->createLikes($comment);
		}

		if ($action === 'unlike') {
			$this->removeLike($comment);
		}
	}

	/**
	 * Creates a like record for the stream item in EasySocial
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function createLikes(KomentoComment $comment)
	{
		if (!$this->exists()) {
			return false;
		}

		$params = $comment->getParams();
		$social = $params->get('social');

		if (!$social && !$social->stream) {
			return false;
		}

		$options = [
			'type' => 'komento.user.create', 
			'uid' => $social->stream, 
			'created_by' => $this->my->id
		];

		$like = ES::table('Likes');
		$exists = $like->load($options);

		if (!$exists) {
			$like->type = 'komento.user.create';
			$like->uid = $social->stream;
			$like->created_by = $this->my->id;
			$like->created = JFactory::getDate()->toSql();
			$like->stream_id = $social->stream;
			$like->store();
		}
	}

	/**
	 * Removes the likes record from EasySocial when a person unlikes the comment
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function removeLike(KomentoComment $comment)
	{
		if (!$this->exists()) {
			return false;
		}

		$params = $comment->getParams();
		$social = $params->get('social');

		if (!$social && !$social->stream) {
			return false;
		}

		$options = [
			'type' => 'komento.user.create', 
			'uid' => $social->stream, 
			'created_by' => $this->my->id
		];

		$like = ES::table('Likes');
		$exists = $like->load($options);

		if ($exists) {
			$like->delete();

			// Try to delete any existing stream item from EasySocial
			ES::stream()->delete($comment->id, 'komento', $comment->created_by, 'like');
		}
	}

	/**
	 * Notifies a list of names
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function notifyNames($names, $comment)
	{
		$app = $comment->getApplication();

		$options = [
			'uid' => $comment->id,
			'actor_id' => $this->my->id,
			'type' => 'komento',
			'url' => $comment->getPermalink(),
			'contentTitle' => $app->getContentTitle(),
			'owner' => $comment->created_by
		];

		$recipients = [];

		foreach ($names as $recipient) {
			$recipients[] = $recipient->id;
		}

		ES::notify('komento.mentions', $recipients, false, $options);
	}

	/**
	 * Notifies a list of users
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function notify($action, $comment)
	{
		if (!$this->exists() || !$this->config->get('notification_es_event_new_' . $action)) {
			return false;
		}

		$maxUserThreshold = 30;

		//NOTE: usergroup alwaya at the last.
		$targets = [];

		if ($action == 'comment') {
			$targets = ['author', 'participant', 'usergroup'];
		}

		if ($action == 'reply') {
			$targets = ['parent', 'author', 'participant', 'usergroup'];
		}

		if ($action == 'like') {
			$targets = ['owner', 'usergroup'];
		}

		// Nothing to notify
		if (!$targets) {
			return false;
		}

		$app = $comment->getApplication();

		$systemOptions = [
			'uid' => $comment->id,
			'actor_id' => $this->my->id,
			'type' => 'komento',
			'url' => $comment->getPermalink(),
			'contentTitle' => $app->getContentTitle(),
			'owner' => $comment->created_by
		];

		$socialApp = $this->getApp();

		if ($socialApp) {
			$systemOptions['app_id'] = $socialApp->id;
		}

		$tobeNotify = [];

		// We always do not want action user to get notified
		$tobeNotify[] = $this->my->id;
		$exludeIds[] = $this->my->id;

		$hasUserGroup = in_array('usergroup', $targets);

		if ($hasUserGroup) {
			// remove the last segment, which is usergroup. we will process usergroup differently.
			array_pop($targets);
		}

		// group the users
		foreach ($targets as $target) {
			$users = array_diff($this->getNotificationTarget($target, $action, $comment), $exludeIds);

			if ($target != 'participant') {
				$systemOptions['target'] = $target;
				ES::notify('komento.' . $action, $users, false, $systemOptions);
				$exludeIds = array_merge($exludeIds, $users);

			} else {
				$tobeNotify = $users;
				$exludeIds = array_merge($exludeIds, $users);
			}

		}

		if ($tobeNotify) {
			// if we reach here, means we are processing participients.
			$systemOptions['target'] = 'participant';

			// if the users is not exceeded the max, then we use normal way to send the notification to ES.
			// #233
			if (count($tobeNotify) <= $maxUserThreshold) {
				ES::notify('komento.' . $action, $tobeNotify, false, $systemOptions);
			} else {
				// we send as batch
				$this->notifyUsers('komento.' . $action, $tobeNotify, 'user', $systemOptions);
			}

			$exludeIds = array_merge($exludeIds, $tobeNotify);
		}

		// now we process the user groups as batch. #233
		if ($hasUserGroup) {
			$systemOptions['target'] = 'usergroup';

			// get the user group ids.
			$gids = $this->getNotificationTarget('usergroup', $action, $comment);

			if ($gids) {
				$this->notifyUsers('komento.' . $action, $gids, 'usergroup', $systemOptions, $exludeIds);
			}
		}
	}

	/**
	 * Notifies a list of users
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function notifyUsers($rules, $recipients, $recipientType, $systemOptions, $exclude = [])
	{
		$model = KT::model('easysocial');
		$model->notifyUsers($rules, $recipients, $recipientType, $systemOptions, $exclude);
	}

	/**
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getNotificationTarget($target, $action, $comment)
	{
		$ids = [];

		switch($target)
		{
			case 'usergroup':
				$gids = $this->config->get('notification_es_to_usergroup_' . $action);

				if (!empty($gids)) {
					if (!is_array($gids)) {
						$gids = explode(',', $gids);
					}

					$ids = $gids;
				}
			break;

			case 'author':

				if ($this->config->get('notification_es_to_author')) {
					$application = KT::loadApplication($comment->component)->load($comment->cid);

					$author = $application->getAuthorId();

					if (!empty($author)) {
						$ids = [$author];
					}
				}
			break;

			case 'parent':
				if (!empty($comment->parent_id)) {
					$parent = KT::getTable('comments');
					$state = $parent->load($comment->parent_id);

					if ($state && !empty($parent->created_by)) {
						$ids = [$parent->created_by];
					}
				}
			break;

			case 'owner':
				if (!empty($comment->created_by)) {
					$ids = [$comment->created_by];
				}
			break;

			case 'participant':
				if ($this->config->get('notification_es_to_participant')) {
					$options = [
						'component' => $comment->component,
						'cid' => $comment->cid,
						'noguest' => true,
						'state' => 1
					];

					$model = KT::model('comments');
					$ids = $model->getUsers($options);
				}
			break;
		}

		return $ids;
	}
}
