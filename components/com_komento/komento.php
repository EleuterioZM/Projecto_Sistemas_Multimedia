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

require_once(JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php');
require_once(JPATH_ROOT . '/components/com_komento/bootstrap.php');

$app = JFactory::getApplication();
$input = $app->input;

$task = $input->get('task', 'display', 'cmd');

if ($task == 'confirmSubscription') {

	$token = $input->get('token', '', 'cmd');
	$returnURL = $input->get('return', '', 'default');

	if (empty($token)) {
		echo JText::_('COM_KOMENTO_INVALID_TOKEN');
		exit;
	}

	$model = KT::model('subscription');
	$state = $model->confirmSubscription($token, $returnURL);
}

// We need the base controller
KT::import('site:/controllers/controller');

require_once(KT_ROOT . '/views/views.php');

// We treat the view as the controller. Load other controller if there is any.
$controller	= $input->get('controller', '', 'word');

if (!empty($controller)) {
	$controller	= FCJString::strtolower($controller);

	// Import controller
	$state = KT::import('site:/controllers/' . $controller);

	if (!$state) {
		throw FH::exception(JText::sprintf('Invalid controller %1$s', $controller), 500);
	}
}

// Listen for AJAX calls here
KT::ajax()->process();

// We need to process cronjobs when needed
$cron = $input->get('cron', false, 'bool');
$crondata = $input->get('crondata', false, 'bool');

if ($crondata) {
	$msg = KT::gdpr()->cron();

	echo $msg;
	exit;
}

if ($task === 'cron' || $cron) {
	KT::cron()->execute();
	exit;
}

$class = 'KomentoController' . FCJString::ucfirst($controller);

// Test if the object really exists in the current context
if (!class_exists($class)) {
	throw FH::exception(JText::sprintf('COM_KT_INVALID_CONTROLLER_CLASS_ERROR', $class), 500);
}

$controller = new $class();
$controller->execute($task);
$controller->redirect();