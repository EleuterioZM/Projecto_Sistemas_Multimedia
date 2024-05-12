<?php

global $allowedentitynames, $allowedxmlentitynames;

$allowedentitynames = array(
    'nbsp',
    'iexcl',
    'cent',
    'pound',
    'curren',
    'yen',
    'brvbar',
    'sect',
    'uml',
    'copy',
    'ordf',
    'laquo',
    'not',
    'shy',
    'reg',
    'macr',
    'deg',
    'plusmn',
    'acute',
    'micro',
    'para',
    'middot',
    'cedil',
    'ordm',
    'raquo',
    'iquest',
    'Agrave',
    'Aacute',
    'Acirc',
    'Atilde',
    'Auml',
    'Aring',
    'AElig',
    'Ccedil',
    'Egrave',
    'Eacute',
    'Ecirc',
    'Euml',
    'Igrave',
    'Iacute',
    'Icirc',
    'Iuml',
    'ETH',
    'Ntilde',
    'Ograve',
    'Oacute',
    'Ocirc',
    'Otilde',
    'Ouml',
    'times',
    'Oslash',
    'Ugrave',
    'Uacute',
    'Ucirc',
    'Uuml',
    'Yacute',
    'THORN',
    'szlig',
    'agrave',
    'aacute',
    'acirc',
    'atilde',
    'auml',
    'aring',
    'aelig',
    'ccedil',
    'egrave',
    'eacute',
    'ecirc',
    'euml',
    'igrave',
    'iacute',
    'icirc',
    'iuml',
    'eth',
    'ntilde',
    'ograve',
    'oacute',
    'ocirc',
    'otilde',
    'ouml',
    'divide',
    'oslash',
    'ugrave',
    'uacute',
    'ucirc',
    'uuml',
    'yacute',
    'thorn',
    'yuml',
    'quot',
    'amp',
    'lt',
    'gt',
    'apos',
    'OElig',
    'oelig',
    'Scaron',
    'scaron',
    'Yuml',
    'circ',
    'tilde',
    'ensp',
    'emsp',
    'thinsp',
    'zwnj',
    'zwj',
    'lrm',
    'rlm',
    'ndash',
    'mdash',
    'lsquo',
    'rsquo',
    'sbquo',
    'ldquo',
    'rdquo',
    'bdquo',
    'dagger',
    'Dagger',
    'permil',
    'lsaquo',
    'rsaquo',
    'euro',
    'fnof',
    'Alpha',
    'Beta',
    'Gamma',
    'Delta',
    'Epsilon',
    'Zeta',
    'Eta',
    'Theta',
    'Iota',
    'Kappa',
    'Lambda',
    'Mu',
    'Nu',
    'Xi',
    'Omicron',
    'Pi',
    'Rho',
    'Sigma',
    'Tau',
    'Upsilon',
    'Phi',
    'Chi',
    'Psi',
    'Omega',
    'alpha',
    'beta',
    'gamma',
    'delta',
    'epsilon',
    'zeta',
    'eta',
    'theta',
    'iota',
    'kappa',
    'lambda',
    'mu',
    'nu',
    'xi',
    'omicron',
    'pi',
    'rho',
    'sigmaf',
    'sigma',
    'tau',
    'upsilon',
    'phi',
    'chi',
    'psi',
    'omega',
    'thetasym',
    'upsih',
    'piv',
    'bull',
    'hellip',
    'prime',
    'Prime',
    'oline',
    'frasl',
    'weierp',
    'image',
    'real',
    'trade',
    'alefsym',
    'larr',
    'uarr',
    'rarr',
    'darr',
    'harr',
    'crarr',
    'lArr',
    'uArr',
    'rArr',
    'dArr',
    'hArr',
    'forall',
    'part',
    'exist',
    'empty',
    'nabla',
    'isin',
    'notin',
    'ni',
    'prod',
    'sum',
    'minus',
    'lowast',
    'radic',
    'prop',
    'infin',
    'ang',
    'and',
    'or',
    'cap',
    'cup',
    'int',
    'sim',
    'cong',
    'asymp',
    'ne',
    'equiv',
    'le',
    'ge',
    'sub',
    'sup',
    'nsub',
    'sube',
    'supe',
    'oplus',
    'otimes',
    'perp',
    'sdot',
    'lceil',
    'rceil',
    'lfloor',
    'rfloor',
    'lang',
    'rang',
    'loz',
    'spades',
    'clubs',
    'hearts',
    'diams',
    'sup1',
    'sup2',
    'sup3',
    'frac14',
    'frac12',
    'frac34',
    'there4',
);

$allowedxmlentitynames = array(
    'amp',
    'lt',
    'gt',
    'apos',
    'quot',
);


/**
 * Filters text content and strips out disallowed HTML.
 *
 * This function makes sure that only the allowed HTML element names, attribute
 * names, attribute values, and HTML entities will occur in the given text string.
 *
 * This function expects unslashed data.
 *
 * @param string         $string            Text content to filter.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Filtered content containing only the allowed HTML.
 * @see   wp_allowed_protocols() for the default allowed protocols in link URLs.
 *
 * @since 1.0.0
 *
 * @see   wp_kses_post() for specifically filtering post content and fields.
 */
if (!function_exists('wp_kses')) {
    function wp_kses($string, $allowed_html, $allowed_protocols = array()) {
        if (empty($allowed_protocols)) {
            $allowed_protocols = wp_allowed_protocols();
        }

        $string = wp_kses_no_null($string, array('slash_zero' => 'keep'));
        $string = wp_kses_normalize_entities($string);

        return wp_kses_split($string, $allowed_html, $allowed_protocols);
    }
}

