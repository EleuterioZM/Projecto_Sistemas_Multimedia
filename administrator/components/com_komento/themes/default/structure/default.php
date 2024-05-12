<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="is-joomla-backend">
	<div id="kt" class="kt-component kt-backend si-theme--light <?php echo FH::isJoomla4() ? 'is-loading' : '';?>" data-kt-structure data-fd-structure>

		<?php if (FH::isJoomla4()) { ?>
			<?php echo $this->fd->html('loader.block'); ?>
		<?php } ?>

		<?php echo $this->fd->html('admin.outdated', 'COM_KT_OUTDATED_VERSION', $updateTaskUrl, 'COM_KOMENTO_DASHBOARD_UPDATE_NOW'); ?>

		<?php if ($tmpl !== 'component') { ?>
			<div class="app <?php echo FH::isJoomla4() ? 't-hidden' : '';?>" data-fd-body>
				<?php echo $sidebar; ?>

				<div class="app-content <?php echo !$heading ? 'pt-no' : '';?>">
					<?php echo KT::info()->html(true); ?>

					<?php if ($heading || $description) { ?>
						<?php echo $this->fd->html('admin.headers', $heading, $description); ?>
					<?php } ?>

					<div class="app-body">
						<?php if ($overlay) { ?>
							<?php echo $this->fd->html('overlay.grid', JText::_('COM_KT_UPGRADE_TO_PRO'), JText::_('COM_KT_UPGRADE_TO_PRO_DESC')); ?>
						<?php } ?>

						<?php echo $output; ?>
					</div>
				</div>
			</div>

			<?php if ($help) { ?>
				<?php echo $this->fd->html('admin.toolbarHelp', $help); ?>
			<?php } ?>
			
			<?php echo $this->fd->html('admin.toolbarSaveGroup'); ?>
		<?php } ?>

		<?php if ($tmpl === 'component') { ?>
			<?php echo $output; ?>
		<?php } ?>
	</div>
</div>

<?php echo $this->fd->html('html.popover'); ?>