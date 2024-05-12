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

class JFormFieldNRModulePositions extends NRFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return   array   An array of JHtml options.
     */
    protected function getOptions()
    {
        // get templates
        $templates = $this->getTemplates();

        require_once JPATH_ADMINISTRATOR . '/components/com_templates/helpers/templates.php';

        // get all position options
        $options = [];

        $options[] = JHTML::_('select.option', '',  \JText::_('NR_NONE_SELECTED'));
        foreach ($templates as $template) {
            $options[] = JHTML::_('select.option', '<OPTGROUP>', $template->name);

            // find all positions for template
            $positions = TemplatesHelper::getPositions(0, $template->element);
            foreach ($positions as $position) {
                $options[] = JHTML::_('select.option', $position, $position);
            }

            $options[] = JHTML::_('select.option', '</OPTGROUP>');
        }

        return array_merge(parent::getOptions(), $options);
    }

    /**
     * Returns all enabled templates
     * 
     * @return  object
     */
    private function getTemplates()
    {
        $db = $this->db;

        $query = $db->getQuery(true);
        $query->select('element, name, enabled');
        $query->from('#__extensions');
        $query->where('client_id = 0');
        $query->where('type = '.$db->quote('template'));
        $query->where('enabled = 1');
        $db->setQuery($query);

        return $db->loadObjectList();
    }
}