/**
 * Retrieve a list of protocols to allow in HTML attributes.
 *
 * @return string[] Array of allowed protocols. Defaults to an array containing 'http', 'https',
 *                  'ftp', 'ftps', 'mailto', 'news', 'irc', 'irc6', 'ircs', 'gopher', 'nntp', 'feed',
 *                  'telnet', 'mms', 'rtsp', 'sms', 'svn', 'tel', 'fax', 'xmpp', 'webcal', and 'urn'.
 *                  This covers all common link protocols, except for 'javascript' which should not
 *                  be allowed for untrusted users.
 * @since 4.3.0 Added 'webcal' to the protocols array.
 * @since 4.7.0 Added 'urn' to the protocols array.
 * @since 5.3.0 Added 'sms' to the protocols array.
 * @since 5.6.0 Added 'irc6' and 'ircs' to the protocols array.
 *
 * @see   wp_kses()
 * @see   esc_url()
 *
 * @since 3.3.0
 */
if (!function_exists('wp_allowed_protocols')) {
    function wp_allowed_protocols() {
        static $protocols = array();

        if (empty($protocols)) {
            $protocols = array(
                'http',
                'https',
                'ftp',
                'ftps',
                'mailto',
                'news',
                'irc',
                'irc6',
                'ircs',
                'gopher',
                'nntp',
                'feed',
                'telnet',
                'mms',
                'rtsp',
                'sms',
                'svn',
                'tel',
                'fax',
                'xmpp',
                'webcal',
                'urn'
            );
        }

        return $protocols;
    }
}

/**
 * Removes any invalid control characters in a text string.
 *
 * Also removes any instance of the `\0` string.
 *
 * @param string $string  Content to filter null characters from.
 * @param array  $options Set 'slash_zero' => 'keep' when '\0' is allowed. Default is 'remove'.
 *
 * @return string Filtered content.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_no_null')) {
    function wp_kses_no_null($string, $options = null) {
        if (!isset($options['slash_zero'])) {
            $options = array('slash_zero' => 'remove');
        }

        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $string);
        if ('remove' === $options['slash_zero']) {
            $string = preg_replace('/\\\\+0+/', '', $string);
        }

        return $string;
    }
}

/**
 * Converts lone less than signs.
 *
 * KSES already converts lone greater than signs.
 *
 * @param string $text Text to be converted.
 *
 * @return string Converted text.
 * @since 2.3.0
 *
 */
if (!function_exists('wp_pre_kses_less_than')) {
    function wp_pre_kses_less_than($text) {
        return preg_replace_callback('%<[^>]*?((?=<)|>|$)%', 'wp_pre_kses_less_than_callback', $text);
    }
}

/**
 * Callback function used by preg_replace.
 *
 * @param string[] $matches Populated by matches to preg_replace.
 *
 * @return string The text returned after esc_html if needed.
 * @since 2.3.0
 *
 */
if (!function_exists('wp_pre_kses_less_than_callback')) {
    function wp_pre_kses_less_than_callback($matches) {
        if (false === strpos($matches[0], '>')) {
            return esc_html($matches[0]);
        }

        return $matches[0];
    }
}

/**
 * Converts and fixes HTML entities.
 *
 * This function normalizes HTML entities. It will convert `AT&T` to the correct
 * `AT&amp;T`, `&#00058;` to `&#058;`, `&#XYZZY;` to `&amp;#XYZZY;` and so on.
 *
 * When `$context` is set to 'xml', HTML entities are converted to their code points.  For
 * example, `AT&T&hellip;&#XYZZY;` is converted to `AT&amp;T…&amp;#XYZZY;`.
 *
 * @param string $string  Content to normalize entities.
 * @param string $context Context for normalization. Can be either 'html' or 'xml'.
 *                        Default 'html'.
 *
 * @return string Content with normalized entities.
 * @since 5.5.0 Added `$context` parameter.
 *
 * @since 1.0.0
 */
if (!function_exists('wp_kses_normalize_entities')) {
    function wp_kses_normalize_entities($string, $context = 'html') {
        // Disarm all entities by converting & to &amp;
        $string = str_replace('&', '&amp;', $string);

        // Change back the allowed entities in our list of allowed entities.
        if ('xml' === $context) {
            $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_xml_named_entities', $string);
        } else {
            $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_named_entities', $string);
        }
        $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'wp_kses_normalize_entities2', $string);
        $string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'wp_kses_normalize_entities3', $string);

        return $string;
    }
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by XML validators.  HTML named entity
 * references are converted to their code points.
 *
 * @param array  $matches preg_replace_callback() matches array.
 *
 * @return string Correctly encoded entity.
 * @global array $allowedxmlentitynames
 *
 * @since 5.5.0
 *
 * @global array $allowedentitynames
 */
if (!function_exists('wp_kses_xml_named_entities')) {
    function wp_kses_xml_named_entities($matches) {
        global $allowedentitynames, $allowedxmlentitynames;

        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];

        if (in_array($i, $allowedxmlentitynames, true)) {
            return "&$i;";
        } elseif (in_array($i, $allowedentitynames, true)) {
            return html_entity_decode("&$i;", ENT_HTML5);
        }

        return "&amp;$i;";
    }
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by HTML and XML validators.
 *
 * @param array  $matches preg_replace_callback() matches array.
 *
 * @return string Correctly encoded entity.
 * @since 3.0.0
 *
 * @global array $allowedentitynames
 *
 */
