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

class JFormFieldNR_Well extends NRFormField
{
    /**
     * The field type.
     *
     * @var string
     */
    public $type = 'nr_well';

    /**
     * Layout to render the form field
     *
     * @var  string
     */
    protected $renderLayout = 'well';

    /**
     * Override renderer include path
     *
     * @return  array
     */
    protected function getLayoutPaths()
    {
        return JPATH_PLUGINS . '/system/nrframework/layouts/';
    }

    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    protected function getInput()
    {   
        JHtml::stylesheet('plg_system_nrframework/fields.css', ['relative' => true, 'version' => 'auto']);

        $title       = $this->get('label');
        $description = $this->get('description');
        $url         = $this->get('url');
        $class       = $this->get('class');
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
            if ($title)
            {
                $html[] = '<h4>' . $this->prepareText($title) . '</h4>';
            }
            if ($description)
            {
                $html[] = '<div class="well-desc">' . $this->prepareText($description) . $info . '</div>';
            }

            if ($url)
            {
                if (defined('nrJ4'))
                {
                    $html[] = '<a class="btn btn-outline-secondary btn-sm wellbtn" target="_blank" href="' . $url . '"><span class="icon-info-circle"></span></a>';
                } else 
                {
                    $html[] = '<a class="btn btn-secondary wellbtn" target="_blank" href="' . $url . '"><span class="icon-info"></span></a>';
                }
            }
        }

        if ($end) {
            $html[] = '</div>';
        }

        return implode('', $html);
    }
}