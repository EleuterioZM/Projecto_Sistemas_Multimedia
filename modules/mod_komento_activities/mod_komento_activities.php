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

$config = KT::config();

// Load frontend styles
$stylesheet = KT::stylesheet('site', $config->get('layout_theme'));
$stylesheet->attach();

$type = [];

if ($params->get('includelikes')) {
	$type[] = 'like';
}

if ($params->get('includecomments')) {
	$type[] = 'comment';
}
if ($params->get('includereplies')) {
	$type[] = 'reply';
}

// If there is no type selected, there is nothing to show.
if (empty($type)) {
	return false;
}

$options = [
	'type' => $type,
	'limit' => $params->get('limit'),
	'component' => $params->get('component')
];

$model = KT::model('activity');
$results = $model->getUserActivities('all', $options);

$activities = [];

// Process the activity object
foreach ($results as $activity) {
	$comment = KT::comment($activity->comment_id);

	if (!$comment->id) {
		continue;
	}

	$activity->comment = $comment;

	$maxtitlelength = $params->get('maxtitlelength');

	$itemTitle = $activity->comment->getItemTitle();

	// trim title length
	if (FCJString::strlen($itemTitle) > $maxtitlelength) {
		$itemTitle = FCJString::substr($itemTitle, 0, $maxtitlelength) . '...';
	}

	$activity->comment->itemTitle = $itemTitle;
	$activity->author = KT::user($activity->uid);

	$activities[] = $activity;
}

if (empty($activities)) {
	return;
}

$fd = KT::fd();
$themes = KT::themes();

require(JModuleHelper::getLayoutPath('mod_komento_activities'));