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
<div class="space-y-md">
	<?php for($i = 0; $i < $rows; $i++) { ?>
		<div class="o-placeholder-loader">
			<div class="flex w-full">
				<?php if ($avatar) { ?>
				<div class="flex-shrink-0 h-[40px] w-[40px]">
					<div class="o-placeholder-box h-[40px] w-[40px] <?php echo $class; ?>"></div>
				</div>
				<?php } ?>

				<div class="<?php echo $avatar ? 'ml-md' : '';?> space-y-xs flex flex-grow flex-col">
					<?php foreach ($widthRatio as $width) { ?>
					<div class="o-placeholder-box w-<?php echo $width; ?>"></div>
					<?php } ?>
				</div>

				<?php if ($aspectRatio) { ?>
				<div class="<?php echo $shrinkAspectRatio ? 'flex-shrink-0' : ''; ?>">
					<div class="o-aspect-ratio min-w-[<?php echo $aspectRatioSize; ?>px] <?php echo $roundedAspectRatio ? 'rounded-md' : '';?> overflow-hidden" style="--aspect-ratio: <?php echo $ratio;?>">
						<div class="o-placeholder-box"></div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>