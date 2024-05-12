<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

spl_autoload_register(function($className) {
	$parts = explode('\\', $className);

	if (count($parts) <= 1) {
		return;
	}

	// We only want to deal with Foundry namespaces
	if ($parts[0] !== 'Foundry') {
		return;
	}

	array_shift($parts);

	$folder = strtolower($parts[0]);
	$library = strtolower($parts[1]);

	$absolutePath = __DIR__ . '/' . $folder . '/' . $library . '.php';

	if (!file_exists($absolutePath)) {
		return;
	}

	require_once($absolutePath);
});
