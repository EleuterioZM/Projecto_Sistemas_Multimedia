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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-5 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_EMAILS_EDITOR_FILE_INFO'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_EMAILS_EDITOR_FILE_LOCATION', 'filepath'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'filepath', $file->path, 'filepath', ['disabled' => true]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_KOMENTO_EMAILS_EDITOR_OVERRIDE_FILE_LOCATION', 'overridepath'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'overridepath', $file->overridePath, 'overridepath', ['disabled' => true]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-1 md:col-span-7 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_EMAILS_EDITOR_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $editor->display('source', $file->contents, '100%', '400px', 80, 20, false, null, null, null, array('syntax' => 'php', 'filter' => 'raw')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->fd->html('form.action', 'mailq', 'mailq'); ?>
	<input type="hidden" name="file" value="<?php echo base64_encode($file->relative);?>" />
	<input type="hidden" name="base" value="<?php echo $file->base ? 1 : 0; ?>" />
</form>