if (!function_exists('wp_kses_named_entities')) {
    function wp_kses_named_entities($matches) {
        global $allowedentitynames;

        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];

        return (!in_array($i, $allowedentitynames, true)) ? "&amp;$i;" : "&$i;";
    }
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function helps `wp_kses_normalize_entities()` to only accept 16-bit
 * values and nothing more for `&#number;` entities.
 *
 * @access private
 *
 * @param array $matches `preg_replace_callback()` matches array.
 *
 * @return string Correctly encoded entity.
 * @ignore
 * @since  1.0.0
 *
 */
if (!function_exists('wp_kses_normalize_entities2')) {
    function wp_kses_normalize_entities2($matches) {
        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];
        if (valid_unicode($i)) {
            $i = str_pad(ltrim($i, '0'), 3, '0', STR_PAD_LEFT);
            $i = "&#$i;";
        } else {
            $i = "&amp;#$i;";
        }

        return $i;
    }
}

/**
 * Callback for `wp_kses_normalize_entities()` for regular expression.
 *
 * This function helps `wp_kses_normalize_entities()` to only accept valid Unicode
 * numeric entities in hex form.
 *
 * @param array $matches `preg_replace_callback()` matches array.
 *
 * @return string Correctly encoded entity.
 * @since  2.7.0
 * @access private
 * @ignore
 *
 */
if (!function_exists('wp_kses_normalize_entities3')) {
    function wp_kses_normalize_entities3($matches) {
        if (empty($matches[1])) {
            return '';
        }

        $hexchars = $matches[1];

        return (!valid_unicode(hexdec($hexchars))) ? "&amp;#x$hexchars;" : '&#x' . ltrim($hexchars, '0') . ';';
    }
}

/**
 * Searches for HTML tags, no matter how malformed.
 *
 * It also matches stray `>` characters.
 *
 * @param string          $string                 Content to filter.
 * @param array[]|string  $allowed_html           An array of allowed HTML elements and attributes,
 *                                                or a context name such as 'post'. See wp_kses_allowed_html()
 *                                                for the list of accepted context names.
 * @param string[]        $allowed_protocols      Array of allowed URL protocols.
 *
 * @return string Content with fixed HTML tags
 * @global array[]|string $pass_allowed_html      An array of allowed HTML elements and attributes,
 *                                                or a context name such as 'post'.
 * @global string[]       $pass_allowed_protocols Array of allowed URL protocols.
 *
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_split')) {
    function wp_kses_split($string, $allowed_html, $allowed_protocols) {
        global $pass_allowed_html, $pass_allowed_protocols;

        $pass_allowed_html      = $allowed_html;
        $pass_allowed_protocols = $allowed_protocols;

        return preg_replace_callback('%(<!--.*?(-->|$))|(<[^>]*(>|$)|>)%', '_wp_kses_split_callback', $string);
    }
}

/**
 * Callback for `wp_kses_split()`.
 *
 * @param array           $match                  preg_replace regexp matches
 *
 * @return string
 * @global array[]|string $pass_allowed_html      An array of allowed HTML elements and attributes,
 *                                                or a context name such as 'post'.
 * @global string[]       $pass_allowed_protocols Array of allowed URL protocols.
 *
 * @since  3.1.0
 * @access private
 * @ignore
 *
 */
if (!function_exists('_wp_kses_split_callback')) {
    function _wp_kses_split_callback($match) {
        global $pass_allowed_html, $pass_allowed_protocols;

        return wp_kses_split2($match[0], $pass_allowed_html, $pass_allowed_protocols);
    }
}

/**
 * Callback for `wp_kses_split()` for fixing malformed HTML tags.
 *
 * This function does a lot of work. It rejects some very malformed things like
 * `<:::>`. It returns an empty string, if the element isn't allowed (look ma, no
 * `strip_tags()`!). Otherwise it splits the tag into an element and an attribute
 * list.
 *
 * After the tag is split into an element and an attribute list, it is run
 * through another filter which will remove illegal attributes and once that is
 * completed, will be returned.
 *
 * @access private
 *
 * @param string         $string            Content to filter.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Fixed HTML element
 * @ignore
 * @since  1.0.0
 *
 */
if (!function_exists('wp_kses_split2')) {
    function wp_kses_split2($string, $allowed_html, $allowed_protocols) {
        $string = wp_kses_stripslashes($string);

        // It matched a ">" character.
        if ('<' !== substr($string, 0, 1)) {
            return '&gt;';
        }

        // Allow HTML comments.
        if ('<!--' === substr($string, 0, 4)) {
            $string = str_replace(array(
                '<!--',
                '-->'
            ), '', $string);
            while (($newstring = wp_kses($string, $allowed_html, $allowed_protocols)) != $string) {
                $string = $newstring;
            }
            if ('' === $string) {
                return '';
            }
            // Prevent multiple dashes in comments.
            $string = preg_replace('/--+/', '-', $string);
            // Prevent three dashes closing a comment.
            $string = preg_replace('/-$/', '', $string);

            return "<!--{$string}-->";
        }

        // It's seriously malformed.
        if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9-]+)([^>]*)>?$%', $string, $matches)) {
            return '';
        }

        $slash    = trim($matches[1]);
        $elem     = $matches[2];
        $attrlist = $matches[3];

        // They are using a not allowed HTML element.
        if (!isset($allowed_html[strtolower($elem)])) {
            return '';
        }

        // No attributes are allowed for closing elements.
        if ('' !== $slash) {
            return "</$elem>";
        }

        return wp_kses_attr($elem, $attrlist, $allowed_html, $allowed_protocols);
    }
}

