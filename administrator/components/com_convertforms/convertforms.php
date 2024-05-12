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

// Load Framework
if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
{
	throw new RuntimeException('Novarain Framework is not installed', 500);
}

$app = JFactory::getApplication();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_convertforms'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

use NRFramework\Functions;
use NRFramework\Extension;

// Load framework's and component's language files
Functions::loadLanguage();
Functions::loadLanguage('com_convertforms');
Functions::loadLanguage('plg_system_convertforms');

// Initialize Convert Forms Library
require_once JPATH_ADMINISTRATOR . '/components/com_convertforms/autoload.php';

// Check required extensions
if (!Extension::pluginIsEnabled('nrframework'))
{
	$app->enqueueMessage(JText::sprintf('NR_EXTENSION_REQUIRED', JText::_('COM_CONVERTFORMS'), JText::_('PLG_SYSTEM_NRFRAMEWORK')), 'error');
}

if (!Extension::pluginIsEnabled('convertforms'))
{
	$app->enqueueMessage(JText::sprintf('NR_EXTENSION_REQUIRED', JText::_('COM_CONVERTFORMS'), JText::_('PLG_SYSTEM_CONVERTFORMS')), 'error');
}

if (!Extension::componentIsEnabled('ajax'))
{
	$app->enqueueMessage(JText::sprintf('NR_EXTENSION_REQUIRED', JText::_('COM_CONVERTFORMS'), 'Ajax Interface'), 'error');
}

// Load component's CSS/JS files
ConvertForms\Helper::loadassets();

if (defined('nrJ4'))
{
	JHtml::stylesheet('plg_system_nrframework/joomla4.css', ['relative' => true, 'version' => 'auto']);
} else 
{
	JHtml::stylesheet('com_convertforms/joomla3.css', ['relative' => true, 'version' => 'auto']);
}

// Perform the Request task
$controller = JControllerLegacy::getInstance('ConvertForms');
$controller->execute($app->input->get('task'));
$controller->redirect();