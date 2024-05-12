<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('number');

class JFormFieldNRNumber extends JFormFieldNumber
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    function getInput()
    {   
        $parent = parent::getInput();
        $addon  = (string) $this->element['addon'];

        if (empty($addon))
        {
            return $parent;
        }

        return '
            <div class="input-append input-group">
                ' . $parent . '
                <span class="add-on input-group-append">
                    <span class="input-group-text" style="font-size:inherit;">' . JText::_($addon) . '</span>
                </span>
            </div>
        ';
    }
}
