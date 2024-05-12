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
<div class="cb-item" data-key="<?php echo $conditionKey ?>">
    <div class="cb-item-toolbar">
        <div class="cb-dropdown">
            <?php echo $toolbar->renderFieldset('base'); ?>
        </div>
        <div class="cb-item-buttons">
            <svg class="loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25px" height="25px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <circle cx="50" cy="50" fill="none" stroke="#dddddd" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
                </circle>
            </svg>
            <div class="links">
                <a class="cb-button only-icon remove tf-cb-remove-condition" href="#" title="<?php echo \JText::_('NR_CB_TRASH_CONDITION'); ?>"><span class="icon icon-trash"></span></a>
                <a class="cb-button only-icon tf-cb-add-new-group" href="#" title="<?php echo \JText::_('NR_CB_ADD_CONDITION'); ?>"><span class="icon icon-plus-2"></span></a>
            </div>
            <div class="toggle-status" title="<?php echo \JText::_('NR_CB_TOGGLE_RULE_STATUS') ?>">
                <?php
                require_once JPATH_PLUGINS . '/system/nrframework/fields/nrtoggle.php';
                $field = new \JFormFieldNRToggle();
                $element = new \SimpleXMLElement('<field name="' . $name . '[enabled]" type="NRToggle" class="small" checked="' . $enabled . '" />');
                $field->setup($element, null);
                echo $field->__get('input');
                ?>
            </div>
        </div>
    </div>
    <div class="cb-item-content">
        <?php echo $options ? $options : '<div class="select-condition-message">' . JText::_('NR_CB_SELECT_CONDITION_GET_STARTED') . '</div>'; ?>
    </div>
</div>