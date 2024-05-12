<?php

namespace Nextend\Security\Joomla;

use Nextend\Framework\Pattern\SingletonTrait;

class JoomlaSecurity {

    use SingletonTrait;

    protected function init() {
        include_once(dirname(__FILE__) . '/Common.php');
        include_once(dirname(__FILE__) . '/Escape.php');
    }
}