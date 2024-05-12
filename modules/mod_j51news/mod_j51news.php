<?php
/**
* J51_News
* Version		: 1.0
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

$list 			= ModJ51NewsHelper::getList($params);
$baseurl 		= JURI::base();
$countcol 		= $params->get('countcol', 2);
$show_img 		= $params->get('show_img', 1);
$show_date 		= $params->get('show_date', 1);
$show_title 	= $params->get('show_title', 1);
$show_category 	= $params->get('show_category', 1);
$title_tag 	    = $params->get('title_tag', 'h3');
$show_text 		= $params->get('show_text', 1);
$text_tag 	    = $params->get('text_tag', 'p');
$show_button 	= $params->get('show_button', 1);
$length_text 	= $params->get('length_text', 300);
$date_format 	= $params->get('date_format','d.m.Y');
$columns 		= $params->get('columns', 100);
$masonry 		= $params->get('masonry', 0);
$layout_type    = $params->get('layout_type', 'grid');
$j51_trans_speed = $params->get('j51_trans_speed', 1000);
$j51_autoplay   = $params->get('j51_autoplay', 'true');
$j51_autoplay_delay = $params->get('j51_autoplay_delay', 3000);
$item_margin_x 	= $params->get('item_margin_x', 10);
$item_margin_y 	= $params->get('item_margin_y', 10);
$item_button 	= $params->get('item_button');
$columns_tabl	= $params->get( 'columns_tabl', 3);
$columns_tabp	= $params->get( 'columns_tabp', 2);
$columns_mobl	= $params->get( 'columns_mobl', 1);
$columns_mobp	= $params->get( 'columns_mobp', 1);
$image_width	= $params->get( 'image_width', '50%');
$max_width		= $params->get( 'max_width');
$overlay_type 	= $params->get( 'overlay_type', 'overlay-fade-out');
$j51_news_layout = $params->get( 'j51_news_layout', 'col-i-c');
$j51_moduleid 	= $module->id;
$title_color    = $params->get( 'title_color', '');
$text_color     = $params->get( 'text_color', '');
$bg_color       = $params->get( 'bg_color', '');
$svg_code       = $params->get( 'svg_code', '');

switch ($columns) {
    case 100:
        $columns_num = 1;
        break;
    case 50:
        $columns_num = 2;
        break;
    case 33.33:
        $columns_num = 3;
        break;
    case 25:
        $columns_num = 4;
        break;
    case 20:
        $columns_num = 5;
        break;
    case 16.5:
        $columns_num = 6;
        break;
}

require JModuleHelper::getLayoutPath('mod_j51news', $params->get('layout', 'default'));
?>
