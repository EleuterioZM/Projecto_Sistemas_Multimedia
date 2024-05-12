<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('JPATH_PLATFORM') or die;

use NRFramework\HTML;

JFormHelper::loadFieldClass('list');

class JFormFieldAssignmentSelection extends JFormFieldList
{
	/**
	 *  Assignment options
	 *
	 *  @var  array
	 */
    protected $options = array(
        0 => 'JDISABLED',
        1 => 'NR_INCLUDE',
        2 => 'NR_EXCLUDE'
    );

    /**
     *  Return options list to field
     *
     *  @return  array
     */
    protected function getOptions()
    {
        foreach ($this->options as $key => $value)
        {
            $options[] = JHTML::_('select.option', $key, JText::_($value));
        }

        return array_merge(parent::getOptions(), $options);
    }

    /**
     *  Setup field with predefined classes and load its media files
     *
     *  @param   SimpleXMLElement  $element  
     *  @param   String            $value   
     *  @param   String            $group    
     *
     *  @return  SimpleXMLElement                    
     */
    public function setup(SimpleXMLElement $element, $value, $group = NULL)
    {
        $return = parent::setup($element, $value, $group);

        JHtml::_('jquery.framework');

		HTML::script('plg_system_nrframework/assignmentselection.js');
		HTML::stylesheet('plg_system_nrframework/assignmentselection.css');

        $this->class = 'assignmentselection chzn-color-state';

        return $return;
    }
}