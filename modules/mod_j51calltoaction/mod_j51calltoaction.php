<?php
/**
* J51_CallToAction
* Version		: 1.0
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;

require_once dirname(__FILE__) . '/helper.php';

// The application
$app = Factory::getApplication();

$document        = Factory::getDocument();
$user            = Factory::getUser();

$doc 						= JFactory::getDocument();
$j51_moduleid       		= $module->id;
$j51_items    				= $params->get( 'j51_items' );
$j51_text				    = $params->get( 'j51_text', '' );
$j51_layout				    = $params->get( 'j51_layout', 'col-t-b' );
$j51_align				    = $params->get( 'j51_align', 'center' );
$j51_margin_x				= $params->get( 'j51_margin_x', '0' );
$j51_margin_y				= $params->get( 'j51_margin_y', '20' );
$j51_col_breakpoint			= $params->get( 'j51_col_breakpoint', '759' );
$j51_bg_color				= $params->get( 'j51_bg_color', '' );
$j51_bg_image				= $params->get( 'j51_bg_image', '' );

require JModuleHelper::getLayoutPath('mod_j51calltoaction', $params->get('layout', 'default'));
?>