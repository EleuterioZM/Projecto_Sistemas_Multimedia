<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

use ConvertForms\Helper;
use ConvertForms\Form;

defined('_JEXEC') or die();

class SmartTags
{
    public static function replace($string, $submission = null, $form_id = null)
    {
        // Add Submission Tags to collection
        $smartTags = new \NRFramework\SmartTags();

        $extra_data = [];

        if (is_object($submission))
        {
            // Get extra data from plugins that listen to the event
            \JFactory::getApplication()->triggerEvent('onConvertFormsGetSubmissionSmartTags', [$submission, &$extra_data]);
        }

        // Register CF Smart Tags
        $smartTags->register(
            '\ConvertForms\SmartTags',
            JPATH_SITE . '/administrator/components/com_convertforms/ConvertForms/SmartTags',
            [
                'submission' => $submission,
                'extra_data' => $extra_data
            ]
        );

        $result = $smartTags->replace($string);

        // Temporary fix for duplicate site URL.
        // Since v2.8.0 that we don't force absoluste URLs in the editors, we may don't need to fix duplicate site URL on the fly any longer.
        // We need to keep it though for a while to prevent backwards compatibility issues.
        $result = self::fixDuplicateSiteURL($result);
        
        return $result;
    }

    /**
     * In TinyMCE we are forcing absolute URLs (relative_urls=false). This means that the editors prefixes all 'src' and 'href' properties 
     * with the site's base URL. If we try to use a File Upload Field Smart Tag in a link like in the example below:
     *  
     * <a href="{field.myuploadfield}">Download File</a>
     * 
     * The editor will transform the link into
     * 
     * <a href="http://www.mysite.com/{field.myuploadfield}">Download File</a>
     * 
     * Even though since v2.7.4 we store the relative path instead of the absolute URL in the database, this issue is not resolved as the relative path
     * is transformed into an absolute URL by the prepareValue() method before the value arrives in this method.
     *
     * @param  string $string
     *
     * @return string
     */
    private static function fixDuplicateSiteURL($subject)
    {
        if (is_string($subject) && strpos($subject, 'http') !== false)
        {
            $domain = \JFactory::getApplication()->input->server->get('HTTP_HOST', '', 'STRING');
            /**
             * $domain returns www.site.com
             * The regex below will not be able to find the duplicate URL given the $subject:
             * https://site.com/https://www.site.com/path/to/file
             * Once we drop the "www." part from $domain, we will be able to successfully find and replace the duplicate URL.
             */
            $domain = str_replace('www.', '', $domain);
            return preg_replace('#http(s)?:\/\/(.*?)' . $domain . '(.*?)\/http#', 'http', $subject);
        }

        if (is_array($subject))
        {
            foreach ($subject as $key => &$item)
            {
                if (!is_string($item))
                {
                    continue;
                }
    
                $item = self::fixDuplicateSiteURL($item);
            }
        }

        return $subject;
    }
}

?>