<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

if (!is_array($fields) || empty($fields))
{
    return;
}

?>
<div class="nr-responsive-control<?php echo $class; ?>" style="width:<?php echo $width; ?>">
    <div class="top">
        <span class="title"><?php echo $title; ?></span>
        <div class="actions">
            <a href="#" data-type="desktop" title="<?php echo JText::_('NR_DESKTOPS_WITH_BREAKPOINT_INFO'); ?>" class="nr-responsive-control-type-btn icon-screen is-active"></a>
            <a href="#" data-type="tablet" title="<?php echo JText::_('NR_TABLETS_WITH_BREAKPOINT_INFO'); ?>" class="nr-responsive-control-type-btn icon-tablet"></a>
            <a href="#" data-type="mobile" title="<?php echo JText::_('NR_MOBILES_WITH_BREAKPOINT_INFO'); ?>" class="nr-responsive-control-type-btn icon-mobile"></a>
        </div>
    </div>
    <div class="content">
        <div class="item desktop is-active">
            <?php echo $fields['desktop']; ?>
        </div>
        <div class="item tablet">
            <?php echo $fields['tablet']; ?>
        </div>
        <div class="item mobile">
            <?php echo $fields['mobile']; ?>
        </div>
    </div>
</div>