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

defined('_JEXEC') or die('Restricted access');

class JFormFieldServiceLists extends JFormFieldText
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        $this->addMedia();

        return implode(" ", array(
            parent::getInput(),
            '<button type="button" class="btn btn-secondary viewLists">
                <span class="icon-loop"></span> Lists
            </button>
            <ul class="cflists"></ul>'
        ));
    }

    /**
     *  Adds field's JavaScript and CSS files to the document
     */
    private function addMedia()
    {
        JHtml::stylesheet('com_convertforms/servicelists.css', ['relative' => true, 'version' => 'auto']);
        JHtml::script('com_convertforms/servicelists.js', ['relative' => true, 'version' => 'auto']);
    }
}