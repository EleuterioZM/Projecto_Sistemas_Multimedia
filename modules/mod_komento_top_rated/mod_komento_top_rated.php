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
require_once(__DIR__ . '/helper.php');

$helper = new modKomentoTopRated();

// Load languages
KT::loadLanguage();

// Initialise all data
$config = KT::config();

// If rating is not available, then return.
if (!$config->get('enable_ratings', false)) {
	return;
}

// Load frontend styles
$stylesheet = KT::stylesheet('site', $config->get('layout_theme'));
$stylesheet->attach();

$options = [
	'limit' => ($params->get('limit', 5) == 0) ? false : $params->get('limit', 5),
	'component' => $params->get('component', 'all')
];

$articles = $helper->getAggregatedRatingArticles($options);

if (empty($articles)) {
	return;
}

$fd = KT::fd();

require(JModuleHelper::getLayoutPath('mod_komento_top_rated'));