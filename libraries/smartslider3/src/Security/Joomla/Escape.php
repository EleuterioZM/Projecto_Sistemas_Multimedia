<?php

use Nextend\Framework\Platform\Platform;

/**
 * Escapes single quotes, `"`, `<`, `>`, `&`, and fixes line endings.
 *
 * Escapes text strings for echoing in JS. It is intended to be used for inline JS
 * (in a tag attribute, for example `onclick="..."`). Note that the strings have to
 * be in single quotes. The {@see 'js_escape'} filter is also applied here.
 *
 * @param string $text The text to be escaped.
 *
 * @return string Escaped text.
 * @since 2.8.0
 *
 */
if (!function_exists('esc_js')) {
    function esc_js($text) {
        $safe_text = wp_check_invalid_utf8($text);
        $safe_text = _wp_specialchars($safe_text, ENT_COMPAT);
        $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
        $safe_text = str_replace("\r", '', $safe_text);
        $safe_text = str_replace("\n", '\\n', addslashes($safe_text));

        return $safe_text;
    }
}

/**
 * Escaping for HTML blocks.
 *
 * @param string $text
 *
 * @return string
 * @since 2.8.0
 *
 */
if (!function_exists('esc_html')) {
    function esc_html($text) {
        $safe_text = wp_check_invalid_utf8($text);
        $safe_text = _wp_specialchars($safe_text, ENT_QUOTES);

        return $safe_text;
    }
}

/**
 * Escaping for HTML attributes.
 *
 * @param string $text
 *
 * @return string
 * @since 2.8.0
 *
 */
if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        $safe_text = wp_check_invalid_utf8($text);
        $safe_text = _wp_specialchars($safe_text, ENT_QUOTES);

        return $safe_text;
    }
}

/**
 * Escaping for textarea values.
 *
 * @param string $text
 *
 * @return string
 * @since 3.1.0
 *
 */
if (!function_exists('esc_textarea')) {
    function esc_textarea($text) {
        return htmlspecialchars($text, ENT_QUOTES, Platform::getCharset());
    }
}

/**
 * Escaping for XML blocks.
 *
 * @param string $text Text to escape.
 *
 * @return string Escaped text.
 * @since 5.5.0
 *
 */
if (!function_exists('esc_xml')) {
    function esc_xml($text) {
        $safe_text = wp_check_invalid_utf8($text);

        $cdata_regex = '\<\!\[CDATA\[.*?\]\]\>';
        $regex       = <<<EOF
/
	(?=.*?{$cdata_regex})                 # lookahead that will match anything followed by a CDATA Section
	(?<non_cdata_followed_by_cdata>(.*?)) # the "anything" matched by the lookahead
	(?<cdata>({$cdata_regex}))            # the CDATA Section matched by the lookahead

|	                                      # alternative

	(?<non_cdata>(.*))                    # non-CDATA Section
/sx
EOF;

        $safe_text = (string)preg_replace_callback($regex, static function ($matches) {
            if (!isset($matches[0])) {
                return '';
            }

            if (isset($matches['non_cdata'])) {
                // escape HTML entities in the non-CDATA Section.
                return _wp_specialchars($matches['non_cdata'], ENT_XML1);
            }

            // Return the CDATA Section unchanged, escape HTML entities in the rest.
            return _wp_specialchars($matches['non_cdata_followed_by_cdata'], ENT_XML1) . $matches['cdata'];
        }, $safe_text);

        return $safe_text;
    }
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The {@see 'clean_url'} filter
 * is applied to the returned cleaned URL.
 *
 * @param string   $url       The URL to be cleaned.
 * @param string[] $protocols Optional. An array of acceptable protocols.
 *                            Defaults to return value of wp_allowed_protocols().
 * @param string   $_context  Private. Use esc_url_raw() for database usage.
 *
 * @return string The cleaned URL after the {@see 'clean_url'} filter is applied.
 *                An empty string is returned if `$url` specifies a protocol other than
 *                those in `$protocols`, or if `$url` contains an empty string.
 * @since 2.8.0
 *
 */
if (!function_exists('esc_url')) {
    function esc_url($url, $protocols = null, $_context = 'display') {
        $original_url = $url;

        if ('' === $url) {
            return $url;
        }

        $url = str_replace(' ', '%20', ltrim($url));
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);

        if ('' === $url) {
            return $url;
        }

        if (0 !== stripos($url, 'mailto:')) {
            $strip = array(
                '%0d',
                '%0a',
                '%0D',
                '%0A'
            );
            $url   = _deep_replace($strip, $url);
        }

        $url = str_replace(';//', '://', $url);
        /*
         * If the URL doesn't appear to contain a scheme, we presume
         * it needs http:// prepended (unless it's a relative link
         * starting with /, # or ?, or a PHP file).
         */
        if (strpos($url, ':') === false && !in_array($url[0], array(
                '/',
                '#',
                '?'
            ), true) && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
            $url = 'http://' . $url;
        }

        // Replace ampersands and single quotes only when displaying.
        if ('display' === $_context) {
            $url = wp_kses_normalize_entities($url);
            $url = str_replace('&amp;', '&#038;', $url);
            $url = str_replace("'", '&#039;', $url);
        }

        if ((false !== strpos($url, '[')) || (false !== strpos($url, ']'))) {

            $parsed = wp_parse_url($url);
            $front  = '';

            if (isset($parsed['scheme'])) {
                $front .= $parsed['scheme'] . '://';
            } elseif ('/' === $url[0]) {
                $front .= '//';
            }

            if (isset($parsed['user'])) {
                $front .= $parsed['user'];
            }

            if (isset($parsed['pass'])) {
                $front .= ':' . $parsed['pass'];
            }

            if (isset($parsed['user']) || isset($parsed['pass'])) {
                $front .= '@';
            }

            if (isset($parsed['host'])) {
                $front .= $parsed['host'];
            }

            if (isset($parsed['port'])) {
                $front .= ':' . $parsed['port'];
            }

            $end_dirty = str_replace($front, '', $url);
            $end_clean = str_replace(array(
                '[',
                ']'
            ), array(
                '%5B',
                '%5D'
            ), $end_dirty);
            $url       = str_replace($end_dirty, $end_clean, $url);

        }

        if ('/' === $url[0]) {
            $good_protocol_url = $url;
        } else {
            if (!is_array($protocols)) {
                $protocols = wp_allowed_protocols();
            }
            $good_protocol_url = wp_kses_bad_protocol($url, $protocols);
            if (strtolower($good_protocol_url) != strtolower($url)) {
                return '';
            }
        }

        /**
         * Filters a string cleaned and escaped for output as a URL.
         *
         * @param string $good_protocol_url The cleaned URL to be returned.
         * @param string $original_url      The URL prior to cleaning.
         * @param string $_context          If 'display', replace ampersands and single quotes only.
         *
         * @since 2.3.0
         *
         */
        return $good_protocol_url;
    }
}

