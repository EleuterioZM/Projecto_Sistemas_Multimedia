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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_NOTIFICATION_GENERAL'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_enable', 'COM_KOMENTO_SETTINGS_NOTIFICATION_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_sendmailonpageload', 'COM_KOMENTO_SETTINGS_SEND_MAIL_ON_PAGE_LOAD'); ?>
				<?php echo $this->fd->html('settings.text', 'notification_total_email', 'COM_KOMENTO_SETTINGS_TOTAL_EMAIL_TO_SEND', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_EMAILS'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_event_new_comment', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_event_new_reply', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_REPLY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_event_new_pending', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_NEW_PENDING'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_event_new_pending_author', 'COM_KT_NOTIFY_AUTHOR_FOR_PENDING_COMMENTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_event_reported_comment', 'COM_KOMENTO_SETTINGS_NOTIFICATION_EVENT_REPORTED_COMMENT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'custom_email_logo', 'COM_KOMENTO_SETTINGS_NOTIFICATIONS_CUSTOM_EMAIL_LOGO', '', 'data-custom-email-logo'); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $this->config->get('custom_email_logo') ? '' : 't-hidden' ?>" data-email-logo-wrapper data-fd-form-group>
					<?php echo $this->fd->html('form.label', 'COM_KOMENTO_SETTINGS_NOTIFICATIONS_CUSTOM_EMAILS_LOGO', 'logo'); ?>

					<div class="flex-grow" data-email-logo data-id data-default-email-logo="<?php echo KT::notification()->getLogo(true); ?>">
						<div class="flex flex-col flex-grow space-y-xs" data-fd-file data-preview="1" data-remove="0">
							<div class="flex w-full rounded-md overflow-hidden">
								<input type="file" name="email_logo" class="hidden sr-only" accept="image/*" />
								
								<div class="flex w-[180px] rounded-md overflow-hidden">
									<div class="o-aspect-ratio o-aspect-ratio--contain" data-fd-file-preview>
										<img src="<?php echo KT::notification()->getLogo(); ?>" data-email-logo-image />
									</div>
								</div>
							</div>

							<div class="flex space-x-xs">
								<?php echo $this->fd->html('button.standard', JText::_('Browse'), 'default', 'sm', [
									'attributes' => 'data-fd-file-browse'
								]); ?>

								<?php echo $this->fd->html('button.standard', JText::_('COM_KOMENTO_DELETE'), 'danger', 'sm', [
									'attributes' => 'data-restore-logo',
									'outline' => true,
									'class' => !KT::notification()->hasOverrideLogo() ? 't-hidden' : ''
								]); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_SETTINGS_SUBSCRIPTIONS'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'subscription_auto', 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_AUTO'); ?>
				<?php echo $this->fd->html('settings.toggle', 'subscription_confirmation', 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_CONFIRMATION'); ?>
				<?php echo $this->fd->html('settings.toggle', 'email_digest_enabled', 'COM_KT_SETTINGS_SUBSCRIPTIONS_ENABLE_EMAIL_DIGEST'); ?>
				<?php echo $this->fd->html('settings.dropdown', 'email_digest_interval', 'COM_KT_SETTINGS_SUBSCRIPTIONS_EMAIL_DIGEST_INTERVAL', [
					'instant' => 'COM_KT_SETTINGS_SUBSCRIPTIONS_INTERVAL_INSTANT',
					'daily' => 'COM_KT_SETTINGS_SUBSCRIPTIONS_INTERVAL_DAILY',
					'weekly' => 'COM_KT_SETTINGS_SUBSCRIPTIONS_INTERVAL_WEEKLY',
					'monthly' => 'COM_KT_SETTINGS_SUBSCRIPTIONS_INTERVAL_MONTHLY'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_NOTIFICATION_RECIPIENTS'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'notification_to_author', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_AUTHOR'); ?>
				<?php echo $this->fd->html('settings.toggle', 'notification_to_subscribers', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_SUBSCRIBERS'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_to_usergroup_comment', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_COMMENT'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_to_usergroup_reply', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_REPLY'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_to_usergroup_pending', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_PENDING'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'notification_to_usergroup_reported', 'COM_KOMENTO_SETTINGS_NOTIFICATION_TO_USERGROUP_REPORTED'); ?>
			</div>
		</div>
	</div>
</div>