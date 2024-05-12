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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'jomsocial_enable_comment', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'jomsocial_enable_reply', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_REPLY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'jomsocial_enable_like', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_LIKE'); ?>
				<?php echo $this->fd->html('settings.text', 'jomsocial_comment_length', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_COMMENT_LENGTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_CHARACTERS'
				]);?>
				<?php echo $this->fd->html('settings.toggle', 'jomsocial_enable_userpoints', 'COM_KOMENTO_SETTINGS_ACTIVITIES_JOMSOCIAL_ENABLE_USERPOINTS'); ?>
			</div>
		</div>
	</div>
</div>

