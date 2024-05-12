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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_FORM'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_login_form', 'COM_KOMENTO_SETTINGS_ENABLE_LOGIN_FORM'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'login_provider', 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER', [
					'joomla' => 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_JOOMLA',
					'easysocial' => 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_EASYSOCIAL',
					'cb' => 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_COMMUNITYBUILDER',
					'jomsocial' => 'COM_KOMENTO_SETTINGS_LOGIN_PROVIDER_JOMSOCIAL'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_subscription', 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_ENABLE'); ?>
				<?php echo $this->fd->html('settings.userGroupsTree', 'show_tnc', 'COM_KOMENTO_SETTINGS_TNC_ENABLE'); ?>
				<?php echo $this->fd->html('settings.textarea', 'tnc_text', 'COM_KOMENTO_SETTINGS_TNC_TEXT', '', '', ['rows' => 15]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">		
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_FORM_FIELDS'); ?>
			<div class="panel-body">

				<?php echo $this->fd->html('settings.dropdown', 'show_name', 'COM_KOMENTO_SETTINGS_SHOW_NAME', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'require_name', 'COM_KOMENTO_SETTINGS_REQUIRE_NAME', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'show_email', 'COM_KOMENTO_SETTINGS_SHOW_EMAIL', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'require_email', 'COM_KOMENTO_SETTINGS_REQUIRE_EMAIL', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'show_website', 'COM_KOMENTO_SETTINGS_SHOW_WEBSITE', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'require_website', 'COM_KOMENTO_SETTINGS_REQUIRE_WEBSITE', [
					'0' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF',
					'1' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST',
					'2' => 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL'
				]); ?>
			</div>
		</div>

	</div>
</div>
