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

require_once(__DIR__ . '/setup.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php');

if (!$my->authorise('core.manage', 'com_komento')) {
	$app->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return $app->close();
}

// Check if foundry enabled or not.
if (!KT::isFoundryEnabled()) {
	return KT::raiseWarning(404, JText::_('Please ensure that the plugin <b>Foundry by Stackideas</b> is enabled on the site. Go to <a href="index.php?option=com_plugins&view=plugins&filter[search]=Foundry">Joomla! plugin manager</a>'));
}

KT::checkEnvironment();

// Include the base controller
require_once(KOMENTO_ADMIN_ROOT . '/controllers/controller.php');

KT::document()->start();

KT::import('admin:/views/views');

KT::ajax()->process();

// We treat the view as the controller. Load other controller if there is any.
$controller = $input->get('controller', '', 'word');
$task = $input->get('task', 'display', 'cmd');

if (!empty($controller)) {
	$controller = FCJString::strtolower($controller);

	require_once(KOMENTO_ADMIN_ROOT . '/controllers/' . $controller . '.php');
}

$class = 'KomentoController' . FCJString::ucfirst($controller);

// Test if the object really exists in the current context
if (!class_exists($class)) {
	throw FH::exception(JText::sprintf('COM_KT_INVALID_CONTROLLER_CLASS_ERROR', $class), 500);
}

$controller	= new $class();
$controller->execute($task);
$controller->redirect();

KT::document()->end();