/**
 * Strips slashes from in front of quotes.
 *
 * This function changes the character sequence `\"` to just `"`. It leaves all other
 * slashes alone. The quoting from `preg_replace(//e)` requires this.
 *
 * @param string $string String to strip slashes from.
 *
 * @return string Fixed string with quoted slashes.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_stripslashes')) {
    function wp_kses_stripslashes($string) {
        return preg_replace('%\\\\"%', '"', $string);
    }
}

/**
 * Removes all attributes, if none are allowed for this element.
 *
 * If some are allowed it calls `wp_kses_hair()` to split them further, and then
 * it builds up new HTML code from the data that `wp_kses_hair()` returns. It also
 * removes `<` and `>` characters, if there are any left. One more thing it does
 * is to check if the tag has a closing XHTML slash, and if it does, it puts one
 * in the returned code as well.
 *
 * An array of allowed values can be defined for attributes. If the attribute value
 * doesn't fall into the list, the attribute will be removed from the tag.
 *
 * Attributes can be marked as required. If a required attribute is not present,
 * KSES will remove all attributes from the tag. As KSES doesn't match opening and
 * closing tags, it's not possible to safely remove the tag itself, the safest
 * fallback is to strip all attributes from the tag, instead.
 *
 * @param string         $element           HTML element/tag.
 * @param string         $attr              HTML attributes from HTML element to closing HTML element tag.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Sanitized HTML element.
 * @since       5.9.0 Added support for an array of allowed values for attributes.
 *              Added support for required attributes.
 *
 * @since       1.0.0
 */
if (!function_exists('wp_kses_attr')) {
    function wp_kses_attr($element, $attr, $allowed_html, $allowed_protocols) {

        // Is there a closing XHTML slash at the end of the attributes?
        $xhtml_slash = '';
        if (preg_match('%\s*/\s*$%', $attr)) {
            $xhtml_slash = ' /';
        }

        // Are any attributes allowed at all for this element?
        $element_low = strtolower($element);
        if (empty($allowed_html[$element_low]) || true === $allowed_html[$element_low]) {
            return "<$element$xhtml_slash>";
        }

        // Split it.
        $attrarr = wp_kses_hair($attr, $allowed_protocols);

        // Check if there are attributes that are required.
        $required_attrs = array_filter($allowed_html[$element_low], function ($required_attr_limits) {
            return isset($required_attr_limits['required']) && true === $required_attr_limits['required'];
        });

        /*
         * If a required attribute check fails, we can return nothing for a self-closing tag,
         * but for a non-self-closing tag the best option is to return the element with attributes,
         * as KSES doesn't handle matching the relevant closing tag.
         */
        $stripped_tag = '';
        if (empty($xhtml_slash)) {
            $stripped_tag = "<$element>";
        }

        // Go through $attrarr, and save the allowed attributes for this element in $attr2.
        $attr2 = '';
        foreach ($attrarr as $arreach) {
            // Check if this attribute is required.
            $required = isset($required_attrs[strtolower($arreach['name'])]);

            if (wp_kses_attr_check($arreach['name'], $arreach['value'], $arreach['whole'], $arreach['vless'], $element, $allowed_html)) {
                $attr2 .= ' ' . $arreach['whole'];

                // If this was a required attribute, we can mark it as found.
                if ($required) {
                    unset($required_attrs[strtolower($arreach['name'])]);
                }
            } elseif ($required) {
                // This attribute was required, but didn't pass the check. The entire tag is not allowed.
                return $stripped_tag;
            }
        }

        // If some required attributes weren't set, the entire tag is not allowed.
        if (!empty($required_attrs)) {
            return $stripped_tag;
        }

        // Remove any "<" or ">" characters.
        $attr2 = preg_replace('/[<>]/', '', $attr2);

        return "<$element$attr2$xhtml_slash>";
    }
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * This function does a lot of work. It parses an attribute list into an array
 * with attribute data, and tries to do the right thing even if it gets weird
 * input. It will add quotes around attribute values that don't have any quotes
 * or apostrophes around them, to make it easier to produce HTML code that will
 * conform to W3C's HTML specification. It will also remove bad URL protocols
 * from attribute values. It also reduces duplicate attributes by using the
 * attribute defined first (`foo='bar' foo='baz'` will result in `foo='bar'`).
 *
 * @param string   $attr              Attribute list from HTML element to closing HTML element tag.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 *
 * @return array[] Array of attribute information after parsing.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_hair')) {
    function wp_kses_hair($attr, $allowed_protocols) {
        $attrarr  = array();
        $mode     = 0;
        $attrname = '';
        $uris     = wp_kses_uri_attributes();

        // Loop through the whole attribute list.

        while (strlen($attr) != 0) {
            $working = 0; // Was the last operation successful?

            switch ($mode) {
                case 0:
                    if (preg_match('/^([_a-zA-Z][-_a-zA-Z0-9:.]*)/', $attr, $match)) {
                        $attrname = $match[1];
                        $working  = 1;
                        $mode     = 1;
                        $attr     = preg_replace('/^[_a-zA-Z][-_a-zA-Z0-9:.]*/', '', $attr);
                    }

                    break;

                case 1:
                    if (preg_match('/^\s*=\s*/', $attr)) { // Equals sign.
                        $working = 1;
                        $mode    = 2;
                        $attr    = preg_replace('/^\s*=\s*/', '', $attr);
                        break;
                    }

                    if (preg_match('/^\s+/', $attr)) { // Valueless.
                        $working = 1;
                        $mode    = 0;
                        if (false === array_key_exists($attrname, $attrarr)) {
                            $attrarr[$attrname] = array(
                                'name'  => $attrname,
                                'value' => '',
                                'whole' => $attrname,
                                'vless' => 'y',
                            );
                        }
                        $attr = preg_replace('/^\s+/', '', $attr);
                    }

                    break;

                case 2:
                    if (preg_match('%^"([^"]*)"(\s+|/?$)%', $attr, $match)) {
                        // "value"
                        $thisval = $match[1];
                        if (in_array(strtolower($attrname), $uris, true)) {
                            $thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);
                        }

                        if (false === array_key_exists($attrname, $attrarr)) {
                            $attrarr[$attrname] = array(
                                'name'  => $attrname,
                                'value' => $thisval,
                                'whole' => "$attrname=\"$thisval\"",
                                'vless' => 'n',
                            );
                        }
                        $working = 1;
                        $mode    = 0;
                        $attr    = preg_replace('/^"[^"]*"(\s+|$)/', '', $attr);
                        break;
                    }

                    if (preg_match("%^'([^']*)'(\s+|/?$)%", $attr, $match)) {
                        // 'value'
                        $thisval = $match[1];
                        if (in_array(strtolower($attrname), $uris, true)) {
                            $thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);
                        }

                        if (false === array_key_exists($attrname, $attrarr)) {
                            $attrarr[$attrname] = array(
                                'name'  => $attrname,
                                'value' => $thisval,
                                'whole' => "$attrname='$thisval'",
                                'vless' => 'n',
                            );
                        }
                        $working = 1;
                        $mode    = 0;
                        $attr    = preg_replace("/^'[^']*'(\s+|$)/", '', $attr);
                        break;
                    }

                    if (preg_match("%^([^\s\"']+)(\s+|/?$)%", $attr, $match)) {
                        // value
                        $thisval = $match[1];
                        if (in_array(strtolower($attrname), $uris, true)) {
                            $thisval = wp_kses_bad_protocol($thisval, $allowed_protocols);
                        }

                        if (false === array_key_exists($attrname, $attrarr)) {
                            $attrarr[$attrname] = array(
                                'name'  => $attrname,
                                'value' => $thisval,
                                'whole' => "$attrname=\"$thisval\"",
                                'vless' => 'n',
                            );
                        }
                        // We add quotes to conform to W3C's HTML spec.
                        $working = 1;
                        $mode    = 0;
                        $attr    = preg_replace("%^[^\s\"']+(\s+|$)%", '', $attr);
                    }

                    break;
            } // End switch.

            if (0 == $working) { // Not well-formed, remove and try again.
                $attr = wp_kses_html_error($attr);
                $mode = 0;
            }
        } // End while.

        if (1 == $mode && false === array_key_exists($attrname, $attrarr)) {
            // Special case, for when the attribute list ends with a valueless
            // attribute like "selected".
            $attrarr[$attrname] = array(
                'name'  => $attrname,
                'value' => '',
                'whole' => $attrname,
                'vless' => 'y',
            );
        }

        return $attrarr;
    }
}

