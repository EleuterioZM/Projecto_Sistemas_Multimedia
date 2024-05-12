<?php
defined('_JEXEC') or die('Restricted index access');

// BC
function j51BlockExists($tpl, $module)
{
	return (new \J51\Helper\BlockHelper())->blockExists($tpl, $module);
}

// BC
function j51Block($tpl, $module)
{
	return (new \J51\Helper\BlockHelper())->renderBlock($tpl, $module);
}

//COUNT MODULES IN CONTENTTOP - DECIDE WIDTH - COLLAPSE IF NECESSARY
$contenttop_counted = 0;
if ($this->countModules('contenttop-a')) {
	$contenttop_counted++;
}
if ($this->countModules('contenttop-b')) {
	$contenttop_counted++;
}
if ($this->countModules('contenttop-c')) {
	$contenttop_counted++;
}
if ($contenttop_counted == 3) {
	$contenttop_width = '33.3%';
} else if ($contenttop_counted == 2) {
	$contenttop_width = '49.9%';
} else if ($contenttop_counted == 1) {
	$contenttop_width = '100%';
}

//COUNT MODULES IN CONTENTBOTTOM - DECIDE WIDTH - COLLAPSE IF NECESSARY
$contentbottom_counted = 0;
if ($this->countModules('contentbottom-a')) {
	$contentbottom_counted++;
}
if ($this->countModules('contentbottom-b')) {
	$contentbottom_counted++;
}
if ($this->countModules('contentbottom-c')) {
	$contentbottom_counted++;
}
if ($contentbottom_counted == 3) {
	$contentbottom_width = '33.3%';
} else if ($contentbottom_counted == 2) {
	$contentbottom_width = '49.9%';
} else if ($contentbottom_counted == 1) {
	$contentbottom_width = '100%';
}
