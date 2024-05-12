<?php

namespace Nextend\SmartSlider3\Platform\Joomla;

use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Request\Request;

class ImageFallback {

    static public function fallback($imageVars, $textVars = array(), $root = '') {
        $root   = self::fixRoot($root);
        $return = '';

        foreach ($imageVars as $image) {
            if (!empty($image)) {
                if (strpos($image, '#') !== false) {
                    $imageHelper = explode('#', $image);
                    $realImage   = $imageHelper[0];
                } else {
                    $realImage = $image;
                }
                $return = self::getImage($realImage, $root);
                if (!empty($return)) {
                    break;
                }
            }
        }

        if ($return == '' && !empty($textVars)) {
            foreach ($textVars as $text) {
                $imageInText = self::findImage($text);

                if (!empty($imageInText)) {
                    $return = self::getImage($imageInText, $root);

                    if ($return != '$/') {
                        break;
                    } else {
                        $return = '';
                    }
                }
            }
        }

        return $return;
    }

    static public function fixRoot($root) {
        if (substr($root, 0, 5) != 'http:' && substr($root, 0, 6) != 'https:') {
            $root = self::siteURL();
        }

        return self::removeSlashes($root);
    }

    static public function getImage($image, $root) {
        $imageUrl = self::httpLink($image, $root);
        if (self::isExternal($imageUrl) || self::imageUrlExists($imageUrl)) {
            return $imageUrl;
        } else {
            return '';
        }
    }

    static public function findImage($s) {
        preg_match_all('/(<img.*?src=[\'"](.*?)[\'"][^>]*>)|(background(-image)??\s*?:.*?url\((["|\']?)?(.+?)(["|\']?)?\))/i', $s, $r);
        if (isset($r[2]) && !empty($r[2][0])) {
            $s = $r[2][0];
        } else if (isset($r[6]) && !empty($r[6][0])) {
            $s = trim($r[6][0], "'\" \t\n\r\0\x0B");
        } else {
            $s = '';
        }

        return $s;
    }

    static public function removeSlashes($text, $right = true) {
        if ($right) {
            return rtrim($text, '/\\');
        } else {
            return ltrim($text, '/\\');
        }
    }

    static public function siteURL() {
        return Uri::root(false);
    }

    static public function isExternal($url) {
        $url = str_replace(array(
            'http:',
            'https:',
            '//',
            '\\\\'
        ), '', $url);

        $domain = Request::$SERVER->getVar('HTTP_HOST');

        return !(substr($url, 0, strlen($domain)) === $domain);
    }

    static public function httpLink($image, $root) {
        if (substr($image, 0, 5) != 'http:' && substr($image, 0, 6) != 'https:' && substr($image, 0, 2) != '//' && substr($image, 0, 2) != '\\\\') {
            return $root . '/' . self::removeSlashes($image, false);
        } else {
            return $image;
        }
    }

    static public function imageUrlExists($imageUrl) {
        if (substr($imageUrl, 0, 2) == '//' || substr($imageUrl, 0, 2) == '\\\\') {
            $imageUrl = (strtolower(Request::$SERVER->getCmd('HTTPS', 'off')) != 'off' ? "https:" : "http:") . $imageUrl;
        }

        return Filesystem::existsFile(Filesystem::absoluteURLToPath(urldecode($imageUrl)));
    }
}