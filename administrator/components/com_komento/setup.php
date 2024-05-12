<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


$file = JPATH_ROOT . '/tmp/komento.installation';

$app = JFactory::getApplication();
$input = $app->input;
$my = JFactory::getUser();

// Cancel setup
$cancelSetup = $app->input->get('cancelSetup', false, 'bool');

if ($cancelSetup && JFile::exists($file)) {

	// Delete the tmp file
	JFile::delete($file);

	// Redirect
	return $app->redirect('index.php?option=com_komento');
}

$install = $input->get('setup', false, 'bool') || $input->get('launchInstaller', false, 'bool');

if ($install) {

	$obj = new stdClass();
	$obj->new = false;
	$obj->step = 1;
	$obj->status = 'installing';

	$contents = json_encode($obj);

	JFile::write($file, $contents);
}

// Check if there's a file initiated for installation
$installCompleted = $app->input->get('active') == 'complete';

if (JFile::exists($file) || $installCompleted) {
	require_once(dirname(__FILE__) . '/setup/bootstrap.php');
	exit;
}