/**
 * Determines whether an attribute is allowed.
 *
 * @param string $name         The attribute name. Passed by reference. Returns empty string when not allowed.
 * @param string $value        The attribute value. Passed by reference. Returns a filtered value.
 * @param string $whole        The `name=value` input. Passed by reference. Returns filtered input.
 * @param string $vless        Whether the attribute is valueless. Use 'y' or 'n'.
 * @param string $element      The name of the element to which this attribute belongs.
 * @param array  $allowed_html The full list of allowed elements and attributes.
 *
 * @return bool Whether or not the attribute is allowed.
 * @since 5.0.0 Added support for `data-*` wildcard attributes.
 *
 * @since 4.2.3
 */
if (!function_exists('wp_kses_attr_check')) {
    function wp_kses_attr_check(&$name, &$value, &$whole, $vless, $element, $allowed_html) {
        $name_low    = strtolower($name);
        $element_low = strtolower($element);

        if (!isset($allowed_html[$element_low])) {
            $name  = '';
            $value = '';
            $whole = '';

            return false;
        }

        $allowed_attr = $allowed_html[$element_low];

        if (!isset($allowed_attr[$name_low]) || '' === $allowed_attr[$name_low]) {
            /*
             * Allow `data-*` attributes.
             *
             * When specifying `$allowed_html`, the attribute name should be set as
             * `data-*` (not to be mixed with the HTML 4.0 `data` attribute, see
             * https://www.w3.org/TR/html40/struct/objects.html#adef-data).
             *
             * Note: the attribute name should only contain `A-Za-z0-9_-` chars,
             * double hyphens `--` are not accepted by WordPress.
             */
            if (strpos($name_low, 'data-') === 0 && !empty($allowed_attr['data-*']) && preg_match('/^data(?:-[a-z0-9_]+)+$/', $name_low, $match)) {
                /*
                 * Add the whole attribute name to the allowed attributes and set any restrictions
                 * for the `data-*` attribute values for the current element.
                 */
                $allowed_attr[$match[0]] = $allowed_attr['data-*'];
            } else {
                $name  = '';
                $value = '';
                $whole = '';

                return false;
            }
        }

        if ('style' === $name_low) {
            $new_value = safecss_filter_attr($value);

            if (empty($new_value)) {
                $name  = '';
                $value = '';
                $whole = '';

                return false;
            }

            $whole = str_replace($value, $new_value, $whole);
            $value = $new_value;
        }

        if (is_array($allowed_attr[$name_low])) {
            // There are some checks.
            foreach ($allowed_attr[$name_low] as $currkey => $currval) {
                if (!wp_kses_check_attr_val($value, $vless, $currkey, $currval)) {
                    $name  = '';
                    $value = '';
                    $whole = '';

                    return false;
                }
            }
        }

        return true;
    }
}

