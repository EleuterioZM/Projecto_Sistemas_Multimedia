<?php
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../..'));
define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once(JPATH_BASE . DS . 'includes' . DS . 'framework.php');

$root = str_replace('\\', '/', Joomla\CMS\Uri\Uri::root());
$root = str_replace('/plugins/system/smartslider3/apis/', '', $root);

header('Location: ' . $root . '/administrator/index.php?option=com_smartslider3&nextendcontroller=generator&nextendaction=finishauth&group=instagram&state=' . $_GET['state'] . '&code=' . $_GET['code']);
exit;