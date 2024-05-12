<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldNRModules extends NRFormFieldList
{
    /**
     * Provide a list of all published modules.
     *
     * @return   array   An array of JHtml options.
     */
    protected function getOptions()
    {
        // Get modules
        $modules = $this->getModules();

        // get all position options
        $options = [];

        $options[] = JHTML::_('select.option', '',  \JText::_('NR_NONE_SELECTED'));
        foreach ($modules as $module) {
            $options[] = JHTML::_('select.option', $module->id, $module->title . ' (' . $module->id . ')');
        }

        return array_merge(parent::getOptions(), $options);
    }

    /**
     * Returns all enabled modules.
     * 
     * @return  object
     */
    private function getModules()
    {
        $db = $this->db;

        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__modules');
        $query->where('published = 1');
        $db->setQuery($query);

        return $db->loadObjectList();
    }
}