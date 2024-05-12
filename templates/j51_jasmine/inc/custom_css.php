<?php
defined('_JEXEC') or die('Restricted index access');

// Custom.css
$document->addStyleDeclaration($this->params->get('custom_css'));
if($this->params->get('customcss_sw') == "1") {
	$wa->useStyle('template.custom');
}

// Responsive Custom CSS
if($this->params->get('tabport_css') != "1") {
	$document->addStyleDeclaration('@media only screen and (min-width: 768px) and (max-width: 959px) {'.$this->params->get('tabport_css').'}');
}
if($this->params->get('mobland_css') != "1") {
	$document->addStyleDeclaration('@media only screen and ( max-width: 767px ) {'.$this->params->get('mobland_css').'}');
}
if($this->params->get('mobport_css') != "1") {
	$document->addStyleDeclaration('@media only screen and (max-width: 440px) {'.$this->params->get('mobport_css').'}');
}
