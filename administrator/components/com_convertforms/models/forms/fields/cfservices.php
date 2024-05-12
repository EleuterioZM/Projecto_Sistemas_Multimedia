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
JFormHelper::loadFieldClass('list');

class JFormFieldCFServices extends JFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        // Trigger all ConvertForms plugins
        JPluginHelper::importPlugin('convertforms');

        // Get a list with all available services
        $services = JFactory::getApplication()->triggerEvent('onConvertFormsServiceName');

        $options[] = JHTML::_('select.option', '0', JText::_('JDISABLED'));

        // Alphabetically sort services
        asort($services);

        foreach ($services as $option)
        {
            $options[] = JHTML::_('select.option', $option['alias'], $option['name']);
        }

        return array_merge(parent::getOptions(), $options);
    }
}