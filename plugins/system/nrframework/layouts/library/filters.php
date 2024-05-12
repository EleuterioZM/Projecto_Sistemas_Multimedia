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

if (!$filters)
{
    return;
}
?>
<div class="tf-library-filters">
    <?php
    foreach ($filters as $key => $filter)
    {
        ?>
        <div class="tf-library-filter-item open" data-type="<?php echo $key; ?>">
            <div class="tf-library-filter-item-label">
                <span><?php echo $filter['label']; ?></span>
                <svg class="tf-library-filter-item-toggle" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" d="M9 1.5L5.70711 4.79289C5.31658 5.18342 4.68342 5.18342 4.29289 4.79289L1 1.5" stroke="currentColor" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="tf-library-filter-choices">
            <?php
            foreach ($filter['items'] as $_key => $label)
            {
                $choice_item_key = 'tf_library_filters' . $key . '_filter_' . $_key;
                ?>
                <div class="tf-library-filter-choice-item">
                    <input type="checkbox" class="tf-library-filter-choice-item-checkbox" id="<?php echo $choice_item_key; ?>" value="<?php echo $label; ?>" />
                    <label for="<?php echo $choice_item_key; ?>"><?php echo $label ?></label>
                </div>
                <?php
            }
            ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>