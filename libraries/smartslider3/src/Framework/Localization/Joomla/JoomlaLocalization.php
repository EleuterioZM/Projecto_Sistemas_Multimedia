<?php

namespace Nextend\Framework\Localization\Joomla;

use Joomla\CMS\Factory;
use Nextend\Framework\Localization\AbstractLocalization;
use Nextend\Framework\Localization\Joomla\Pomo\MO;
use Nextend\Framework\Localization\Joomla\Pomo\NOOP_Translations;

class JoomlaLocalization extends AbstractLocalization {

    public function getLocale() {

        $lang = Factory::getLanguage();

        return str_replace('-', '_', $lang->getTag());
    }

    public function createMo() {

        return new MO();
    }

    public function createNOOP_Translations() {

        return new NOOP_Translations();
    }
}