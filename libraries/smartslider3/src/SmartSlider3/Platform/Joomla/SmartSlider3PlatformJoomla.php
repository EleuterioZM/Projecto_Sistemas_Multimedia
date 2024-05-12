<?php


namespace Nextend\SmartSlider3\Platform\Joomla;


use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Sanitize;
use Nextend\SmartSlider3\Platform\AbstractSmartSlider3Platform;

class SmartSlider3PlatformJoomla extends AbstractSmartSlider3Platform {

    public function start() {

        require_once(dirname(__FILE__) . '/compat.php');

        $this->initSanitize();
    }


    public function getAdminUrl() {

        return Uri::root() . 'administrator/index.php?option=com_smartslider3';
    }

    public function getAdminAjaxUrl() {

        return Uri::root() . 'administrator/index.php?option=com_smartslider3&nextendajax=1';
    }

    private function initSanitize() {
        Sanitize::set_allowed_tags();
    }
}