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

define('FD_ROOT', JPATH_LIBRARIES . '/foundry');
define('FD_THEMES', FD_ROOT . '/themes');
define('FD_THEMES_OVERRIDES', JPATH_ROOT . '/templates/stackideas/foundry');
define('FD_MEDIA', JPATH_ROOT . '/media/foundry');

// URIs
define('FD_URI_MEDIA', rtrim(JURI::root(), '/') . '/media/foundry');
define('FD_URI_IMAGES', FD_URI_MEDIA . '/images');
define('FD_URI_CSS', FD_URI_MEDIA . '/css');

// Environment
define('FD_MODE', 'production');

define('FD_JROUTER_MODE_SEF', 1);

define('FD_SUCCESS', 'success');
define('FD_WARNING', 'warning');
define('FD_ERROR', 'error');
define('FD_INFO', 'info');
define('FD_COMPILER', 'http://compiler.stackideas.com');