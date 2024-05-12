<?php

namespace Nextend\Framework\Url\Joomla;

use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Url\AbstractPlatformUrl;

class JoomlaUrl extends AbstractPlatformUrl {

    private $fullUri;

    function __construct() {

        $this->siteUrl = Uri::root();

        $this->fullUri  = rtrim(Uri::root(), '/');
        $this->_baseuri = rtrim(Uri::root(true), '/');

        $this->_currentbase = $this->fullUri;

        $this->scheme = parse_url($this->fullUri, PHP_URL_SCHEME);
    }

    public function getFullUri() {

        return $this->fullUri;
    }

    public function ajaxUri($query = '') {
        return Uri::current();
    }
}