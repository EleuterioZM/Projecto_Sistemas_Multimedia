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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_CAPTCHA'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'antispam_captcha_enable', 'COM_KOMENTO_SETTINGS_CAPTCHA_ENABLE'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'antispam_captcha_type', 'COM_KOMENTO_SETTINGS_CAPTCHA_TYPE', [
					'0' => 'COM_KOMENTO_SETTINGS_CAPTCHA_BUILT_IN',
					'1' => 'COM_KOMENTO_SETTINGS_CAPTCHA_RECAPTCHA',
					'hcaptcha' => 'COM_KT_SETTINGS_CAPTCHA_HCAPTCHA'
				]); ?>

				<?php echo $this->fd->html('settings.userGroupsTree', 'show_captcha', 'COM_KOMENTO_SETTINGS_CAPTCHA_USERGROUPS'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel <?php echo $this->config->get('antispam_captcha_type') !== '1' ? 't-hidden' : '';?>" data-panel-captcha="1">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_RECAPTCHA', '', '/administrators/how-tos/how-to-setting-up-recaptcha'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'antispam_recaptcha_public_key', 'COM_KOMENTO_SETTINGS_RECAPTCHA_PUBLIC_KEY'); ?>
				<?php echo $this->fd->html('settings.text', 'antispam_recaptcha_private_key', 'COM_KOMENTO_SETTINGS_RECAPTCHA_PRIVATE_KEY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'antispam_recaptcha_invisible', 'COM_KT_RECAPTCHA_INVISIBLE_RECAPTCHA'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'antispam_recaptcha_theme', 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME', [
					'clean' => 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_CLEAN',
					'white' => 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_WHITE',
					'red' => 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_RED',
					'blackglass' => 'COM_KOMENTO_SETTINGS_RECAPTCHA_THEME_BLACKGLASS'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'antispam_recaptcha_lang', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE', '', [
					'size' => 3
				], '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">' . JText::_('COM_KOMENTO_VIEW_LANGUAGE_CODES') . '</a>'); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('antispam_captcha_type') !== 'hcaptcha' ? 't-hidden' : '';?>" data-panel-captcha="hcaptcha">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_SETTINGS_HCAPTCHA', '', '/administrators/antispam/hcaptcha'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_KT_HCAPTCHA_INFO', 'https://hCaptcha.com/?r=3edc32b5f23a', 'Get hCaptcha', 'sm'); ?>

				<?php echo $this->fd->html('settings.text', 'antispam_hcaptcha_site', 'COM_KT_HCAPTCHA_SITE_KEY'); ?>
				<?php echo $this->fd->html('settings.text', 'antispam_hcaptcha_secret', 'COM_KT_HCAPTCHA_SECRET_KEY'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'antispam_hcaptcha_theme', 'COM_KT_HCAPTCHA_THEME', [
					'light' => 'Light (Default)',
					'dark' => 'Dark'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'antispam_hcaptcha_size', 'COM_KT_HCAPTCHA_SIZE', [
					'compact' => 'Compact',
					'normal' => 'Normal (Default)'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'antispam_hcaptcha_lang', 'COM_KOMENTO_SETTINGS_RECAPTCHA_LANGUAGE', '', [
					'size' => 3
				], '<a href="https://docs.hcaptcha.com/languages?r=3edc32b5f23a" target="_blank">' . JText::_('COM_KOMENTO_VIEW_LANGUAGE_CODES') . '</a>'); ?>
			</div>
		</div>
	</div>
</div>