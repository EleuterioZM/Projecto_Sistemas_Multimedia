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
<div class="font-sans text-center bg-gray-50 <?php echo $padding;?> <?php echo $rounded ? 'rounded-md' : '';?> <?php echo $wrapperClass;?>">
	<?php if ($icon) { ?>
	<div class="mb-lg">
		<?php echo $this->fd->html('icon.font', 'text-3xl leading-xl ' . $icon); ?>
	</div>
	<?php } ?>

	<div class="text-sm leading-sm">
		<?php echo $title;?>
	</div>

	<?php if ($actions) { ?>
	<div class="mt-lg">
		<?php echo $actions; ?>
	</div>
	<?php } ?>
</div>
