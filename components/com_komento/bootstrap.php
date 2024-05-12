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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once(JPATH_ADMINISTRATOR . '/components/com_komento/constants.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_komento/includes/dependencies.php');
require_once(KOMENTO_HELPERS . '/document/document.php');
require_once(KOMENTO_HELPER);
require_once(KOMENTO_HELPERS . '/router/router.php');

// Load language here
// initially language is loaded in content plugin
// for custom integration that doesn't go through plugin, language is not loaded
// hence, language should be loaded in bootstrap

$lang = JFactory::getLanguage();
$path = JPATH_ROOT;

$app = JFactory::getApplication();

// Check if foundry enabled or not.
if (!KT::isFoundryEnabled()) {
	$message = 'Please ensure that the plugin <b>Foundry by Stackideas</b> is enabled on the site.';

	if ($app->isClient('administrator')) {
		$message .= ' Go to <a href="index.php?option=com_plugins&view=plugins&filter[search]=Foundry">Joomla! plugin manager</a>';
	}

	return KT::raiseWarning(404, JText::_($message));
}

// Ensure that Foundry is loaded
KT::initFoundry();

if (FH::isFromAdmin()) {
	$path .= '/administrator';
}

// Load English first as fallback
$config = KT::config();

if ($config->get('enable_language_fallback')) {
	$lang->load('com_komento', $path, 'en-GB', true);
}

$lang->load('com_komento', $path, $lang->getDefault(), true);
$lang->load('com_komento', $path, null, true);