<?php
defined('_JEXEC') or die('Restricted index access');

$wa  = $this->getWebAssetManager();

$fontawesome_sw = $this->params->get('fontawesome_sw', 1);
$fontawesome_solid = $this->params->get('fontawesome_solid', 1);
$fontawesome_reg = $this->params->get('fontawesome_reg', 1);
$fontawesome_brands = $this->params->get('fontawesome_brands', 1);
$animatecss_sw = $this->params->get('animatecss_sw', 1);

if ($min_css) {
	$wa->useStyle('template.base');
	$wa->useStyle('template.bonsai.util.min');
	$wa->useStyle('template.nexus.min');
	$wa->useStyle('template.responsive.min');
} else {
	$wa->useStyle('template.base');
	$wa->useStyle('template.bonsai.util');
	$wa->useStyle('template.nexus');
	$wa->useStyle('template.responsive');
}

if ($animatecss_sw) {
	if ($min_css) {
		$wa->useStyle('template.animate.min');
	} else {
		$wa->useStyle('template.animate');
	}
}

if ($fontawesome_sw) {
	$wa->useStyle('fontawesome.joomla');
	$wa->useStyle('fontawesome.base');
	$wa->useStyle('fontawesome.shims');
	if ($fontawesome_reg) {
		$wa->useStyle('fontawesome.regular');
	}
	if ($fontawesome_brands) {
		$wa->useStyle('fontawesome.brands');
	}
	if ($fontawesome_solid) {
		$wa->useStyle('fontawesome.solid');
	}
}

require_once('fonts.php');
require_once('style.php');
require_once('overrides.php');
require_once('custom_css.php');