/**
 * Performs a deep string replace operation to ensure the values in $search are no longer present.
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @param string|array $search  The value being searched for, otherwise known as the needle.
 *                              An array may be used to designate multiple needles.
 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
 *
 * @return string The string with the replaced values.
 * @since  2.8.1
 * @access private
 *
 */
if (!function_exists('_deep_replace')) {
    function _deep_replace($search, $subject) {
        $subject = (string)$subject;

        $count = 1;
        while ($count) {
            $subject = str_replace($search, '', $subject, $count);
        }

        return $subject;
    }
}

/**
 * A wrapper for PHP's parse_url() function that handles consistency in the return values
 * across PHP versions.
 *
 * PHP 5.4.7 expanded parse_url()'s ability to handle non-absolute URLs, including
 * schemeless and relative URLs with "://" in the path. This function works around
 * those limitations providing a standard output on PHP 5.2~5.4+.
 *
 * Secondly, across various PHP versions, schemeless URLs containing a ":" in the query
 * are being handled inconsistently. This function works around those differences as well.
 *
 * @param string $url       The URL to parse.
 * @param int    $component The specific component to retrieve. Use one of the PHP
 *                          predefined constants to specify which one.
 *                          Defaults to -1 (= return all parts as an array).
 *
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 * @since 4.4.0
 * @since 4.7.0 The `$component` parameter was added for parity with PHP's `parse_url()`.
 *
 * @link  https://www.php.net/manual/en/function.parse-url.php
 *
 */
if (!function_exists('wp_parse_url')) {
    function wp_parse_url($url, $component = -1) {
        $to_unset = array();
        $url      = (string)$url;

        if ('//' === substr($url, 0, 2)) {
            $to_unset[] = 'scheme';
            $url        = 'placeholder:' . $url;
        } elseif ('/' === substr($url, 0, 1)) {
            $to_unset[] = 'scheme';
            $to_unset[] = 'host';
            $url        = 'placeholder://placeholder' . $url;
        }

        $parts = parse_url($url);

        if (false === $parts) {
            // Parsing failure.
            return $parts;
        }

        // Remove the placeholder values.
        foreach ($to_unset as $key) {
            unset($parts[$key]);
        }

        return _get_component_from_parsed_url_array($parts, $component);
    }
}

/**
 * Retrieve a specific component from a parsed URL array.
 *
 * @param array|false $url_parts The parsed URL. Can be false if the URL failed to parse.
 * @param int         $component The specific component to retrieve. Use one of the PHP
 *                               predefined constants to specify which one.
 *                               Defaults to -1 (= return all parts as an array).
 *
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a string or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 * @internal
 *
 * @since  4.7.0
 * @access private
 *
 * @link   https://www.php.net/manual/en/function.parse-url.php
 *
 */
if (!function_exists('_get_component_from_parsed_url_array')) {
    function _get_component_from_parsed_url_array($url_parts, $component = -1) {
        if (-1 === $component) {
            return $url_parts;
        }

        $key = _wp_translate_php_url_constant_to_key($component);
        if (false !== $key && is_array($url_parts) && isset($url_parts[$key])) {
            return $url_parts[$key];
        } else {
            return null;
        }
    }
}

/**
 * Translate a PHP_URL_* constant to the named array keys PHP uses.
 *
 * @param int $constant PHP_URL_* constant.
 *
 * @return string|false The named key or false.
 * @link   https://www.php.net/manual/en/url.constants.php
 *
 * @internal
 *
 * @since  4.7.0
 * @access private
 *
 */
if (!function_exists('_wp_translate_php_url_constant_to_key')) {
    function _wp_translate_php_url_constant_to_key($constant) {
        $translation = array(
            PHP_URL_SCHEME   => 'scheme',
            PHP_URL_HOST     => 'host',
            PHP_URL_PORT     => 'port',
            PHP_URL_USER     => 'user',
            PHP_URL_PASS     => 'pass',
            PHP_URL_PATH     => 'path',
            PHP_URL_QUERY    => 'query',
            PHP_URL_FRAGMENT => 'fragment',
        );

        if (isset($translation[$constant])) {
            return $translation[$constant];
        } else {
            return false;
        }
    }
}
