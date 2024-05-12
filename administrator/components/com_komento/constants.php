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
defined('_JEXEC') or die('Unauthorized Access');

// Environment paths
define('KT_ROOT', JPATH_ROOT . '/components/com_komento');
define('KT_ADMIN', JPATH_ADMINISTRATOR . '/components/com_komento');
define('KT_DEFAULTS', KT_ADMIN . '/defaults');
define('KT_ASSETS', KT_ROOT . '/assets');
define('KT_LIB', KT_ADMIN . '/includes');
define('KT_DOWNLOAD_PACKAGE', 'free');
define('KT_PLUGINS', KT_ROOT . '/komento_plugins');
define('KT_MEDIA', JPATH_ROOT . '/media/com_komento');
define('KT_THEMES', KT_ROOT . '/themes');
define('KT_PRODUCT_PAGE', 'https://stackideas.com/komento');

define('KOMENTO_CONTROLLERS', KT_ROOT . '/controllers');
define('KOMENTO_MODELS', KT_ADMIN . '/models');
define('KOMENTO_TABLES', KT_ADMIN . '/tables');
define('KOMENTO_HELPER', KT_ADMIN . '/includes/komento.php');
define('KOMENTO_ADMIN_UPDATES', KT_ADMIN . '/updates');

define('KOMENTO_JS_ROOT', KT_MEDIA . '/js');
define('KOMENTO_CSS_ROOT', KT_MEDIA . '/css');
define('KOMENTO_UPLOADS_ROOT', KT_MEDIA . '/uploads');
define('KOMENTO_BOOTSTRAP', KT_ROOT . '/bootstrap.php');
define('KOMENTO_SCRIPTS', KT_MEDIA . '/scripts');

// Languages
define('KOMENTO_UPDATER_LANGUAGE', 'https://services.stackideas.com/translations/komento');
define('KOMENTO_LANGUAGES_INSTALLED', 1);
define('KOMENTO_LANGUAGES_NOT_INSTALLED', 0);
define('KOMENTO_LANGUAGES_NEEDS_UPDATING', 3);

define('KOMENTO_TMP', KT_MEDIA . '/tmp');

// Color states for info messages
define('KOMENTO_MSG_SUCCESS', 'success');
define('KOMENTO_MSG_WARNING', 'warning');
define('KOMENTO_MSG_ERROR', 'danger');
define('KOMENTO_MSG_INFO', 'info');

define('KOMENTO_STATE_PUBLISHED', 1);
define('KOMENTO_STATE_UNPUBLISHED', 0);

// Action types
define('KOMENTO_ACTIONS_TYPE_REPORT', 'report');

// Comment Statuses
define('KT_COMMENT_UNPUBLISHED', 0);
define('KT_COMMENT_PUBLISHED', 1);
define('KT_COMMENT_MODERATE', 2);
define('KT_COMMENT_SPAM', 3);

// Komento subscriptions
define('KT_SUBSCRIPTION_PUBLISHED', 1);
define('KT_SUBSCRIPTION_PENDING', 0);

// Spam types
define('KT_SPAM_HONEYPOT', 'honeypot');
define('KT_SPAM_CLEANTALK', 'cleantalk');
define('KT_SPAM_AKISMET', 'akismet');

// Comment flag
define('KOMENTO_COMMENT_AKISMET_TRAINED', 1);

// CleanTalk Flag
define('KOMENTO_CLEANTALK_SPAM', 3);
define('KOMENTO_CLEANTALK_POSSIBLE_SPAM', 2);

// Updates server
define('KOMENTO_UPDATES_SERVER', 'stackideas.com');
define('KOMENTO_SERVICE_VERSION', 'https://stackideas.com/updater/manifests/komento');
define('KOMENTO_JUPDATE_SERVICE', 'https://stackideas.com/jupdates/manifest/komento');

// Themes
define('KOMENTO_THEME_BASE', 'kuro');

// Sessions
define('KOMENTO_SESSION_NAMESPACE', 'com_komento');

// Push notification max threshold
define('KOMENTO_PUSH_NOTIFICATION_THRESHOLD', 25);

// GDPR download request state
define('KOMENTO_DOWNLOAD_REQ_NEW', 0);
define('KOMENTO_DOWNLOAD_REQ_LOCKED', 1);
define('KOMENTO_DOWNLOAD_REQ_PROCESS', 2);
define('KOMENTO_DOWNLOAD_REQ_READY', 3);

// GDPR Download Folder
define('KOMENTO_GDPR_DOWNLOADS', KT_MEDIA . '/downloads');


// Legacy
define('KOMENTO_ROOT', KT_ROOT);
define('KOMENTO_ADMIN_ROOT', KT_ADMIN);
define('KOMENTO_ASSETS', KT_ASSETS);
define('KOMENTO_LIB', KT_LIB);
define('KOMENTO_COMMENT_UNPUBLISHED', KT_COMMENT_UNPUBLISHED);
define('KOMENTO_COMMENT_PUBLISHED', KT_COMMENT_PUBLISHED);
define('KOMENTO_COMMENT_MODERATE', KT_COMMENT_MODERATE);
define('KOMENTO_COMMENT_SPAM', KT_COMMENT_SPAM);
define('KOMENTO_PLUGINS', KT_PLUGINS);
define('KOMENTO_MEDIA', KT_MEDIA);
define('KOMENTO_THEMES', KT_THEMES);
define('KOMENTO_HELPERS', KT_LIB);
define('KOMENTO_CLASSES', KT_ROOT . '/classes');
