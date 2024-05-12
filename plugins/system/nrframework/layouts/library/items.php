<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>
<div class="tf-library-list">
    <div class="tf-library-item blank_popup">
        <span class="tf-library-item-wrap">
            <a class="parent" href="<?php echo $create_new_template_link; ?>">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor"/>
                    <line x1="12" y1="7.5" x2="12" y2="16.5" stroke="currentColor" stroke-linecap="round"/>
                    <line x1="16.5" y1="12" x2="7.5" y2="12" stroke="currentColor" stroke-linecap="round"/>
                </svg>
                <span class="title"><?php echo $blank_template_label; ?></span>
                <span class="description"><?php echo \JText::_('NR_START_FROM_SCRATCH'); ?></span>
            </a>
        </span>
    </div>
    <?php
    // Skeleton
    for ($i = 0; $i < 15; $i++)
    {
        ?>
        <div class="tf-library-item skeleton">
            <div class="tf-library-item-wrap">
                <div></div>
                <div class="actions">
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>