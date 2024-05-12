<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

// Register Convert Form namespace
JLoader::registerNamespace('ConvertForms', JPATH_ADMINISTRATOR . '/components/com_convertforms/ConvertForms', false, false, 'psr4');

// Ensure backwards compatibility with old class names
JLoader::registerAlias('ConvertFormsHelper',    '\\ConvertForms\\Helper');
JLoader::registerAlias('ConvertFormsService',   '\\ConvertForms\\Plugin');
JLoader::registerAlias('ConvertFormsSmartTags', '\\ConvertForms\\SmartTags');
JLoader::registerAlias('ConvertFormsAnalytics', '\\ConvertForms\\Analytics');

?>