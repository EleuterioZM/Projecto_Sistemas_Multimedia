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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_EASYSOCIAL'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_KOMENTO_WHAT_IS_EASYSOCIAL', 'https://stackideas.com/easysocial', 'COM_KOMENTO_GET_EASYSOCIAL', 'sm'
										, JURI::root() . 'media/com_komento/images/integrations/easysocial.png', 64); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_points', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_POINTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_badges', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_BADGES'); ?>
				<?php echo $this->fd->html('settings.toggle', 'easysocial_profile_popbox', 'COM_KOMENTO_LAYOUT_AVATAR_USE_EASYSOCIAL_PROFILE_POPBOX'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_stream_comment', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_stream_like', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_STREAM_LIKE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_sync_comment', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_easysocial_sync_like', 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_EASYSOCIAL_SYNC_LIKE'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_EASYSOCIAL_NOTIFICATIONS'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_es_enable', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_es_event_new_comment', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_es_event_new_reply', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_REPLY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_es_event_new_like', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_EVENT_NEW_LIKE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_es_to_author', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_AUTHOR'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_es_to_participant', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_PARTICIPANT'); ?>

				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_es_to_usergroup_comment', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_COMMENT'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_es_to_usergroup_reply', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_REPLY'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_es_to_usergroup_like', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EASYSOCIAL_TO_USERGROUP_LIKE'); ?>
			</div>
		</div>
	</div>
</div>
