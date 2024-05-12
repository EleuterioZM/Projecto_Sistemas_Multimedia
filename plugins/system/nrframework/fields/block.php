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

###### Note ######
# This field is deprecated. Use NR_Well instead
###### Note ######

class JFormFieldNR_Block extends NRFormField
{
    /**
     * The field type.
     *
     * @var         string
     */
    public $type = 'nr_block';

    protected function getLabel()
    {
        return '';
    }

    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    protected function getInput()
    {
        JHtml::stylesheet('plg_system_nrframework/fields.css', false, true);

        $title       = $this->get('label');
        $description = $this->get('description');
        $class       = $this->get('class');
        $showclose   = $this->get('showclose', 0);
        $start       = $this->get('start', 0);
        $end         = $this->get('end', 0);
        $info        = $this->get("html", null);

        if ($info)
        {
            $info = str_replace("{{", "<", $info);
            $info = str_replace("}}", ">", $info);       
        }

        $html = array();

        if ($start || !$end)
        {
            $html[] = '</div>';

            if (strpos($class, 'alert') !== false)
            {
                $html[] = '<div class="alert ' . $class . '">';
            }
            else
            {
                $html[] = '<div class="well nr-well' . $class . '">';
            }
            if ($title)
            {
                $html[] = '<h4>' . $this->prepareText($title) . '</h4>';
            }
            if ($description)
            {
                $html[] = '<div class="well-desc">' . $this->prepareText($description) . $info . '</div>';
            }
            $html[] = '<div><div>';
        }

        if (!$start && !$end)
        {
            $html[] = '</div>';
        }

        return '</div>' . implode('', $html);
    }
}