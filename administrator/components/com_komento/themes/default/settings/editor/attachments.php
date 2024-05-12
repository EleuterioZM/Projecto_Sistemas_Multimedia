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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_ATTACHMENTS_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'upload_enable', 'COM_KOMENTO_SETTINGS_ATTACHMENT_ENABLE'); ?>
				<?php echo $this->fd->html('settings.text', 'upload_path', 'COM_KOMENTO_SETTINGS_ATTACHMENT_CUSTOM_PATH');?>
				<?php echo $this->fd->html('settings.text', 'upload_allowed_extension', 'COM_KOMENTO_SETTINGS_ATTACHMENT_ALLOWED_EXTENSION');?>
				<?php echo $this->fd->html('settings.text', 'upload_max_file', 'COM_KOMENTO_SETTINGS_ATTACHMENT_MAX_FILE', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_FILES'
				]);?>
				<?php echo $this->fd->html('settings.text', 'upload_max_size', 'COM_KOMENTO_SETTINGS_ATTACHMENT_MAX_SIZE', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_MB'
				]);?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md ">
					<div class="inline-flex align-middle md:mb-0 md:pr-md md:w-5/12 w-full flex-shrink-0">&nbsp;</div>
					<div class="flex-grow text-xs">
						<?php echo JText::sprintf('COM_KOMENTO_SETTINGS_PHP_MAX_FILESIZE', ini_get('upload_max_filesize')); ?><br>
						<?php echo JText::sprintf('COM_KOMENTO_SETTINGS_PHP_MAX_POSTSIZE', ini_get('post_max_size')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
	</div>
</div>