<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use NRFramework\Conditions\ConditionBuilder;
use NRFramework\Extension;

JFormHelper::loadFieldClass('hidden');

class JFormFieldConditionBuilder extends JFormFieldHidden
{
    /**
     *  Method to render the input field
     *
     *  @return  string
     */
    protected function getInput()
    {
       // Condition Builder relies on com_ajax for AJAX requests.
       if (!Extension::componentIsEnabled('ajax'))
       {
           \JFactory::getApplication()->enqueueMessage(\JText::_('AJAX Component is not enabled.'), 'error');
           return;
       }

        // This is required on views we don't control such as the Fields or the Modules view page.
        JHtml::_('formbehavior.chosen', '.hasChosen');

        JHtml::stylesheet('plg_system_nrframework/fields.css', ['relative' => true, 'version' => 'auto']);
        JHtml::stylesheet('plg_system_nrframework/joomla' . (defined('nrJ4') ? '4' : '3') . '.css', ['relative' => true, 'version' => 'auto']);

        \JText::script('NR_CB_SELECT_CONDITION_GET_STARTED');
        \JText::script('NR_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM');

        // Value must be always be a JSON string.
        if (is_array($this->value))
        {
            $this->value = json_encode($this->value);
        }

        // If field is empty, initialize it with an empty Condition Group

        $payload = [
            'include_rules' => isset($this->getOptions()['include_rules']) ? ConditionBuilder::prepareXmlRulesList($this->getOptions()['include_rules']) : '',
            'exclude_rules' => isset($this->getOptions()['exclude_rules']) ? ConditionBuilder::prepareXmlRulesList($this->getOptions()['exclude_rules']) : '',
            'geo_modal' => ConditionBuilder::getGeoModal() // Out of context
        ];

        return '
            <div class="cb-wrapper">
                ' . parent::getInput() . ConditionBuilder::getLayout('conditionbuilder', $payload) . '
            </div>
        ';
    }

    /**
     * Returns the field options.
     * 
     * @return  array
     */
    protected function getOptions()
    {
        $options = [
            'include_rules' => (string) $this->element['include_rules'],
            'exclude_rules' => (string) $this->element['exclude_rules']
        ];
        
        return $options;
    }
}