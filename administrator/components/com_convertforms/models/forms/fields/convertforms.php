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
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/' . 'models');

class JFormFieldConvertForms extends JFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return    array   An array of JHtml options.
     */
    protected function getOptions()
    {
        $model = JModelLegacy::getInstance('Forms', 'ConvertFormsModel', ['ignore_request' => true]);

        $state = isset($this->element['state']) ? (string) $this->element['state'] : 1;

        $model->setState('filter.state', explode(',', $state));

        $convertforms = $model->getItems();
        $options = array();

        foreach ($convertforms as $key => $convertform)
        {
            $name = $convertform->state != 1 ? $convertform->name . ' (' . JText::_('JUNPUBLISHED') . ')' : $convertform->name;
            $options[] = JHTML::_('select.option', $convertform->id, $name . ' (' . $convertform->id . ')');
        }   

        return array_merge(parent::getOptions(), $options);
    }
}