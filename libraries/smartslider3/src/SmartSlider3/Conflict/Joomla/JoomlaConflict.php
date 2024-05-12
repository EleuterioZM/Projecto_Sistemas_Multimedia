<?php


namespace Nextend\SmartSlider3\Conflict\Joomla;


use Nextend\SmartSlider3\Conflict\Conflict;
use Nextend\Framework\Settings;

class JoomlaConflict extends Conflict {

    protected function __construct() {
        parent::__construct();

        $this->testPluginJCHOptimize();
    }

    /**
     * JCH Optimize
     * @url https://extensions.joomla.org/extension/jch-optimize/
     */
    private function testPluginJCHOptimize() {
        if (defined('JCH_VERSION') && Settings::get('async-non-primary-css', 0)) {
            $this->displayConflict('JCH Optimize', n2_('JCH Optimize could have a conflict with Smart Slider\'s Global settings -> Framework settings -> Async Non-Primary CSS. If your Google fonts are not loading, turn this option off.'));
        }
    }

}