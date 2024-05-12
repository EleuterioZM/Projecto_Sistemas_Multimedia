<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('plg_system_nrframework/conditionbuilder.css', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_system_nrframework/helper.js', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_system_nrframework/conditionbuilder.js', ['relative' => true, 'version' => 'auto']);

extract($displayData);
?>
<div class="cb"
    data-token="<?php echo JSession::getFormToken(); ?>"
    data-root="<?php echo JURI::base(); ?>"
    data-option="<?php echo JFactory::getApplication()->input->get('option'); ?>"
    data-layout="<?php echo JFactory::getApplication()->input->get('layout'); ?>"
    data-include-rules="<?php echo $include_rules; ?>"
    data-exclude-rules="<?php echo $exclude_rules; ?>">
    <div class="cb-groups"></div>
    <div class="actions">
        <a class="icon tf-cb-add-new-group" href="#" title="<?php echo JText::_('NR_CB_ADD_CONDITION_GROUP'); ?>">
            <span class="icon icon-plus-2"></span>
            <span class="text"><?php echo JText::_('NR_CB_ADD_CONDITION_GROUP'); ?></span>
            <svg class="loading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <circle cx="50" cy="50" fill="none" stroke="#333" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
                </circle>
            </svg>
        </a>
    </div>
</div>

<?php
echo $geo_modal;
if (!empty($available_condititions))
{
    \NRFramework\HTML::renderProOnlyModal();
}
?>