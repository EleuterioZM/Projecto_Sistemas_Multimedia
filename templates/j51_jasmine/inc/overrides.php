<?php
defined('_JEXEC') or die('Restricted index access');

// Module Overrides
$moduleoverrides = $this->params->get('moduleoverrides');
if (!empty($moduleoverrides)) {
foreach ($moduleoverrides as $item) : if($item->module_position != "0") :

if (!empty($item->module_bg_image)) {
$document->addStyleDeclaration('
.'.$item->module_position.' .module_surround {
	background-image: url('.$this->baseurl.'/'.$item->module_bg_image.');
	background-position: 50% 50%;
}');
}
if (!empty($item->module_bg_color)) {
$document->addStyleDeclaration('
.'.$item->module_position.' .module_surround {
	background-color: '.$item->module_bg_color.';
}');
}
if (!empty($item->module_text_color)) {
$document->addStyleDeclaration('
.'.$item->module_position.' {
	color: '.$item->module_text_color.' !important;
}');
}
if (!empty($item->module_title_color)) {
$document->addStyleDeclaration('
.'.$item->module_position.' .module_header h3,
.'.$item->module_position.' h1,
.'.$item->module_position.' h2,
.'.$item->module_position.' h3,
.'.$item->module_position.' h4,
.'.$item->module_position.' h5,
.'.$item->module_position.' h6 {
	color: '.$item->module_title_color.' !important;
}');
}
if (!empty($item->module_button_color)) {
$document->addStyleDeclaration('
.'.$item->module_position.' .btn {
	background-color: '.$item->module_button_color.'
}');
} 
if (!empty($item->module_padding)) {
$document->addStyleDeclaration('
.'.$item->module_position.' .module_surround {
	padding: '.$item->module_padding.'px !important;
}');
}

if (!($item->module_padding_top == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {padding-top: '.$item->module_padding_top.'px !important;}}');
}
if (!($item->module_padding_right == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {padding-right: '.$item->module_padding_right.'px !important;}}');
}
if (!($item->module_padding_bottom == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {padding-bottom: '.$item->module_padding_bottom.'px !important;}}');
}
if (!($item->module_padding_left == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {padding-left: '.$item->module_padding_left.'px !important;}}');
}

if (!($item->module_margin_top == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {margin-top: '.$item->module_margin_top.'px !important;}}');
}
if (!($item->module_margin_right == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {margin-right: '.$item->module_margin_right.'px !important;}}');
}
if (!($item->module_margin_bottom == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {margin-bottom: '.$item->module_margin_bottom.'px !important;}}');
}
if (!($item->module_margin_left == NULL)) {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) {.'.$item->module_position.' .module_surround {margin-left: '.$item->module_margin_left.'px !important;}}');
}

endif;
endforeach;
}
