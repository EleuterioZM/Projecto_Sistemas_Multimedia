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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_MODERATION'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_moderation', 'COM_KOMENTO_SETTINGS_ENABLE_MODERATION'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'requires_moderation', 'COM_KOMENTO_SETTINGS_MODERATION_USERGROUP'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_SETTINGS_AUTOMATED_MODERATION'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_auto_moderation', 'COM_KT_SETTINGS_ENABLE_AUTOMATED_MODERATION'); ?>

				<?php echo $this->fd->html('settings.userGroupsTree', 'requires_auto_moderation', 'COM_KT_SETTINGS_AUTOMATED_MODERATION_USERGROUP'); ?>

				<?php echo $this->fd->html('settings.text', 'moderation_threshold', 'COM_KT_SETTINGS_MODERATION_THRESHOLD', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_COMMENTS'
				]);?>
				
			</div>
		</div>
	</div>
</div>