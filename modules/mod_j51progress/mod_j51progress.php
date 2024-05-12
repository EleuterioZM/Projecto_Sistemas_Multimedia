<?php
/**
* J51_Progress
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

require_once (dirname(__FILE__).DS.'helper.php');

// Define some variables
$j51_items    		    = $params->get( 'j51_items' );
$j51_type		        = htmlspecialchars($params->get( 'j51_type', 'default' ), ENT_COMPAT, 'UTF-8');
$j51_margin_x		    = htmlspecialchars($params->get( 'j51_margin_x' ), ENT_COMPAT, 'UTF-8');
$j51_margin_y		    = htmlspecialchars($params->get( 'j51_margin_y' ), ENT_COMPAT, 'UTF-8');
$j51_color			    = htmlspecialchars($params->get( 'j51_color' ), ENT_COMPAT, 'UTF-8');
$j51_bg_color		    = htmlspecialchars($params->get( 'j51_bg_color' ), ENT_COMPAT, 'UTF-8');
$j51_set			    = htmlspecialchars($params->get( 'j51_set' ), ENT_COMPAT, 'UTF-8');
$j51_size			    = htmlspecialchars($params->get( 'j51_size' ), ENT_COMPAT, 'UTF-8');
$j51_layout		    	= htmlspecialchars($params->get( 'j51_layout' ), ENT_COMPAT, 'UTF-8');
$j51_align			    = htmlspecialchars($params->get( 'j51_align' ), ENT_COMPAT, 'UTF-8');
$j51_bg_style			= htmlspecialchars($params->get( 'j51_bg_style', 'boxed' ), ENT_COMPAT, 'UTF-8');
$j51_bg_color			= htmlspecialchars($params->get( 'j51_bg_color', '#fff' ), ENT_COMPAT, 'UTF-8');
$j51_border_color	   	= htmlspecialchars($params->get( 'j51_border_color', '#fff' ), ENT_COMPAT, 'UTF-8');
$j51_border_size	   	= htmlspecialchars($params->get( 'j51_border_size', '2' ), ENT_COMPAT, 'UTF-8');
$j51_layout			   	= htmlspecialchars($params->get( 'j51_layout' ), ENT_COMPAT, 'UTF-8');
$j51_columns		   	= htmlspecialchars($params->get( 'j51_columns' ), ENT_COMPAT, 'UTF-8');
$j51_title_tag		   	= htmlspecialchars($params->get( 'j51_title_tag', 'h3' ), ENT_COMPAT, 'UTF-8');
$j51_title_color		= htmlspecialchars($params->get( 'j51_title_color' ), ENT_COMPAT, 'UTF-8');
$j51_value_color		= htmlspecialchars($params->get( 'j51_value_color' ), ENT_COMPAT, 'UTF-8');
$j51_enable_animation  	= htmlspecialchars($params->get( 'j51_enable_animation', 1 ), ENT_COMPAT, 'UTF-8');
$j51_animation_length  	= htmlspecialchars($params->get( 'j51_animation_length' ), ENT_COMPAT, 'UTF-8');
$j51_interval_length   	= htmlspecialchars($params->get( 'j51_interval_length' ), ENT_COMPAT, 'UTF-8');
$j51_progress_color     = htmlspecialchars($params->get( 'j51_progress_color' ), ENT_COMPAT, 'UTF-8');
$j51_progress_bg_color  = htmlspecialchars($params->get( 'j51_progress_bg_color', 'rgba(135,135,135,.3)'), ENT_COMPAT, 'UTF-8');
$j51_progress_height   	= htmlspecialchars($params->get( 'j51_progress_height', '5' ), ENT_COMPAT, 'UTF-8');
$j51_columns_tabl       = htmlspecialchars($params->get( 'j51_columns_tabl', '33.333%'), ENT_COMPAT, 'UTF-8');
$j51_columns_tabp       = htmlspecialchars($params->get( 'j51_columns_tabp', '33.333%'), ENT_COMPAT, 'UTF-8');
$j51_columns_mobl       = htmlspecialchars($params->get( 'j51_columns_mobl', '50%'), ENT_COMPAT, 'UTF-8');
$j51_columns_mobp       = htmlspecialchars($params->get( 'j51_columns_mobp', '100%'), ENT_COMPAT, 'UTF-8');
$j51_moduleid       	= $module->id;
$delay                  = 0;
$id                            = 1;
$dataArray                     = array();

// Pass data from PHP to JS
foreach ($j51_items as $item) {
    $dataObj = new stdClass();
    $dataObj->id = 'counter' . $j51_moduleid . 'i' . $id;
    $dataObj->number = 'number' . $j51_moduleid . 'i' . $id;
    $dataObj->counts = htmlspecialchars($item->j51_progress, ENT_COMPAT, 'UTF-8');
    $dataObj->delay = $delay;
    $dataObj->animation_length = $j51_animation_length / 1000;
    $dataArray[] = $dataObj;

    $id++;
    $delay = $delay + $j51_interval_length;
}

require JModuleHelper::getLayoutPath('mod_j51progress', $j51_type);