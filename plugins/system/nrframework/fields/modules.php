<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldModules extends NRFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return   array   An array of JHtml options.
     */
    protected function getOptions()
    {
        $db = $this->db;
        
        $query = $db->getQuery(true);
        
        $query->select('*')
        ->from('#__modules')
        ->where('published=1')
        ->where('access !=3')
        ->order('title');
        
        $client = isset($this->element['client']) ? (int) $this->element['client'] : false;

        if ($client !== false)
        {
            $query->where('client_id = ' . $client);
        }

        $rows = $db->setQuery($query);
        $results = $db->loadObjectList();

        $options = array();

        if ($this->showSelect())
        {
            $options[] = JHTML::_('select.option', "", '- ' . JText::_("NR_SELECT_MODULE") . ' -');
        }

        foreach ($results as $option)
        {
            $options[] = JHTML::_('select.option', $option->id, $option->title);
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}