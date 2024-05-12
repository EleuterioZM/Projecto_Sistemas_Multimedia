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

if (!$items || !is_array($items) || !count($items))
{
	return;
}

if (!$readonly && !$disabled && $lightbox)
{
    JHtml::script('plg_system_nrframework/widgets/gallery/gallery.js', ['relative' => true, 'version' => 'auto']);
}

if ($load_stylesheet)
{
	JHtml::stylesheet('plg_system_nrframework/widgets/gallery.css', ['relative' => true, 'version' => 'auto']);
}

?>

<div class="nrf-widget tf-gallery-wrapper<?php echo $css_class; ?>" data-id="<?php echo $id; ?>">
    <div class="gallery-items<?php echo $gallery_items_css; ?>">
    <?php
        foreach ($items as $index => $item)
        {
            // If its an invalid image path, show a warning and continue
            if (isset($item['invalid']) && $show_warnings)
            {
                echo '<div><strong>Warning:</strong> ' . sprintf(\JText::_('NR_INVALID_IMAGE_PATH'), $item['path']) . '</div>';
                continue;
            }

            $item['index'] = $index;
            $displayData['item'] = $item;
            echo $this->sublayout('item', $displayData);
        }
    ?>
    </div>
    <?php 
        if ($lightbox)
        {
            echo $this->sublayout('glightbox', $displayData);
        }
    ?>
</div>