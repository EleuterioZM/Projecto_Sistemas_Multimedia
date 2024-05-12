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

$items_payload = [
    'create_new_template_link' => $create_new_template_link,
    'blank_template_label' => $blank_template_label
];
$footer_payload = [
    'create_new_template_link' => $create_new_template_link,
    'project_name' => $project_name
];

$layouts_path = JPATH_PLUGINS . '/system/nrframework/layouts';

JHtml::_('jquery.framework');
?>
<div class="tf-library-page" data-preview-url="<?php echo $preview_url; ?>" data-options="<?php echo htmlspecialchars(json_encode($displayData)); ?>">
    <?php echo \JLayoutHelper::render('library/sidebar', [], $layouts_path); ?>
    <div class="tf-library-body">
        <?php
            echo \JLayoutHelper::render('library/toolbar', [], $layouts_path);
            echo \JLayoutHelper::render('library/noresults', [], $layouts_path);
            echo \JLayoutHelper::render('library/items', $items_payload, $layouts_path);
            echo \JLayoutHelper::render('library/footer', $footer_payload, $layouts_path);
        ?>
    </div>
</div>