/**
 * Returns an array of HTML attribute names whose value contains a URL.
 *
 * This function returns a list of all HTML attributes that must contain
 * a URL according to the HTML specification.
 *
 * This list includes URI attributes both allowed and disallowed by KSES.
 *
 * @link  https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes
 *
 * @since 5.0.1
 *
 * @return string[] HTML attribute names whose value contains a URL.
 */
if (!function_exists('wp_kses_uri_attributes')) {
    function wp_kses_uri_attributes() {
        $uri_attributes = array(
            'action',
            'archive',
            'background',
            'cite',
            'classid',
            'codebase',
            'data',
            'formaction',
            'href',
            'icon',
            'longdesc',
            'manifest',
            'poster',
            'profile',
            'src',
            'usemap',
            'xmlns',
        );

        return $uri_attributes;
    }
}

/**
 * Sanitizes a string and removed disallowed URL protocols.
 *
 * This function removes all non-allowed protocols from the beginning of the
 * string. It ignores whitespace and the case of the letters, and it does
 * understand HTML entities. It does its work recursively, so it won't be
 * fooled by a string like `javascript:javascript:alert(57)`.
 *
 * @param string   $string            Content to filter bad protocols from.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Filtered content.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_bad_protocol')) {
    function wp_kses_bad_protocol($string, $allowed_protocols) {
        $string     = wp_kses_no_null($string);
        $iterations = 0;

        do {
            $original_string = $string;
            $string          = wp_kses_bad_protocol_once($string, $allowed_protocols);
        } while ($original_string != $string && ++$iterations < 6);

        if ($original_string != $string) {
            return '';
        }

        return $string;
    }
}

/**
 * Sanitizes content from bad protocols and other characters.
 *
 * This function searches for URL protocols at the beginning of the string, while
 * handling whitespace and HTML entities.
 *
 * @param string   $string            Content to check for bad protocols.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 * @param int      $count             Depth of call recursion to this function.
 *
 * @return string Sanitized content.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_bad_protocol_once')) {
    function wp_kses_bad_protocol_once($string, $allowed_protocols, $count = 1) {
        $string  = preg_replace('/(&#0*58(?![;0-9])|&#x0*3a(?![;a-f0-9]))/i', '$1;', $string);
        $string2 = preg_split('/:|&#0*58;|&#x0*3a;|&colon;/i', $string, 2);
        if (isset($string2[1]) && !preg_match('%/\?%', $string2[0])) {
            $string   = trim($string2[1]);
            $protocol = wp_kses_bad_protocol_once2($string2[0], $allowed_protocols);
            if ('feed:' === $protocol) {
                if ($count > 2) {
                    return '';
                }
                $string = wp_kses_bad_protocol_once($string, $allowed_protocols, ++$count);
                if (empty($string)) {
                    return $string;
                }
            }
            $string = $protocol . $string;
        }

        return $string;
    }
}

/**
 * Callback for `wp_kses_bad_protocol_once()` regular expression.
 *
 * This function processes URL protocols, checks to see if they're in the
 * list of allowed protocols or not, and returns different data depending
 * on the answer.
 *
 * @access private
 *
 * @param string   $string            URI scheme to check against the list of allowed protocols.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Sanitized content.
 * @since  1.0.0
 *
 * @ignore
 */
if (!function_exists('wp_kses_bad_protocol_once2')) {
    function wp_kses_bad_protocol_once2($string, $allowed_protocols) {
        $string2 = wp_kses_decode_entities($string);
        $string2 = preg_replace('/\s/', '', $string2);
        $string2 = wp_kses_no_null($string2);
        $string2 = strtolower($string2);

        $allowed = false;
        foreach ((array)$allowed_protocols as $one_protocol) {
            if (strtolower($one_protocol) == $string2) {
                $allowed = true;
                break;
            }
        }

        if ($allowed) {
            return "$string2:";
        } else {
            return '';
        }
    }
}

/**
 * Converts all numeric HTML entities to their named counterparts.
 *
 * This function decodes numeric HTML entities (`&#65;` and `&#x41;`).
 * It doesn't do anything with named entities like `&auml;`, but we don't
 * need them in the allowed URL protocols system anyway.
 *
 * @param string $string Content to change entities.
 *
 * @return string Content after decoded entities.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_decode_entities')) {
    function wp_kses_decode_entities($string) {
        $string = preg_replace_callback('/&#([0-9]+);/', '_wp_kses_decode_entities_chr', $string);
        $string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', '_wp_kses_decode_entities_chr_hexdec', $string);

        return $string;
    }
}

/**
 * Regex callback for `wp_kses_decode_entities()`.
 *
 * @param array $match preg match
 *
 * @return string
 * @since  2.9.0
 * @access private
 * @ignore
 *
 */
if (!function_exists('_wp_kses_decode_entities_chr')) {
    function _wp_kses_decode_entities_chr($match) {
        return chr($match[1]);
    }
}

/**
 * Regex callback for `wp_kses_decode_entities()`.
 *
 * @param array $match preg match
 *
 * @return string
 * @since  2.9.0
 * @access private
 * @ignore
 *
 */
