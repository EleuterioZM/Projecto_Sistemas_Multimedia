<?php

use Nextend\SmartSlider3\Install\Install;
use Nextend\SmartSlider3\Platform\Joomla\Plugin\PluginSmartSlider3;
use Nextend\SmartSlider3\Settings;
use Nextend\SmartSlider3\SmartSlider3Info;

defined('_JEXEC') or die;

jimport("smartslider3.joomla");

if (class_exists('\Nextend\SmartSlider3\Platform\Joomla\Plugin\PluginSmartSlider3')) {
    class_alias(PluginSmartSlider3::class, 'plgSystemSmartSlider3');
}

if (Settings::get('n2_ss3_version') != SmartSlider3Info::$completeVersion) {
    Install::install();
}