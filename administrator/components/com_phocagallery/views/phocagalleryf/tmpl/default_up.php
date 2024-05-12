<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access'); ?>

<div class="ph-item-box">
		<div class="ph-item-image"><a href="index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component&amp;folder=<?php echo PhocaGalleryText::filterValue($this->t['state']->parent, 'folderpath'); ?>&amp;field=<?php echo htmlspecialchars($this->field); ?>" ><span class="ph-cp-item"><i class="phi duotone phi-fs-l phi-fc-bl icon-arrow-up"></i></span></a></div>

	    <div class="ph-item-name"><a href="index.php?option=com_phocagallery&amp;view=phocagalleryf&amp;tmpl=component&amp;folder=<?php echo PhocaGalleryText::filterValue($this->t['state']->parent, 'folderpath'); ?>&amp;field=<?php echo htmlspecialchars($this->field); ?>" >..</a></div>

        <div class="ph-item-action-box"></div>
</div>
