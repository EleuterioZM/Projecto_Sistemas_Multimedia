<?php

namespace Nextend\Framework\Translation\Joomla;

use Joomla\CMS\Factory;
use Nextend\Framework\Translation\AbstractTranslation;

class JoomlaTranslation extends AbstractTranslation {

    public function getLocale() {
        return Factory::getLanguage()
                       ->getTag();
    }
}