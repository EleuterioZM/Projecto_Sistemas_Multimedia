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
<div data-fd-dropdown-wrapper>	
	<div class="inline" 
		data-fd-dropdown
		data-fd-dropdown-placement="<?php echo $placement;?>" 
		data-fd-dropdown-trigger="<?php echo $trigger;?>"
		data-fd-dropdown-target="<?php echo $target;?>"
		data-fd-dropdown-arrow="<?php echo $arrow ? 1 : 0;?>"

		<?php if ($content) { ?>
		data-fd-dropdown-content="<?php echo $content;?>"
		<?php } ?>

		<?php if ($mount) { ?>
		data-fd-dropdown-mount="<?php echo $mount;?>"
		<?php } ?>

		<?php if ($destroy) { ?>
		data-fd-dropdown-destroy="<?php echo $destroy;?>"
		<?php } ?>

		<?php if ($hidden) { ?>
		data-fd-dropdown-hidden="<?php echo $hidden;?>"
		<?php } ?>

		<?php if ($create) { ?>
		data-fd-dropdown-create="<?php echo $create;?>"
		<?php } ?>

		<?php if ($show) { ?>
		data-fd-dropdown-show="<?php echo $show;?>"
		<?php } ?>
	>
		<?php echo $button; ?>
	</div>
	
	<div class="t-hidden" data-fd-dropdown-items>
		<div id="fd">
			<div class="<?php echo $appearance;?> si-theme-<?php echo $accent;?>">
				<div class="o-dropdown <?php echo $divider ? 'o-dropdown divide-y divide-gray-200' : '';?> <?php echo $class;?>">
					<?php if ($header) { ?>
						<?php echo $this->fd->html('dropdown.header', $header); ?>
					<?php  } ?>

					<div class="o-dropdown__bd  <?php echo !$footer ? 'rounded-bl-md rounded-br-md' : '';?>">
						<?php if (is_array($items)) { ?>
							<div class="o-dropdown-nav px-xs py-sm">
								<?php foreach ($items as $item) { ?>
									<?php echo $item; ?>
								<?php } ?>
							</div>
						<?php } else { ?>
							<?php echo $items; ?>
						<?php } ?>
					</div>

					<?php if ($footer) { ?>
						<?php echo $this->fd->html('dropdown.footer', $footer); ?>
					<?php  } ?>
				</div>
			</div>
		</div>
	</div>
</div>