<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldCampaigns extends JFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions() 
    {
        $lists = ConvertForms\Helper::getCampaigns();

        if (!count($lists))
        {
            return;
        }

        $options = array();

        foreach ($lists as $option)
        {
            $options[] = JHTML::_('select.option', $option->id, $option->name);
        }

        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}