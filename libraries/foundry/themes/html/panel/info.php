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
<div class="flex flex-col md:flex-row px-xs py-md">
	<div class="flex">
		<div class="flex-shrink-0">
			<?php if ($image) { ?>
				<?php if ($link) { ?>
				<a href="<?php echo $link;?>" target="_blank">
				<?php } ?>
					<img src="<?php echo $image;?>" align="left" class="mr-md mb-md" width="<?php echo $imageSize;?>" style="width: <?php echo $imageSize;?>px;" />
				<?php if ($link) { ?>
				</a>
				<?php } ?>
			<?php } ?>
		</div>

		<div class="flex flex-grow flex-col">
			<?php echo $text;?>

			<?php if ($link) { ?>
			<div class="mt-sm">
				<?php echo $this->fd->html('button.link', $link, $buttonText, 'default', 'sm', ['attributes' => 'target="_blank"']); ?>
			</div>
			<?php } ?>
		</div>
	</div>

</div>
