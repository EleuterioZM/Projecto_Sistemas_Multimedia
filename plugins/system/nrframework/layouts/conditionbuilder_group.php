<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>
<div class="cb-group" data-key="<?php echo $groupKey ?>">
    <div class="cb-item-toolbar group">
        <div class="show">
            <?php echo JText::_('NR_CB_SHOW_WHEN'); ?>
            <select name="<?php echo $name; ?>[<?php echo $groupKey; ?>][matching_method]" class="form-select width-auto form-select-small">
                <option <?php echo (isset($groupConditions['matching_method']) && $groupConditions['matching_method'] == 'all') ? 'selected ' : ''; ?>value="all"><?php echo strtolower(JText::_('NR_ALL')); ?></option>
                <option <?php echo (isset($groupConditions['matching_method']) && $groupConditions['matching_method'] == 'any') ? 'selected ' : ''; ?>value="any"><?php echo strtolower(JText::_('NR_ANY')); ?></option>
            </select>
            <?php echo JText::_('NR_CB_OF_THE_CONDITIONS_MATCH'); ?>
        </div>
        <div class="cb-item-buttons">
            <div class="links">
                <a class="cb-button only-icon remove removeGroupCondition" href="#" title="<?php echo \JText::_('NR_CB_TRASH_CONDITION_GROUP'); ?>"><span class="icon icon-trash"></span></a>
            </div>
            <div class="toggle-status" title="<?php echo \JText::_('NR_CB_TOGGLE_RULE_GROUP_STATUS') ?>">
                <?php
                $checked = isset($groupConditions['enabled']) && (string) $groupConditions['enabled'] == '1';
                require_once JPATH_PLUGINS . '/system/nrframework/fields/nrtoggle.php';
                $field = new \JFormFieldNRToggle();
                $element = new \SimpleXMLElement('<field name="' . $name . '[' . $groupKey . '][enabled]" type="NRToggle" class="small" checked="' . $checked . '" />');
                $field->setup($element, null);
                echo $field->__get('input');
                ?>
            </div>
        </div>
    </div>
    <div class="cb-items">
        <?php
        // Array of conditions items in HTML format
        if (isset($condition_items_parsed) && is_array($condition_items_parsed))
        {
            foreach ($condition_items_parsed as $html)
            {
                echo $html;
            }
        }
        // Render conditions items in raw format
        else if (isset($groupConditions['rules']))
        {
            foreach ($groupConditions['rules'] as $conditionKey => $condition)
            {
                echo \NRFramework\Conditions\ConditionBuilder::add($name, $groupKey, $conditionKey, (array) $condition, $include_rules, $exclude_rules);
            }
        }
        ?>
    </div>

    <div class="item-group-footer text-right text-end">
        <a class="cb-button icon btn outline tf-cb-add-new-group" href="#" title="<?php echo JText::_('NR_CB_ADD_CONDITION'); ?>">
            <span class="icon icon-plus-2"></span>
            <span class="text"><?php echo JText::_('NR_CB_ADD_CONDITION'); ?></span>
            <svg class="loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <circle cx="50" cy="50" fill="none" stroke="#333" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
                </circle>
            </svg>
        </a>
    </div>
</div>