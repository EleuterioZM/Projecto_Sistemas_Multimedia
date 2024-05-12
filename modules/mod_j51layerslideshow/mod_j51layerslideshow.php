<?php
/**
* J51_LayerSlideshow
* Version       : 1.1
* Created by    : Joomla51
* Email         : info@joomla51.com
* URL           : www.joomla51.com
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

$helper = new modJ51LayerSlideshow();

$baseurl                = JURI::base();
$j51_moduleid           = $module->id;
$animateIn              = $params->get( 'animateIn' );
$animateOut             = $params->get( 'animateOut' );

$j51slideimages         = $params->get('j51slideimages');
$j51_slidetextwidth     = $params->get('j51_slidetextwidth', '700');
$j51_slidetextalign     = $params->get('j51_slidetextalign', 'center');
$j51_text_bg_color	    = $params->get('j51_text_bg_color', '');
$j51_title_color        = $params->get('j51_title_color');
$j51_title_tag          = $params->get('j51_title_tag', 'h2');
$j51_title_anim_default = $params->get('j51_title_anim_default', 'fadeIn');
$j51_text_color         = $params->get('j51_text_color');
$j51_text_tag           = $params->get('j51_text_tag', 'p');
$j51_caption_anim_default = $params->get('j51_caption_anim_default', 'fadeIn');
$autoplay 		        = $params->get('autoplay', 1);
$autoplaySpeed          = $params->get('autoplaySpeed', 4000);
$speed                  = $params->get('speed', 600);
$j51_max_height         = $params->get('j51_max_height');
$j51_header_overlap     = $params->get('j51_header_overlap');
$j51_title_breakpoint   = $params->get('j51_title_breakpoint', '0');
$j51_caption_breakpoint = $params->get('j51_caption_breakpoint', '440');
$j51_overflow_hidden    = $params->get('j51_overflow_hidden', 0);
$j51_anim_speed         = $params->get('j51_anim_speed', 1500);
$j51_title_delay        = $params->get('j51_title_delay', 0);
$j51_caption_delay      = $params->get('j51_caption_delay', 500);

$document = JFactory::getDocument();

require JModuleHelper::getLayoutPath('mod_j51layerslideshow', $params->get('layout', 'default'));
?>