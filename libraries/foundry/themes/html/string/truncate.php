<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-data-truncater overflow-hidden" data-fd-truncater="<?php echo $this->fd->getName();?>">
	<div class="truncate" data-text><?php echo $truncated; ?></div>
	<div class="t-hidden" data-original><?php echo $original;?></div>

	<?php if ($showMore) { ?>
	<a href="javascript:void(0);" data-more><?php echo JText::_($readMoreText); ?></a>
	<?php } ?>
</div>
