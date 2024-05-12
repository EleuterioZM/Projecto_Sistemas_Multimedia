<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

use NRFramework\URL;

defined('_JEXEC') or die('Restricted access');

class URLHelper
{
    /**
     * Searches the given HTML for all external links and appends the affiliate paramter aff=id to every link based on an affiliate list.
     *
     * @param   string  $text           The html to search for external links
     * @param   array   $affiliates     A key value array: domain name => affiliate parameter
     *
     * @return  string
     */
    public static function replaceAffiliateLinks($text, $affiliates, $factory = null)
    {
        if (!class_exists('DOMDocument') || empty($text))
		{
            return $text;
        }

        $factory = $factory ? $factory : new \NRFramework\Factory();

		libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $dom->encoding = 'UTF-8';
        $dom->loadHTML($text);

        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link)
        {
            $linkHref = $link->getAttribute('href');

            if (empty($linkHref))
            {
                continue;
            }

            $url = new URL($linkHref, $factory);

            if ($url->isInternal())
            {
                continue;
            }

            $domain = $url->getDomainName();

            if (!array_key_exists($domain, $affiliates))
            {
                continue;
            }

            $urlInstance = $url->getInstance();
            $urlQuery = $urlInstance->getQuery();
            $affQuery = $affiliates[$domain];

            // If both queries are the same, skip the link tag
            if ($urlQuery === $affQuery)
            {
                continue;
            }

            if (empty($urlQuery))
            {
                $urlInstance->setQuery($affQuery);
            } else 
            {
                parse_str($urlQuery, $params);
                parse_str($affQuery, $params_);
                $params_new = array_merge($params, $params_);
                $urlInstance->setQuery(http_build_query($params_new));
            }

            $newURL = $urlInstance->toString();

            if ($newURL === $linkHref)
            {
                continue;
            }

            $link->setAttribute('href', $newURL);
        }

        return $dom->saveHtml();
    }

    /**
     * Convert all <img> and <a> tags with relative paths to absolute URLs
     *
     * @param  string   $text          The text/HTML to search for relative paths
     * @param  object   $factory       The framework's factory
     * @param  object   $fix_links     Should we parse links?
     * @param  object   $fix_images    Should we parse images?
     *
     * @return void     The converted HTML string
     */
    public static function relativePathsToAbsoluteURLs($text, $factory = null, $fix_links = true, $fix_images = true)
    {
        // Make sure DOMDocument is installed
        if (!class_exists('DOMDocument'))
		{
            return $text;
        }

        // Quick check the given text has some links or images
        $hasImages = $fix_images && strpos($text, '<img') !== false;
        $hasLinks  = $fix_links && strpos($text, '<a') !== false;

        if (empty($text) || (!$hasImages && !$hasLinks))
        {
            return $text;
        }

        $factory = $factory ? $factory : new \NRFramework\Factory();
        $replacements = 0;

        try
        {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument;
            $dom->encoding = 'UTF-8';

            // Handle non-latin characters to UTF8
            $text_ = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');

            // Load HTML without adding a doctype.
            // Do not ever try to remove <html><body> tags with LIBXML_HTML_NOIMPLIED constant as it's rather unstable.
            // https://stackoverflow.com/questions/4879946/how-to-savehtml-of-domdocument-without-html-wrapper/44866403#44866403
            // LIBXML_HTML_NODEFDTD requires Libxml >= 2.7.8 - https://www.php.net/manual/en/libxml.constants.php
            $dom->loadHTML($text_, LIBXML_HTML_NODEFDTD);
    
            // Replace links
            if ($fix_links)
            {
                $links = $dom->getElementsByTagName('a');
        
                foreach ($links as $link)
                {
                    $resource = $link->getAttribute('href');
        
                    if (empty($resource) || mb_substr($resource, 0, 1) == '#')
                    {
                        continue;
                    }
        
                    $url = new URL($resource, $factory);
        
                    if (!$url->isInternal())
                    {
                        continue;
                    }
        
                    $newURL = $url->toAbsolute();
        
                    $link->setAttribute('href', $newURL);

                    $replacements++;
                }
            }
    
            // Replace images
            if ($fix_images)
            {
                $images = $dom->getElementsByTagName('img');

                foreach ($images as $image)
                {
                    $resource = $image->getAttribute('src');

                    if (empty($resource))
                    {
                        continue;
                    }
        
                    $url = new URL($resource, $factory);
        
                    if (!$url->isInternal())
                    {
                        continue;
                    }
        
                    $newURL = $url->toAbsolute();
        
                    $image->setAttribute('src', $newURL);

                    $replacements++;
                }
            }

            // If we don't have any replacements took place, proceed no further and return the original text.
            if ($replacements == 0)
            {
                return $text;
            }

            $html = trim($dom->saveHTML());

            // Make sure no <body> or <html> tags are added in the text
            // In case the final string starts with <html><body>, we assume the elements are added by DOMDocument incorectly and we remove them.
            // In case the final string starts with <html lang="en-gb" dir="ltr"><head>..., we assume the elements are included in the original text and we must leave them.
            if (strpos($html, '<html><body>') !== false)
            {
                $html = str_replace(['<html><body>', '</body></html>'], '', $html);
            }

            return $html;

        } catch (\Throwable $th)
        {
            return $text;
        }
    }
}