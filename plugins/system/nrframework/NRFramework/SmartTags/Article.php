<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

use NRFramework\Conditions\Conditions\Component\ContentBase;

defined('_JEXEC') or die('Restricted access');

class Article extends SmartTag
{
    /**
     * Fetch a property from the User object
     *
     * @param   string  $key   The name of the property to return
     *
     * @return  mixed   Null if property is not found, mixed if property is found
     */
    public function fetchValue($key)
    {
        $contentAssignment = new ContentBase();
        
        if (!$contentAssignment->isSinglePage())
        {
            return;
        }

        // Why the heck $isSinglePage below returns false?
        // $articleAssignment = new \NRFramework\Conditions\Component\ContentArticle();
        // $isSinglePage = $articleAssignment->pass();

        $article = $contentAssignment->getItem();

        if (!isset($article->{$key}) || is_object($article->{$key}))
        {
            return;
        }

        return $article->{$key};
    }
}