<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNRURL extends NRFormField
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    function getInput()
    {   
        $url    = $this->get("url", "#");
        $target = $this->get("target", "_blank");
        $text   = $this->get("text");
        $class  = $this->get("class");
        $icon   = $this->get("icon", null);

        $url = str_replace("{{base}}", JURI::base(), $url);
        $url = str_replace("{{root}}", JURI::root(), $url);

        $html[] = '<a class="nrurl ' . $class . '" href="' . $url . '" target="' . $target . '">';

        if ($icon)
        {
            $html[] = '<span class="icon-'.$icon.'"></span>';
        }

        $html[] = $this->prepareText($text);
        $html[] = '</a>';

        // Add CSS to the page
        $run = false;
        if (!$run)
        {
            JFactory::getDocument()->addStyleDeclaration('
                .nrurl.disabled {
                    pointer-events: none;
                }
            ');

            $run = true;
        }

        return implode(" ", $html);
    }
}