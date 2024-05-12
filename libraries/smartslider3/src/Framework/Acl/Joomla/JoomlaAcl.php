<?php

namespace Nextend\Framework\Acl\Joomla;

use Joomla\CMS\Factory;
use Nextend\Framework\Acl\AbstractPlatformAcl;

class JoomlaAcl extends AbstractPlatformAcl {

    private $user = null;

    public function __construct() {

        $this->user = Factory::getUser();
    }

    public function authorise($action, $MVCHelper) {
        return $this->user->authorise(str_replace('_', '.', $action), 'com_smartslider3');
    }
}