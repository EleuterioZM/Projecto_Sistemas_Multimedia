<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoActivity
{
	public function __construct()
	{
		$this->config = KT::config();
		$this->my = JFactory::getUser();
	}

	/**
	 * Process activities and 3rd party integrations
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function process($action, $comment)
	{
		if (!($comment instanceof KomentoComment)) {

			if (is_int($comment) || is_string($comment)) {
				$comment = KT::comment($comment);
			}

			if ($comment instanceof KomentoTableComments) {
				$comment = KT::comment($comment);
			}
		}

		// If comment isn't unpublished, we should skip this
		if ($action != 'remove' && !$comment->isPublished()) {
			return false;
		}

		$profile = KT::user();
		$application = KT::loadApplication($comment->component)->load($comment->cid);

		if ($application === false) {
			$application = KT::getErrorApplication($comment->component, $comment->cid);
		}

		// native activity
		if(($action == 'comment' && $this->config->get('activities_comment')) || ($action == 'reply' && $this->config->get('activities_reply')) || ($action == 'like' && $this->config->get('activities_like'))) {
			$this->addActivity($action, $comment->id, $profile->id);
		}

		if ($this->initJomsocial()) {
			$this->processJomsocialActivities($action, $comment, $profile, $application);
		}

		// Add aup
		if ($this->config->get('enable_aup')) {
			$this->processAupAlp('addAUP', $action, $comment, $application, $profile);
		}

		// Add AltaUserPoints
		if ($this->config->get('enable_alp')) {
			$this->processAupAlp('addALP', $action, $comment, $application, $profile);
		}

		// Add Discuss points
		if ($this->config->get('enable_discuss_points')) {
			$this->processEasydiscussPoints($action, $comment, $application, $profile);
		}

		$this->processEasysocial($action, $comment);
	}

	public function processEasysocial($action, $comment)
	{
		// Integrate with EasySocial points
		$easysocial = KT::easysocial();
		$easysocial->assignPoints($action, $comment, $this->my);
		$easysocial->assignBadge($action, $comment);
		$easysocial->notify($action, $comment);

		// If action is reply and sync comment is on, then we don't create a stream
		// If comment params->source is from com_easysocial, then we don't create a stream
		if (((in_array($action, array('comment', 'like')) && $this->config->get('enable_easysocial_stream_' . $action)) || $action === 'reply' && !$this->config->get('enable_easysocial_sync_comment'))
				&& (empty($comment->params->source) || $comment->params->source !== 'com_easysocial')) {
			$easysocial->createStream($action, $comment);
		}

		// Insert a new comment into easysocial since we need to synchronize the comments on komento with the stream
		$easysocial->syncComments($action, $comment);

		// Synchronize likes with easysocial
		if ($action == 'like' || $action == 'unlike') {
			$easysocial->syncLikes($action, $comment);
		}
	}

	public function processEasydiscussPoints($action, $comment, $application, $profile)
	{
		$author = $application->getAuthorId();
		$title = $application->getContentTitle();

		switch($action)
		{
			case 'comment':
				KT::addDiscussPoint('komento.add.comment', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_ADD_COMMENT', $title));
				KT::addDiscussPoint('komento.add.comment.article.author', $author, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_ADD_COMMENT_ARTICLE_AUTHOR', $title));
				break;

			case 'reply':
				KT::addDiscussPoint('komento.reply.comment', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_REPLY_COMMENT', $title));
				break;

			case 'like':
				KT::addDiscussPoint('komento.like.comment', $profile->id, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_LIKE_COMMENT', $title));
				KT::addDiscussPoint('komento.comment.liked', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_LIKED', $title));
				break;

			case 'unlike':
				KT::addDiscussPoint('komento.unlike.comment', $profile->id, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_UNLIKE_COMMENT', $title));
				KT::addDiscussPoint('komento.comment.unliked', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNLIKED', $title));
				break;
			case 'report':
				KT::addDiscussPoint('komento.report.comment', $profile->id, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_REPORT_COMMENT', $title));
				KT::addDiscussPoint('komento.comment.reported', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_REPORTED', $title));
				break;
			case 'unreport':
				KT::addDiscussPoint('komento.unreport.comment', $profile->id, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_UNREPORT_COMMENT', $title));
				KT::addDiscussPoint('komento.comment.unreported', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNREPORTED', $title));
				break;
			case 'stick':
				KT::addDiscussPoint('komento.comment.sticked', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_STICKED', $title));
				break;
			case 'unstick':
				KT::addDiscussPoint('komento.comment.unsticked', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_UNSTICKED', $title));
				break;
			case 'remove':
				KT::addDiscussPoint('komento.comment.removed', $comment->created_by, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_COMMENT_REMOVED', $title));
				KT::addDiscussPoint('komento.remove.comment.article.author', $author, JText::sprintf('COM_KOMENTO_DISCUSS_HISTORY_REMOVE_COMMENT_ARTICLE_AUTHOR', $title));
				break;
		}
	}

	public function processAupAlp($task, $action, $comment, $application, $profile)
	{
		$pagelink = $application->getContentPermalink();
		$permalink = $pagelink . '#' . $comment->id;
		$author = $application->getAuthorId();
		$title = $application->getContentTitle();

		switch($action)
		{
			case 'comment':
				KT::$task('plgaup_komento_post_comment', $comment->created_by, 'komento_post_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title));
				KT::$task('plgaup_komento_add_comment_author', $author, 'komento_post_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf('COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title));
				break;

			case 'reply':
				KT::$task('plgaup_komento_reply_comment', $comment->created_by, 'komento_reply_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_REPLY_COMMENT', $permalink, $title));
				break;

			case 'like':
				KT::$task('plgaup_komento_like_comment', $profile->id, 'komento_like_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_LIKE_COMMENT', $permalink, $title));
				KT::$task('plgaup_komento_comment_liked', $comment->created_by, 'komento_comment_liked_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_LIKED', $permalink, $title));
				break;

			case 'unlike':
				KT::$task('plgaup_komento_unlike_comment', $profile->id, 'komento_unlike_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_UNLIKE_COMMENT', $permalink, $title));
				KT::$task('plgaup_komento_comment_unliked', $comment->created_by, 'komento_comment_unliked_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_UNLIKED', $permalink, $title));
				break;
			case 'report':
				KT::$task('plgaup_komento_report_comment', $profile->id, 'komento_report_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_REPORT_COMMENT', $permalink, $title));
				KT::$task('plgaup_komento_comment_reported', $comment->created_by, 'komento_comment_reported_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_REPORTED', $permalink, $title));
				break;
			case 'unreport':
				KT::$task('plgaup_komento_unreport_comment', $profile->id, 'komento_unreport_comment_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_UNREPORT_COMMENT', $permalink, $title));
				KT::$task('plgaup_komento_comment_unreported', $comment->created_by, 'komento_comment_unreported_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_UNREPORTED', $permalink, $title));
				break;
			case 'stick':
				KT::$task('plgaup_komento_comment_sticked', $comment->created_by, 'komento_comment_sticked_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_STICKED', $permalink, $title));
				break;
			case 'unstick':
				KT::$task('plgaup_komento_comment_unsticked', $comment->created_by, 'komento_comment_unsticked_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_UNSTICKED', $permalink, $title));
				break;
			case 'remove':
				KT::$task('plgaup_komento_comment_removed', $comment->created_by, 'komento_comment_removed_' . $comment->id, JText::sprintf('COM_KOMENTO_AUP_COMMENT_REMOVED', $pagelink, $title));
				KT::$task('plgaup_komento_remove_comment_author', $author, 'komento_remove_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf('COM_KOMENTO_AUP_REMOVED_COMMENT_AUTHOR', $pagelink, $title));
				break;
		}
	}

	public function processJomsocialActivities($action, $comment, $profile, $application)
	{
		$author = $application->getAuthorId();

		// Add jomsocial activity
		if ($action == 'comment' && $this->config->get('jomsocial_enable_comment')) {
			$this->addJomSocialActivityComment($comment);
		}

		if ($action == 'reply' && $this->config->get('jomsocial_enable_reply')) {
			$this->addJomSocialActivityReply($comment);
		}

		if ($action == 'like' && $this->config->get('jomsocial_enable_like')) {
			$this->addJomSocialActivityLike($comment, $profile->id);
		}

		// remove jomsocial activity
		if ($action == 'remove' && $this->config->get('jomsocial_enable_comment')) {
			$this->removeJomSocialActivityComment($comment);
		}

		// Add jomsocial userpoints
		if ($this->config->get('jomsocial_enable_userpoints')) {
			switch($action)
			{
				case 'comment':
					KT::addJomSocialPoint('com_komento.comment.add');
					KT::addJomSocialPoint('com_komento.comment.add.author', $author);
					break;

				case 'reply':
					KT::addJomSocialPoint('com_komento.comment.reply');
					break;

				case 'like':
					KT::addJomSocialPoint('com_komento.comment.like');
					KT::addJomSocialPoint('com_komento.comment.liked', $comment->created_by);
					break;

				case 'unlike':
					KT::addJomSocialPoint('com_komento.comment.unlike');
					KT::addJomSocialPoint('com_komento.comment.unliked', $comment->created_by);
					break;

				case 'report':
					KT::addJomSocialPoint('com_komento.comment.report');
					KT::addJomSocialPoint('com_komento.comment.reported', $comment->created_by);
					break;

				case 'unreported':
					KT::addJomSocialPoint('com_komento.comment.unreport');
					KT::addJomSocialPoint('com_komento.comment.unreported', $comment->created_by);
					break;

				case 'stick':
					KT::addJomSocialPoint('com_komento.comment.sticked');
					break;

				case 'unstick':
					KT::addJomSocialPoint('com_komento.comment.unsticked');
					break;

				case 'remove':
					KT::addJomSocialPoint('com_komento.comment.removed');
					KT::addJomSocialPoint('com_komento.comment.removed.author', $author);
					break;
			}
		}
	}

	public function addActivity($type, $commentId, $uid)
	{
		$model = KT::model('Activity');
		return $model->add($type, $commentId, $uid);
	}

	public function initJomsocial()
	{
		$jsCoreFile	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		
		if (!JFile::exists($jsCoreFile)) {
			return false;
		}

		require_once($jsCoreFile);

		return true;
	}

	public static function addJomSocialActivity($options = array())
	{
		$defaultOptions = array(
			'comment' => '',
			'title' => '',
			'content' => '',
			'cmd' => '',
			'actor' => '',
			'target' => 0,
			'app' => '',
			'cid' => '',
			'comment_id' => '',
			'comment_type' => '',
			'like_id' => '',
			'like_type' => ''

		);

		$options = KT::mergeOptions($defaultOptions, $options);

		$config	= KT::config();

		$obj = (object) $options;

		// add JomSocial activities
		CFactory::load('libraries', 'activities');
		CActivityStream::add($obj);
	}

	public function removeJomSocialActivityComment($comment)
	{	
		$config	= KT::config();

		$isAdmin = FH::isFromAdmin();

		// If the delete comment was done from the backend, we need to manually delete it from database.
		// This is because jomsocial's activity model file in backend doesn't has removeActivity() function.
		if ($isAdmin) {
			
			$db = KT::db();
			$query  = 'DELETE FROM ' . $db->nameQuote('#__community_activities') . ' WHERE ' . $db->nameQuote('app') . '=' . $db->Quote('komento') 
					. ' AND ' . $db->nameQuote('cid') . '=' . $db->Quote($comment->id);

			$db->setQuery($query);
			$db->query();

		} else {
			CFactory::load('libraries', 'activities');
			CActivityStream::remove('komento', $comment->id);
		}

	}

	public function addJomSocialActivityComment($comment)
	{
		if (!is_object($comment)) {
			$comment = KT::comment($comment);
		}

		$comment->comment = FCJString::substr(strip_tags($comment->comment), 0, KT::config()->get('jomsocial_comment_length'));

		$options = array(
			'title' => JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_COMMENT_ADDED', $comment->getItemPermalink(), $comment->getItemTitle()),
			'content' => $comment->comment,
			'cmd' => 'komento.comment.add',
			'app' => 'komento',
			'cid' => $comment->id,
			'actor' => $comment->created_by,
			'comment_id' => $comment->id,
			'comment_type' => 'com_komento.comments',
			'like_id' => $comment->id,
			'like_type' => 'com_komento.likes'
		);

		self::addJomSocialActivity($options);
	}

	public function addJomSocialActivityReply($comment)
	{
		if (!is_object($comment)) {
			$comment = KT::comment($comment);
		}

		$comment->comment = FCJString::substr(strip_tags($comment->comment), 0, KT::config()->get('jomsocial_comment_length'));

		$parent = KT::comment($comment->parent_id);
		$parent = KT::formatter('comment', $parent, false);

		$options = array(
			'title' => JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_REPLY_ADDED', $parent->getPermalink(), $comment->getItemPermalink(), $comment->getItemTitle()),
			'content' => $comment->comment,
			'cmd' => 'komento.comment.reply',
			'app' => 'komento',
			'cid' => $comment->id,
			'actor' => $comment->created_by,
			'comment_id' => $comment->id,
			'comment_type' => 'com_komento.comments',
			'like_id' => $comment->id,
			'like_type' => 'com_komento.likes'
		);
		self::addJomSocialActivity($options);
	}

	public function addJomSocialActivityLike($comment, $uid)
	{
		if (!is_object($comment)) {
			$comment = KT::getComment($comment);
		}

		$comment->comment = FCJString::substr(strip_tags($comment->comment), 0, KT::getConfig()->get('jomsocial_comment_length'));

		$options = array(
			'title' => JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_LIKED_COMMENT', $comment->getPermalink(), $comment->getItemPermalink(), $comment->getItemTitle()),
			'content' => $comment->comment,
			'cmd' => 'komento.comment.like',
			'app' => 'komento',
			'cid' => $comment->id,
			'actor' => $uid,
			'comment_id' => $comment->id,
			'comment_type' => 'com_komento.comments',
			'like_id' => $comment->id,
			'like_type' => 'com_komento.likes'
		);
		self::addJomSocialActivity($options);
	}
}
