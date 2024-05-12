<?php


namespace Nextend\SmartSlider3\Slider\Joomla;


use Nextend\SmartSlider3\Platform\Joomla\JoomlaShim;
use Nextend\SmartSlider3\Slider\Base\PlatformSliderBase;
use stdClass;

class PlatformSlider extends PlatformSliderBase {

    public $_module;

    public function addCMSFunctions($text) {

        $params        = new stdclass();
        $article       = new stdClass;
        $article->text = '<div>' . $text . '</div>';

        $data = array(
            'mod_smartslider',
            &$article,
            &$params,
            0
        );

        JoomlaShim::triggerOnContentPrepare($data);

        return $article->text;
    }
}