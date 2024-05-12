<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

class JFormFieldRuleValueHint extends JFormField
{
    protected function getLabel()
    {
        return;
    }

    /**
     *  Method to render the input field
     *
     *  @return  string
     */
    protected function getInput()
    {
        $ruleName = (string) $this->element['ruleName'];
        $rule = \NRFramework\Factory::getCondition($ruleName);

        return '<div class="ruleValueHint">' . $rule->getValueHint() . '</div>';
    }
}