if (!function_exists('_wp_kses_decode_entities_chr_hexdec')) {
    function _wp_kses_decode_entities_chr_hexdec($match) {
        return chr(hexdec($match[1]));
    }
}

/**
 * Handles parsing errors in `wp_kses_hair()`.
 *
 * The general plan is to remove everything to and including some whitespace,
 * but it deals with quotes and apostrophes as well.
 *
 * @param string $string
 *
 * @return string
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_html_error')) {
    function wp_kses_html_error($string) {
        return preg_replace('/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $string);
    }
}

/**
 * Filters an inline style attribute and removes disallowed rules.
 *
 * @param string $css A string of CSS rules.
 *
 * @return string Filtered string of CSS rules.
 * @since       2.8.1
 * @since       4.4.0 Added support for `min-height`, `max-height`, `min-width`, and `max-width`.
 * @since       4.6.0 Added support for `list-style-type`.
 * @since       5.0.0 Added support for `background-image`.
 * @since       5.1.0 Added support for `text-transform`.
 * @since       5.2.0 Added support for `background-position` and `grid-template-columns`.
 * @since       5.3.0 Added support for `grid`, `flex` and `column` layout properties.
 *              Extend `background-*` support of individual properties.
 * @since       5.3.1 Added support for gradient backgrounds.
 * @since       5.7.1 Added support for `object-position`.
 * @since       5.8.0 Added support for `calc()` and `var()` values.
 *
 */
if (!function_exists('safecss_filter_attr')) {
    function safecss_filter_attr($css) {

        $css = wp_kses_no_null($css);
        $css = str_replace(array(
            "\n",
            "\r",
            "\t"
        ), '', $css);

        $allowed_protocols = wp_allowed_protocols();

        $css_array = explode(';', trim($css));

        /**
         * Filters the list of allowed CSS attributes.
         *
         * @param string[] $attr Array of allowed CSS attributes.
         *
         * @since 2.8.1
         *
         */
        $allowed_attr = array(
            'background',
            'background-color',
            'background-image',
            'background-position',
            'background-size',
            'background-attachment',
            'background-blend-mode',

            'border',
            'border-radius',
            'border-width',
            'border-color',
            'border-style',
            'border-right',
            'border-right-color',
            'border-right-style',
            'border-right-width',
            'border-bottom',
            'border-bottom-color',
            'border-bottom-left-radius',
            'border-bottom-right-radius',
            'border-bottom-style',
            'border-bottom-width',
            'border-bottom-right-radius',
            'border-bottom-left-radius',
            'border-left',
            'border-left-color',
            'border-left-style',
            'border-left-width',
            'border-top',
            'border-top-color',
            'border-top-left-radius',
            'border-top-right-radius',
            'border-top-style',
            'border-top-width',
            'border-top-left-radius',
            'border-top-right-radius',

            'border-spacing',
            'border-collapse',
            'caption-side',

            'columns',
            'column-count',
            'column-fill',
            'column-gap',
            'column-rule',
            'column-span',
            'column-width',

            'color',
            'filter',
            'font',
            'font-family',
            'font-size',
            'font-style',
            'font-variant',
            'font-weight',
            'letter-spacing',
            'line-height',
            'text-align',
            'text-decoration',
            'text-indent',
            'text-transform',

            'height',
            'min-height',
            'max-height',

            'width',
            'min-width',
            'max-width',

            'margin',
            'margin-right',
            'margin-bottom',
            'margin-left',
            'margin-top',

            'padding',
            'padding-right',
            'padding-bottom',
            'padding-left',
            'padding-top',

            'flex',
            'flex-basis',
            'flex-direction',
            'flex-flow',
            'flex-grow',
            'flex-shrink',

            'grid-template-columns',
            'grid-auto-columns',
            'grid-column-start',
            'grid-column-end',
            'grid-column-gap',
            'grid-template-rows',
            'grid-auto-rows',
            'grid-row-start',
            'grid-row-end',
            'grid-row-gap',
            'grid-gap',

            'justify-content',
            'justify-items',
            'justify-self',
            'align-content',
            'align-items',
            'align-self',

            'clear',
            'cursor',
            'direction',
            'float',
            'list-style-type',
            'object-position',
            'overflow',
            'vertical-align',
        );

        /*
         * CSS attributes that accept URL data types.
         *
         * This is in accordance to the CSS spec and unrelated to
         * the sub-set of supported attributes above.
         *
         * See: https://developer.mozilla.org/en-US/docs/Web/CSS/url
         */
        $css_url_data_types = array(
            'background',
            'background-image',

            'cursor',

            'list-style',
            'list-style-image',
        );

        /*
         * CSS attributes that accept gradient data types.
         *
         */
        $css_gradient_data_types = array(
            'background',
            'background-image',
        );

        if (empty($allowed_attr)) {
            return $css;
        }

        $css = '';
        foreach ($css_array as $css_item) {
            if ('' === $css_item) {
                continue;
            }

            $css_item        = trim($css_item);
            $css_test_string = $css_item;
            $found           = false;
            $url_attr        = false;
            $gradient_attr   = false;

            if (strpos($css_item, ':') === false) {
                $found = true;
            } else {
                $parts        = explode(':', $css_item, 2);
                $css_selector = trim($parts[0]);

                if (in_array($css_selector, $allowed_attr, true)) {
                    $found         = true;
                    $url_attr      = in_array($css_selector, $css_url_data_types, true);
                    $gradient_attr = in_array($css_selector, $css_gradient_data_types, true);
                }
            }

            if ($found && $url_attr) {
                // Simplified: matches the sequence `url(*)`.
                preg_match_all('/url\([^)]+\)/', $parts[1], $url_matches);

                foreach ($url_matches[0] as $url_match) {
                    // Clean up the URL from each of the matches above.
                    preg_match('/^url\(\s*([\'\"]?)(.*)(\g1)\s*\)$/', $url_match, $url_pieces);

                    if (empty($url_pieces[2])) {
                        $found = false;
                        break;
                    }

                    $url = trim($url_pieces[2]);

                    if (empty($url) || wp_kses_bad_protocol($url, $allowed_protocols) !== $url) {
                        $found = false;
                        break;
                    } else {
                        // Remove the whole `url(*)` bit that was matched above from the CSS.
                        $css_test_string = str_replace($url_match, '', $css_test_string);
                    }
                }
            }

            if ($found && $gradient_attr) {
                $css_value = trim($parts[1]);
                if (preg_match('/^(repeating-)?(linear|radial|conic)-gradient\(([^()]|rgb[a]?\([^()]*\))*\)$/', $css_value)) {
                    // Remove the whole `gradient` bit that was matched above from the CSS.
                    $css_test_string = str_replace($css_value, '', $css_test_string);
                }
            }

            if ($found) {
                // Allow CSS calc().
                $css_test_string = preg_replace('/calc\(((?:\([^()]*\)?|[^()])*)\)/', '', $css_test_string);
                // Allow CSS var().
                $css_test_string = preg_replace('/\(?var\(--[a-zA-Z0-9_-]*\)/', '', $css_test_string);

                // Check for any CSS containing \ ( & } = or comments,
                // except for url(), calc(), or var() usage checked above.
                $allow_css = !preg_match('%[\\\(&=}]|/\*%', $css_test_string);

                /**
                 * Filters the check for unsafe CSS in `safecss_filter_attr`.
                 *
                 * Enables developers to determine whether a section of CSS should be allowed or discarded.
                 * By default, the value will be false if the part contains \ ( & } = or comments.
                 * Return true to allow the CSS part to be included in the output.
                 *
                 * @param bool   $allow_css       Whether the CSS in the test string is considered safe.
                 * @param string $css_test_string The CSS string to test.
                 *
                 * @since 5.5.0
                 *
                 */

                // Only add the CSS part if it passes the regex check.
                if ($allow_css) {
                    if ('' !== $css) {
                        $css .= ';';
                    }

                    $css .= $css_item;
                }
            }
        }

        return $css;
    }
}

