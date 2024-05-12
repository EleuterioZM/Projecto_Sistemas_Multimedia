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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

$lib = JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php';


if (!JFile::exists($lib)) {
	return;
}

require_once($lib);

// Load languages
KT::loadLanguage();

// initialise all data
$profile = KT::getProfile();
$config = KT::config();

// Load frontend styles
$stylesheet = KT::stylesheet('site', $config->get('layout_theme'));
$stylesheet->attach();

$model = KT::model('comments');

$comments = '';
$limit = $params->get('limit');
$sort = $params->get('sort');
$options = [
	'threaded' => 0,
	'sort' => $sort,
	'limit' => $limit,
	'sticked' => $params->get('featuredOnly') ? 1 : 'all',
	'random' => $params->get('random'),
	'showRepliesCount' => 0
];

$component = $params->get('component');
$cid = [];
$filter = $params->get('filter');
$category = $params->get('category');
$articleId = $params->get('articleId');
$userId = $params->get('userId');
$maxCommentLength = $params->get('maxcommentlength');
$maxItemTitleLength = $params->get('maxitemtitlelength');

if ($component != 'all' && $filter == 'article') {

	$cid = explode(',', $articleId);

	if (count($cid) == 1) {
		$cid = $cid[0];
	}

} else if ($component != 'all' && $filter == 'category') {
	$application = KT::loadApplication($component);
	$cid = $application->getContentIds($category);

	if (count($cid) == 1) {
		$cid = $cid[0];
	}
} else {
	$cid = 'all';

	if ($filter == 'user') {

		$userId = explode(',', $userId);

		if (count($userId) == 1) {
			$userId = $userId[0];
		}
		$options['userid'] = $userId;
	}
}

$balance = $limit;
$tmpComments = [];

while ($balance > 0) {
	if ($sort != 'likes') {
		$comments = $model->getComments($component, $cid, $options);
	} else {
		$comments = $model->getPopularComments($component, $cid, $options);
	}

	// $comments = $model->getComments($component, $cid, $options);

	if (empty($comments)) {
		break;
	}

	$comments = KT::formatter('comment', $comments);

	foreach ($comments as $key => $comment) {
		$itemTitle = $comment->getItemTitle();

		// If the item state is not published, unset this comment
		if ($comment->getItemState() == 0) {
			continue;
		}

		if (FCJString::strlen($itemTitle) > $maxItemTitleLength) {
			$itemTitle = FCJString::substr($itemTitle, 0, $maxItemTitleLength) . '...';
		}

		$comment->itemTitle = $itemTitle;
		$tmpComments[] = $comment;
	}

	$balance = $balance - count($tmpComments);

	$options['limitstart'] = $limit;
	$options['limit'] = $balance;

	$limit = $limit + $limit;
}

if (empty($tmpComments)) {
	return;
}

$fd = KT::fd();
$themes = KT::themes();

require(JModuleHelper::getLayoutPath('mod_komento_comments'));