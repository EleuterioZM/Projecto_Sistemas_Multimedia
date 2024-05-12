<?php

namespace Nextend\Framework\Form\Joomla;

use Joomla\CMS\Session\Session;
use Nextend\Framework\Form\Base\PlatformFormBase;

class PlatformForm extends PlatformFormBase {

    public function tokenize() {
        return '<input type="hidden" name="' . Session::getFormToken() . '" value="1">';
    }

    public function tokenizeUrl() {
        $a                           = array();
        $a[Session::getFormToken()] = 1;

        return $a;
    }

    public function checkToken() {
        return Session::checkToken() || Session::checkToken('get');
    }
}