/**
 * Performs different checks for attribute values.
 *
 * The currently implemented checks are "maxlen", "minlen", "maxval", "minval",
 * and "valueless".
 *
 * @param string $value      Attribute value.
 * @param string $vless      Whether the attribute is valueless. Use 'y' or 'n'.
 * @param string $checkname  What $checkvalue is checking for.
 * @param mixed  $checkvalue What constraint the value should pass.
 *
 * @return bool Whether check passes.
 * @since 1.0.0
 *
 */
if (!function_exists('wp_kses_check_attr_val')) {
    function wp_kses_check_attr_val($value, $vless, $checkname, $checkvalue) {
        $ok = true;

        switch (strtolower($checkname)) {
            case 'maxlen':
                /*
                 * The maxlen check makes sure that the attribute value has a length not
                 * greater than the given value. This can be used to avoid Buffer Overflows
                 * in WWW clients and various Internet servers.
                 */

                if (strlen($value) > $checkvalue) {
                    $ok = false;
                }
                break;

            case 'minlen':
                /*
                 * The minlen check makes sure that the attribute value has a length not
                 * smaller than the given value.
                 */

                if (strlen($value) < $checkvalue) {
                    $ok = false;
                }
                break;

            case 'maxval':
                /*
                 * The maxval check does two things: it checks that the attribute value is
                 * an integer from 0 and up, without an excessive amount of zeroes or
                 * whitespace (to avoid Buffer Overflows). It also checks that the attribute
                 * value is not greater than the given value.
                 * This check can be used to avoid Denial of Service attacks.
                 */

                if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value)) {
                    $ok = false;
                }
                if ($value > $checkvalue) {
                    $ok = false;
                }
                break;

            case 'minval':
                /*
                 * The minval check makes sure that the attribute value is a positive integer,
                 * and that it is not smaller than the given value.
                 */

                if (!preg_match('/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value)) {
                    $ok = false;
                }
                if ($value < $checkvalue) {
                    $ok = false;
                }
                break;

            case 'valueless':
                /*
                 * The valueless check makes sure if the attribute has a value
                 * (like `<a href="blah">`) or not (`<option selected>`). If the given value
                 * is a "y" or a "Y", the attribute must not have a value.
                 * If the given value is an "n" or an "N", the attribute must have a value.
                 */

                if (strtolower($checkvalue) != $vless) {
                    $ok = false;
                }
                break;

            case 'values':
                /*
                 * The values check is used when you want to make sure that the attribute
                 * has one of the given values.
                 */

                if (false === array_search(strtolower($value), $checkvalue, true)) {
                    $ok = false;
                }
                break;

            case 'value_callback':
                /*
                 * The value_callback check is used when you want to make sure that the attribute
                 * value is accepted by the callback function.
                 */

                if (!call_user_func($checkvalue, $value)) {
                    $ok = false;
                }
                break;
        } // End switch.

        return $ok;
    }
}