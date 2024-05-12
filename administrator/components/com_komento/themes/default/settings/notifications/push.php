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
			<?php echo $this->fd->html('overlay.form', KT::isFreeVersion(), '', KT_PRODUCT_PAGE); ?>
			
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_PUSH_ONESIGNAL', '', '/administrators/how-tos/onesignal-push-notification-configuration'); ?>
	
			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_KOMENTO_SETTINGS_PUSH_ONESIGNAL_ABOUT'); ?>

				<?php echo $this->fd->html('settings.toggle', 'onesignal_enabled', 'COM_KOMENTO_SETTINGS_ONESIGNAL_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'onesignal_show_welcome', 'COM_KT_SETTINGS_ONESIGNAL_DISPLAY_WELCOME_MESSAGE'); ?>
				<?php echo $this->fd->html('settings.text', 'onesignal_app_id', 'COM_KOMENTO_SETTINGS_ONESIGNAL_APP_ID'); ?>
				<?php echo $this->fd->html('settings.text', 'onesignal_api_key', 'COM_KOMENTO_SETTINGS_ONESIGNAL_REST_API_KEY'); ?>
				<?php echo $this->fd->html('settings.text', 'onesignal_subdomain', 'COM_KOMENTO_SETTINGS_ONESIGNAL_SUBDOMAIN', '', [], JText::_('COM_KOMENTO_SETTINGS_ONESIGNAL_SUBDOMAIN_NOTE')); ?>
				<?php echo $this->fd->html('settings.text', 'onesignal_safari_id', 'COM_KOMENTO_SETTINGS_ONESIGNAL_SAFARI_WEB_ID'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
	</div>
</div>