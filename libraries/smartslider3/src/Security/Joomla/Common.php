<?php

use Nextend\Framework\Platform\Platform;

include_once(dirname(__FILE__) . '/Kses.php');

/**
 * Properly strips all HTML tags including script and style
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. wp_strip_all_tags will return ''
 *
 * @param string $string        String containing HTML tags
 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 *
 * @return string The processed string.
 * @since 2.9.0
 *
 */
if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($string, $remove_breaks = false) {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);

        if ($remove_breaks) {
            $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        }

        return trim($string);
    }
}

if (!function_exists('wp_check_invalid_utf8')) {
    function wp_check_invalid_utf8($string, $strip = false) {
        $string = (string)$string;

        if (0 === strlen($string)) {
            return '';
        }

        // Store the site charset as a static to avoid multiple calls to get_option().
        static $is_utf8 = null;
        if (!isset($is_utf8)) {
            $is_utf8 = in_array(Platform::getCharset(), array(
                'utf8',
                'utf-8',
                'UTF8',
                'UTF-8'
            ), true);
        }
        if (!$is_utf8) {
            return $string;
        }

        // Check for support for utf8 in the installed PCRE library once and store the result in a static.
        static $utf8_pcre = null;
        if (!isset($utf8_pcre)) {
            // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
            $utf8_pcre = @preg_match('/^./u', 'a');
        }
        // We can't demand utf8 in the PCRE installation, so just return the string in those cases.
        if (!$utf8_pcre) {
            return $string;
        }

        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- preg_match fails when it encounters invalid UTF8 in $string.
        if (1 === @preg_match('/^./us', $string)) {
            return $string;
        }

        // Attempt to strip the bad chars if requested (not recommended).
        if ($strip && function_exists('iconv')) {
            return iconv('utf-8', 'utf-8', $string);
        }

        return '';
    }
}

/**
 * Determines if a Unicode codepoint is valid.
 *
 * @param int $i Unicode codepoint.
 *
 * @return bool Whether or not the codepoint is a valid Unicode codepoint.
 * @since 2.7.0
 *
 */
if (!function_exists('valid_unicode')) {
    function valid_unicode($i) {
        return (0x9 == $i || 0xa == $i || 0xd == $i || (0x20 <= $i && $i <= 0xd7ff) || (0xe000 <= $i && $i <= 0xfffd) || (0x10000 <= $i && $i <= 0x10ffff));
    }
}

/**
 * Converts a number of special characters into their HTML entities.
 *
 * Specifically deals with: `&`, `<`, `>`, `"`, and `'`.
 *
 * `$quote_style` can be set to ENT_COMPAT to encode `"` to
 * `&quot;`, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
 *
 * @param string       $string        The text which is to be encoded.
 * @param int|string   $quote_style   Optional. Converts double quotes if set to ENT_COMPAT,
 *                                    both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES.
 *                                    Converts single and double quotes, as well as converting HTML
 *                                    named entities (that are not also XML named entities) to their
 *                                    code points if set to ENT_XML1. Also compatible with old values;
 *                                    converting single quotes if set to 'single',
 *                                    double if set to 'double' or both if otherwise set.
 *                                    Default is ENT_NOQUOTES.
 * @param false|string $charset       Optional. The character encoding of the string. Default false.
 * @param bool         $double_encode Optional. Whether to encode existing HTML entities. Default false.
 *
 * @return string The encoded text with HTML entities.
 * @since  5.5.0 `$quote_style` also accepts `ENT_XML1`.
 * @access private
 *
 * @since  1.2.2
 */
if (!function_exists('_wp_specialchars')) {
    function _wp_specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false) {
        $string = (string)$string;

        if (0 === strlen($string)) {
            return '';
        }

        // Don't bother if there are no specialchars - saves some processing.
        if (!preg_match('/[&<>"\']/', $string)) {
            return $string;
        }

        // Account for the previous behaviour of the function when the $quote_style is not an accepted value.
        if (empty($quote_style)) {
            $quote_style = ENT_NOQUOTES;
        } elseif (ENT_XML1 === $quote_style) {
            $quote_style = ENT_QUOTES | ENT_XML1;
        } elseif (!in_array($quote_style, array(
            ENT_NOQUOTES,
            ENT_COMPAT,
            ENT_QUOTES,
            'single',
            'double'
        ), true)) {
            $quote_style = ENT_QUOTES;
        }

        // Store the site charset as a static to avoid multiple calls to wp_load_alloptions().
        if (!$charset) {
            static $_charset = null;
            if (!isset($_charset)) {
                $_charset = \Nextend\Framework\Platform\Platform::getCharset();
            }
            $charset = $_charset;
        }

        if (in_array($charset, array(
            'utf8',
            'utf-8',
            'UTF8'
        ), true)) {
            $charset = 'UTF-8';
        }

        $_quote_style = $quote_style;

        if ('double' === $quote_style) {
            $quote_style  = ENT_COMPAT;
            $_quote_style = ENT_COMPAT;
        } elseif ('single' === $quote_style) {
            $quote_style = ENT_NOQUOTES;
        }

        if (!$double_encode) {
            // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
            // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
            $string = wp_kses_normalize_entities($string, ($quote_style & ENT_XML1) ? 'xml' : 'html');
        }

        $string = htmlspecialchars($string, $quote_style, $charset, $double_encode);

        // Back-compat.
        if ('single' === $_quote_style) {
            $string = str_replace("'", '&#039;', $string);
        }

        return $string;
    }
}