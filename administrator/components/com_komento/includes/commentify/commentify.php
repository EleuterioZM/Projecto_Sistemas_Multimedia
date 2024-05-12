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

use Foundry\Libraries\Scripts;

class KomentoCommentify
{
	public function __construct($component)
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = KT::config();
		$this->component = $component;
		$this->adapter = KT::loadApplication($this->component);
	}

	/**
	 * Renders the ratings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRatings($cid)
	{
		$default = (object) [
			'total' => 0,
			'count' => 0
		];

		if (!$this->config->get('enable_ratings')) {
			return $default;
		}

		$model = KT::model('Comments');
		$ratings = $model->getOverallRatings($this->component, $cid);

		$default->total = $ratings->value;
		$default->count = (int) $ratings->total;

		return $default;
	}

	/**
	 * Determines if Komento should be rendered
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function shouldRender($context, $trigger)
	{
		if (FH::isFromAdmin()) {
			return false;
		}

		if ($this->adapter instanceof KomentoError || !$this->component) {
			return false;
		}

		// We verify context and trigger first before going into onBeforeLoad because onBeforeLoad already expects the article to be what Komento want to integrate
		$verified = $this->verifyContext($context, $this->adapter->getContext());

		if (!$verified) {
			return false;
		}

		// Verify if event trigger is correct
		$verified = $this->verifyEventTrigger($trigger, $this->adapter->getEventTrigger());

		if (!$verified) {
			return false;
		}


		return true;
	}

	/**
	 * Determines if the category is allowed to have comments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isCategoryAllowed($options, $eventTrigger, $context, $article, $params, $page)
	{
		// Explicitly enabled
		if ($options['enable'] === true) {
			return true;
		}

		$categories = $this->config->get('allowed_categories_' . $this->component . '_settings', '');

		if (!is_array($categories)) {
			$categories = explode(',', $categories);
		}

		$mode = $this->config->get('mode_categories_' . $this->component . '_settings');
		
		// Filter is based on selected categories
		if ($mode == 1) {
			if (empty($categories)) {
				return false;
			}

			// @task: For some reason $article->catid might not be set. If it it's not set, just return false.
			$catid = $this->adapter->getCategoryId();

			if (!$catid) {
				if (!$this->adapter->onRollBack($eventTrigger, $context, $article, $params, $page, $options)) {
					// raise error
				}
				return false;
			}

			if (!in_array($catid, $categories)) {
				if (!$this->adapter->onRollBack($eventTrigger, $context, $article, $params, $page, $options)) {
					// raise error
				}

				return false;
			}

			return true;
		}

		// Filter is based on except selected categories
		if ($mode == 2) {
			if (empty($categories)) {
				return true;
			}
	
			// For some reason $article->catid might not be set. If it it's not set, just return false.
			$catid = $this->adapter->getCategoryId();

			if (!$catid) {
				// raise error
				$this->adapter->onRollBack($eventTrigger, $context, $article, $params, $page, $options);
				return false;
			}

			if (in_array($catid , $categories)) {
				// raise error
				$this->adapter->onRollBack($eventTrigger, $context, $article, $params, $page, $options);

				return false;
			}

			return true;			
		}


		// Filter is based on no categories allowed
		if ($mode == 3) {
			return false;
		}

		// Default we allow everything else
		return true;
	}

	/**
	 * This is where the integration happens where Komento prepares the html output
	 * that can be incuded by the extension.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function render(&$article, $options = [])
	{
		$eventTrigger = FH::normalize($options, 'trigger', null);
		$context = FH::normalize($options, 'context', null);
		$params = FH::normalize($options, 'params', array());
		$page = FH::normalize($options, 'page', 0);
		$app = JFactory::getApplication();

		// Determines if Komento should be rendered in this context
		if (!$this->shouldRender($context, $eventTrigger)) {
			return false;
		}

		// Allow the plugin to do early initialization before anything else
		$this->adapter->init();

		// TODO: Allow string/int: see line 662
		// Sometimes people pass in $article as an array, we convert it to object
		if (is_array($article)) {
			$article = (object) $article;
		}

		// @trigger: onBeforeLoad
		// we do this checking before load because in some cases,
		// article is not an object and the article id might be missing.
		$continueRendering = $this->adapter->onBeforeLoad($eventTrigger, $context, $article, $params, $page, $options);

		if (!$continueRendering) {
			return false;
		}

		// Set the current component
		KT::setCurrentComponent($this->component);

		// Get all the configuration
		$input = $app->input;

		// Check if komento is enabled for the current extension
		if (!$this->config->get('enable_komento')) {

			// Special case for RedShop, we need to rollback the changes made on the article
			if ($this->component === 'com_redshop') {
				$this->adapter->onRollBack($eventTrigger, $context, $article, $params, $page, $options);
			}

			return false;
		}

		// Disable Komento in tmpl=component mode such as print mode
		if ($input->get('tmpl', '', 'string') === 'component') {
			return false;
		}

		// We accept $article as an int
		// For $article as a string, onBeforeLoad should already prepare the $article object properly
		$cid = $article;

		// Set cid based on application mapping keys because some component might have custom keys (not necessarily always $article-id)
		if (!is_string($article) && !is_int($article)) {
			$cid = $article->{$this->adapter->_map['id']};
		}

		// If we do not have the unique id, we should not proceed here
		if (empty($cid)) {
			return false;
		}

		$options['enable'] = FH::normalize($options, 'enable', false);
		$options['disable'] = FH::normalize($options, 'disable', false);
		$options['lock'] = FH::normalize($options, 'lock', false);

		// Process in-content parameters
		if ($this->adapter->processParameter($context)) {
			$this->processParameter($article, $options);
		}

		// Terminate if it's disabled
		if ($options['disable'] && !$this->adapter->onParameterDisabled($eventTrigger, $context, $article, $params, $page, $options)) {
			return false;
		}

		// Loading article infomation with defined get methods
		if (!$this->adapter->load($cid)) {
			return false;
		}

		// Perform category checks
		if (!$this->isCategoryAllowed($options, $eventTrigger, $context, $article, $params, $page)) {
			return false;
		}

		// 3rd party APIs might want to prevent loading of comments here
		if (!$this->adapter->onAfterLoad($eventTrigger, $context, $article, $params, $page, $options)) {
			return false;
		}

		// Send mail on page load
		if ($this->config->get('notification_sendmailonpageload')) {
			KT::mailer()->sendOnPageLoad();
		}

		// Clear captcha database
		if ($this->config->get('database_clearcaptchaonpageload')) {
			KT::clearCaptcha();
		}

		if ($this->config->get('layout_avatar_integration') === 'easysocial' && $this->config->get('easysocial_profile_popbox')) {
			KT::easysocial()->init();
		}

		/**********************************************/
		// Run Komento!

		// Unknown views
		if (!$this->adapter->isListingView() && !$this->adapter->isEntryView()) {
			return;
		}

		// Start collecting page objects.
		KT::document()->start();

		$commentsModel = KT::model('Comments');

		$return = false;
		$renderInListingView = true;

		if (!$this->config->get('layout_frontpage_comment') && !$this->config->get('layout_frontpage_hits') && !$this->config->get('layout_frontpage_preview')) {
			$renderInListingView = false;
		}

		// Listing view
		if ($this->adapter->isListingView() && $renderInListingView) {
			$return = $this->listings($cid, $article, $options);
		}

		// Entry view is where we render the comment html output
		if ($this->adapter->isEntryView()) {
			$return = $this->initEntry($cid, $article, $options);
		}

		KT::document()->end();

		return $return;
	}

	/**
	 * Renders the entry view
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function entry($cid, &$article, $options = [])
	{
		// Load necessary css and javascript files.
		KT::initialize();

		// Initialize the session time
		if (KT::cleantalk()->isEnabled()) {
			KT::session()->setTime();
		}

		$options['parentid'] = 0;
		$options['threaded'] = $this->config->get('enable_threaded');
		$options['loadreplies'] = true;

		// attach the replies count into the query.
		$options['showRepliesCount'] = true;
		$options['sticked'] = '0';
		$options['sort'] = FH::normalize($options, 'sort', $this->input->get('kmt-sort', $this->config->get('default_sort'), 'default'));
		$options['limit'] = FH::normalize($options, 'limit', $this->config->get('max_comments_per_page'));

		$profile = KT::user();
		$my	= JFactory::getUser();

		if (!$profile->allow('read_others_comment')) {
			$options['userid'] = $my->id;
		}

		$model = KT::model('Comments');

		// For pagebreak, we need to reset the limitstart so that every page will show all comments
		if ($this->config->get('pagebreak_load') === 'all') {
			$options['limitstart'] = 0;
		}

		$comments = [];
		$pinnedComments = [];
		$paginationCount = 0;

		if ($profile->allow('read_comment')) {
			$comments = $model->getComments($this->component, $cid, $options);
			$paginationCount = $model->getTotal();

			if ($comments) {
				$comments = KT::formatter('comment', $comments, $options);
			}

			// Retrieve pinned comment
			$pinnedOptions = [
				'sticked' => true, 
				'loadreplies' => true, 
				'sort' => 'default'
			];
			
			$pinnedComments = $model->getComments($this->component, $cid, $pinnedOptions);

			if ($pinnedComments) {
				$pinnedComments = KT::formatter('comment', $pinnedComments, $pinnedOptions);
			}
		}

		$contentLink = $this->adapter->getContentPermalink();

		// Determines if the headers should be shown in the comment form
		$showHeaders = false;

		if (($this->config->get('show_name') == 2 || $this->config->get('show_email') == 2 || $this->config->get('show_website') == 2) ||
			($my->guest && ($this->config->get('show_name') == 1 || $this->config->get('show_email') == 1 || $this->config->get('show_website') == 1))
			) {
			$showHeaders = true;
		}

		$showCaptcha = false;

		if ($this->config->get('antispam_captcha_enable')) {
			$captchaGroup = $this->config->get('show_captcha');

			if (!is_array($captchaGroup)) {
				$captchaGroup = explode(',', $captchaGroup);
			}

			$usergids = $profile->getUsergroups();

			foreach ($usergids as $gid) {

				if (in_array($gid, $captchaGroup)) {
					$showCaptcha = true;
					break;
				}
			}
		}

		$totalFields = 0;

		$showNameField = KT::form()->showField('show_name');
		$requireNameField = KT::form()->requireField('require_name');

		if ($showNameField) {
			$totalFields += 1;
		}

		$showEmailField = KT::form()->showField('show_email');
		$requireEmailField = KT::form()->requireField('require_email');

		if ($showEmailField) {
			$totalFields += 1;
		}

		$showWebsiteField = KT::form()->showField('show_website');
		$requireWebsiteField = KT::form()->requireField('require_website');

		if ($showWebsiteField) {
			$totalFields += 1;
		}

		$showTerms = false;
		$tncGroup = $this->config->get('show_tnc', '');

		if (!is_array($tncGroup)) {
			$tncGroup = explode(',', $tncGroup);
		}

		$usergids = $profile->getUsergroups();

		foreach ($usergids as $gid) {
			if (in_array($gid, $tncGroup)) {
				$showTerms = true;
				break;
			}
		}

		$showRss = (bool) $this->config->get('enable_rss');
		$showSubscribe = false;
		$subscriptionId = null;

		if ($this->config->get('enable_subscription') && (!$my->guest || $this->config->get('show_email') > 0) && !$this->config->get('subscription_auto')) {
			$showSubscribe = true;

			$subscriptionModel = KT::model('Subscription');

			// We can only track if the user is logged in
			if ($my->id) {
				$subscriptionId = $subscriptionModel->getSubscriptionId($this->component, $cid, $my->id);
			}
		}

		// Determines if the more button should be visible
		$showMoreButton = false;
		$moreStartCount = $this->config->get('max_comments_per_page');

		if ($profile->allow('read_comment') && $paginationCount) {
			$showMoreButton = true;

			if (isset($options['limitstart'])) {
				$moreStartCount = $options['limitstart'] + $this->config->get('max_comments_per_page');
			}

			if ($paginationCount <= $moreStartCount) {
				$showMoreButton = false;
			}
		}

		$loadMoreLink = false;

		if ($showMoreButton) {
			$loadMoreLink = '#comments_' . $moreStartCount;

			if (!FH::isJoomla4()) {
				$loadMoreLink = $contentLink . $loadMoreLink;
			}
		}

		// Active sort
		$activeSort = $this->input->get('kmt-sort', $this->config->get('default_sort'), 'cmd');

		$commentCount = 0;

		// Only try to get the count if there are comments, otherwise it would be pointless to fire a query only to know that there are no comments
		if ($comments) {
			$commentCount = $model->getCount($this->component, $cid);
		}

		// Retrieve authors in a comment
		$authors = false;

		if ($profile->allow('read_comment') && $this->config->get('enable_conversation_bar') && $this->config->get('layout_avatar_enable') && $commentCount > 0) {
			$authors = $model->getConversationBarAuthors($this->component, $cid);
		}

		// Double check this with Joomla's registration component
		$joomlaUserParams = JComponentHelper::getParams('com_users');
		$allowRegistration = $joomlaUserParams->get('allowUserRegistration') == '0' ? false : true;

		// Add support for different view form and listing
		$section = FH::normalize($options, 'section', false);

		$ratings = $this->getRatings($cid);

		$type = FH::normalize($options, 'type', 'inline');
		$returnURL = FH::normalize($options, 'returnURL', '');
		$namespace = 'site/structure/compact';

		if ($type === 'inline') {
			$namespace = 'site/structure/inline';
		}

		$liveNotification = FH::normalize($options, 'live_notification', $this->config->get('enable_live_notification'));

		$theme = KT::themes();
		$theme->set('totalFields', $totalFields);
		$theme->set('authors', $authors);
		$theme->set('activeSort', $activeSort);
		$theme->set('moreStartCount', $moreStartCount);
		$theme->set('showMoreButton', $showMoreButton);
		$theme->set('showSubscribe', $showSubscribe);
		$theme->set('showRss', $showRss);
		$theme->set('subscriptionId', $subscriptionId);
		$theme->set('showTerms', $showTerms);
		$theme->set('showCaptcha', $showCaptcha);
		$theme->set('showNameField', $showNameField);
		$theme->set('requireNameField', $requireNameField);
		$theme->set('showEmailField', $showEmailField);
		$theme->set('requireEmailField', $requireEmailField);
		$theme->set('showWebsiteField', $showWebsiteField);
		$theme->set('requireWebsiteField', $requireWebsiteField);
		$theme->set('showHeaders', $showHeaders);
		$theme->set('totalRating', $ratings->total);
		$theme->set('totalRatingCount', $ratings->count);
		$theme->set('component', $this->component);
		$theme->set('cid', $cid);
		$theme->set('comments', $comments);
		$theme->set('options', $options);
		$theme->set('componentHelper', $this->adapter);
		$theme->set('application', $this->adapter);
		$theme->set('commentCount', $commentCount);
		$theme->set('contentLink', $contentLink);
		$theme->set('allowRegistration', $allowRegistration);
		$theme->set('pinnedComments', $pinnedComments);
		$theme->set('namespace', $namespace);
		$theme->set('type', $type);
		$theme->set('liveNotification', $liveNotification);
		$theme->set('returnURL', $returnURL);
		$theme->set('loadMoreLink', $loadMoreLink);

		$html = $theme->output('site/structure/entry');

		return $html;
	}

	/**
	 * Renders the listings view
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function listings($cid, &$article, $options = [])
	{
		KT::initialize();

		$comments = [];
		$model = KT::model('Comments');

		// Comment previews for listings
		if ($this->config->get('layout_frontpage_preview')) {
			$commentOptions = [];

			$commentOptions['threaded'] = 0;
			$commentOptions['limit'] = $this->config->get('preview_count', '3');

			$previewSort = $this->config->get('preview_sort', 'latest');

			// since we are in the listing page, we need this sort value so that the sorting can be referenced by comment->getPermalink() method.
			$this->input->set('sort', $previewSort);

			$commentOptions['sort'] = $previewSort;
			$commentOptions['parentid'] = $this->config->get('preview_parent_only', false) ? 0 : 'all';
			$commentOptions['sticked'] = $this->config->get('preview_sticked_only', false) ? true : 'all';
			$commentOptions['showRepliesCount'] = false;

			// Since this is a listing view, we don't need to cater for pagination
			$commentOptions['itemListing'] = true;

			if ($previewSort === 'popular') {
				$comments = $model->getPopularComments($this->component, $cid, $commentOptions);
			} 

			if ($previewSort !== 'popular') {
				$comments = $model->getComments($this->component, $cid, $commentOptions);
			}

			if ($comments) {
				$comments = KT::formatter('comment', $comments, $commentOptions);
			}
		}

		// Determines if the read more button should be added by Komento
		$showReadmore = false;

		if ($this->config->get('layout_frontpage_readmore') && $this->config->get('layout_frontpage_readmore_button') !== 'joomla') {
			$showReadmore = true;

			if  ($this->component === 'com_content') {
				$articleParams = new JRegistry($article->params);

				if (!$articleParams->get('show_readmore') && !$article->readmore) {
					$showReadmore = false;
				}
			}
		}

		$commentCount = $model->getCount($this->component, $cid);
		$ratings = $this->getRatings($cid);

		$theme = KT::themes();
		$theme->set('cid', $cid);
		$theme->set('totalRatingCount', $ratings->count);
		$theme->set('totalRating', $ratings->total);
		$theme->set('showReadmore', $showReadmore);
		$theme->set('commentCount', $commentCount);
		$theme->set('component', $this->component);
		$theme->set('comments', $comments);
		$theme->set('article', $article);
		$theme->set('adapter', $this->adapter);
		$theme->set('uniqid', uniqid());

		// Backwards compatibility prior to 4.0
		$theme->set('application', $this->adapter);
		$theme->set('componentHelper', $this->adapter);

		$instantComment = $this->config->get('layout_frontpage_instant_comment');

		$theme->set('instantComment', $instantComment);

		if ($instantComment) {
			$entryType = $this->config->get('layout_frontpage_instant_comment_placement', 'right');

			// Render necessary scripts here.
			Scripts::load('perfect-scrollbar');
			Scripts::load('markitup');

			// Render prism if needed to
			if ($this->config->get('bbcode_code')) {
				Scripts::load('prism');
			}

			if ($this->config->get('enable_mention')) {
				Scripts::load('tribute');
			}

			// Do not enable live notifications on listing. #473
			$liveNotification = false;
			$options['live_notification'] = $liveNotification;

			$theme->set('type', $entryType);
			$theme->set('commentOptions', $options);
			$theme->set('liveNotification', $liveNotification);
		}

		$html = $theme->output('site/listings/default');

		return $this->adapter->onExecute($article, $html, 'listing', $options);
	}

	/**
	 * Processes params from an object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function processParameter(&$article, &$options)
	{
		// Retrieve user parameters e.g.
		// {KomentoDisable}, {KomentoLock}

		if (is_string($article)) {
			$text = &$article;
		} elseif (is_object($article)) {
			// adjust to standard format
			if (!isset($article->introtext)) {
				$article->introtext = '';
			}

			if (!isset($article->text)) {
				$article->text = '';
			}

			// We assign it to a temp variable to avoid using pass by reference
			$introtext = $article->introtext;
			$text = $article->text;

			if (!$text) {
				// this could be coming from other extension
				// Special case for Ohanah
				if (isset($article->description)) {
					$text = $article->description;
				}
			}
		} else {
			return;
		}

		// Determine whether the 3rd party component has passes in these default options so that system do not need to scan through these syntax from the content
		$isCommentEnableDefault = isset($options['enable']) && $options['enable'];
		$isCommentDisableDefault = isset($options['disable']) && $options['disable'];
		$isCommentLockDefault = isset($options['lock']) && $options['lock'];

		// Only proceed this if the value return false then need to check the content
		if (!$isCommentEnableDefault) {
			$options['disable'] = (FCJString::strpos($introtext, '{KomentoDisable}') !== false || FCJString::strpos($text, '{KomentoDisable}') !== false);			
		}

		if (!$isCommentDisableDefault) {
			$options['enable'] = (FCJString::strpos($introtext, '{KomentoEnable}') !== false || FCJString::strpos($text, '{KomentoEnable}') !== false);
		}

		if (!$isCommentLockDefault) {
			$options['lock'] = (FCJString::strpos($introtext, '{KomentoLock}') !== false || FCJString::strpos($text, '{KomentoLock}') !== false);
		}
		
		// Remove in-content parameters
		if (!empty($introtext)) {
			$introtext = FCJString::str_ireplace('{KomentoDisable}', '', $introtext);
			$introtext = FCJString::str_ireplace('{KomentoEnable}', '', $introtext);
			$introtext = FCJString::str_ireplace('{KomentoLock}', '', $introtext);
		}

		if (!empty($text)) {
			$text = FCJString::str_ireplace('{KomentoDisable}', '', $text);
			$text = FCJString::str_ireplace('{KomentoEnable}', '', $text);
			$text = FCJString::str_ireplace('{KomentoLock}', '', $text);
		}

		$article->introtext = $introtext;
		$article->text = $text;

		// Special case for Ohanah
		if (isset($article->description)) {
			$article->description = $text;
		}
	}

	/**
	 * Given the context and the application context, ensure that it is a valid context
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function verifyContext($context, $source)
	{
		$default = true;

		if (is_null($context)) {
			return $default;
		}

		if (empty($source)) {
			return false;
		}

		if (is_array($source)) {
			return in_array($context, $source);
		}

		if (is_string($source)) {
			return ($context === $source);
		}

		if (is_bool($source)) {
			return $source;
		}

		return $default;
	}

	/**
	 * Verifies event triggers to ensure that we are triggered correctly
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	public function verifyEventTrigger($trigger, $source)
	{
		$default = true;

		if (is_null($trigger)) {
			return $default;
		}

		if (empty($source)) {
			return false;
		}

		if (is_array($source)) {
			return in_array($trigger, $source);
		}

		if (is_string($source)) {
			return ($trigger === $source);
		}

		if (is_bool($source)) {
			return $source;
		}

		return $default;
	}

	/**
	 * Renders the initial entry view
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function initEntry($cid, &$article, $options = [])
	{
		// Load necessary css and javascript files.
		KT::initialize();

		// Initialize the session time
		if (KT::cleantalk()->isEnabled()) {
			KT::session()->setTime();
		}

		$options['parentid'] = 0;
		$options['threaded'] = $this->config->get('enable_threaded');
		$options['loadreplies'] = true;

		// attach the replies count into the query.
		$options['showRepliesCount'] = true;
		$options['sticked'] = '0';
		$options['sort'] = FH::normalize($options, 'sort', $this->input->get('kmt-sort', $this->config->get('default_sort'), 'default'));
		$options['limit'] = FH::normalize($options, 'limit', $this->config->get('max_comments_per_page'));

		$profile = KT::user();
		$my	= JFactory::getUser();

		if (!$profile->allow('read_others_comment')) {
			$options['userid'] = $my->id;
		}

		$model = KT::model('Comments');

		// For pagebreak, we need to reset the limitstart so that every page will show all comments
		if ($this->config->get('pagebreak_load') === 'all') {
			$options['limitstart'] = 0;
		}

		$commentCount = $model->getCount($this->component, $cid);

		// Add support for different view form and listing
		$section = FH::normalize($options, 'section', false);

		// Render necessary scripts here.
		Scripts::load('markitup');

		// Render prism if needed to
		if ($this->config->get('bbcode_code')) {
			Scripts::load('prism');
		}

		if ($this->config->get('enable_mention')) {
			Scripts::load('tribute');
		}

		$requestOptions = $this->input->getArray();

		$type = $this->config->get('layout_comment_placement', 'right');

		if ($type !== 'inline') {
			Scripts::load('perfect-scrollbar');
		}

		$liveNotification = FH::normalize($options, 'live_notification', $this->config->get('enable_live_notification'));
		$returnUrl = JURI::getInstance()->toString() . '#commentform';

		$comments = false;
		$loadFromAjax = true;

		// Determine if we want to immediately load the comments
		if ($this->config->get('load_initial_comment', false)) {
			$options['type'] = $type;
			$options['returnURL'] = $returnUrl;

			$comments = $this->entry($cid, $cid, $options);
			$loadFromAjax = false;
		}

		$theme = KT::themes();
		$theme->set('component', $this->component);
		$theme->set('cid', $cid);
		$theme->set('commentOptions', $options);
		$theme->set('componentHelper', $this->adapter);
		$theme->set('application', $this->adapter);
		$theme->set('commentCount', $commentCount);
		$theme->set('type', $type);
		$theme->set('requestOptions', $requestOptions);
		$theme->set('liveNotification', $liveNotification);
		$theme->set('loginReturn', $returnUrl);
		$theme->set('comments', $comments);
		$theme->set('loadFromAjax', $loadFromAjax);
		$theme->set('identifier', uniqid());

		$namespace = ($section ? $section : 'default');

		$output = $theme->output('site/structure/' . $namespace);

		return $this->adapter->onExecute($article, $output, 'entry', $options);
	}
}
