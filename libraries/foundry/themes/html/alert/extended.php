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
<div class="o-alert o-alert--<?php echo $class;?> <?php echo $customClass; ?> <?php echo $dismissible ? 'o-alert--dismissible' : '';?>" data-fd-alert="<?php echo $this->fd->getName();?>">
	<div class="flex">
		<?php if ($icon) { ?>
		<div class="pr-md <?php echo !$button ? 't-align-items--c' : '';?>">
			<i class="fdi <?php echo $icon; ?>"></i>
		</div>
		<?php } ?>

		<div class="flex-grow space-y-xs ">
			<div class="flex">
				<div class="flex-grow">
					<b data-fd-alert-title>
						<?php echo JText::_($title); ?>
					</b>
				</div>

				<?php if ($dismissible) { ?>
				<div class="flex-shrink-0 pl-sm">
					<a href="javascript:void(0);" class="o-alert__close" data-fd-dismiss>×</a>
				</div>
				<?php } ?>
			</div>

			<?php if ($description) { ?>
			<div data-fd-alert-message>
				<?php echo JText::_($description); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php if ($button) { ?>
	<div class="text-right">
		<?php echo $button; ?>
	</div>
	<?php } ?>
</div>
