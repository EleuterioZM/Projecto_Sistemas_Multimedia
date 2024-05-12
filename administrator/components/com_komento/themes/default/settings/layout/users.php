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
		<div class="panel form-horizontal">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_LAYOUT_USER_APPEARENCE'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'name_type', 'COM_KOMENTO_SETTINGS_COMMENTS_NAME_TYPE', [
					'default' => 'COM_KOMENTO_SETTINGS_NAME_TYPE_DEFAULT',
					'username' => 'COM_KOMENTO_SETTINGS_NAME_TYPE_USERNAME',
					'name' => 'COM_KOMENTO_SETTINGS_NAME_TYPE_NAME',
					'easysocial' => 'EasySocial',
					'easyblog' => 'EasyBlog',
					'easydiscuss' => 'EasyDiscuss'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_avatar_enable', 'COM_KOMENTO_LAYOUT_AVATAR_ENABLE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_avatar_character', 'COM_KOMENTO_LAYOUT_AVATAR_CHARACTER_BASED', '', 'data-avatar-character-based'); ?>

				<?php echo $this->fd->html('settings.text', 'layout_avatar_character_background_color', 'COM_KT_LAYOUT_AVATAR_CHARACTER_BASED_BACKGROUND_COLORS', '', [
					'wrapperAttributes' => 'data-avatar-character-based-background-color',
					'visible' => $this->config->get('layout_avatar_character')
				]); ?>

				<?php echo $this->fd->html('settings.text', 'layout_avatar_character_font_color', 'COM_KT_LAYOUT_AVATAR_CHARACTER_BASED_FONT_COLORS', '', [
					'wrapperAttributes' => 'data-avatar-character-based-font-color',
					'visible' => $this->config->get('layout_avatar_character')
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_avatar_integration', 'COM_KOMENTO_LAYOUT_AVATAR_INTEGRATION', [
					'default' => 'Default',
					'easysocial' => 'EasySocial',
					'easyblog' => 'EasyBlog',
					'easydiscuss' => 'EasyDiscuss',
					'communitybuilder' => 'Community Builder',
					'k2' => 'K2',
					'gravatar' => 'Gravatar',
					'jomsocial' => 'Jomsocial',
					'kunena' => 'Kunena',
					'kunena3' => 'Kunena 3',
					'phpbb' => 'phpBB',
					'hwdmediashare' => 'HWDMediaShare',
					'joomprofile' => 'JoomProfile',
					'cmavatar' => 'CM Avatar',
					'jsn' => 'Profile Pro (com_jsn)'
				], '', 'data-avatar-integration', '', [
					'wrapperClass' => $this->config->get('layout_avatar_character') ? 't-hidden' : '',
					'wrapperAttributes' => 'data-avatar-integration-option'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'gravatar_default_avatar', 'COM_KOMENTO_LAYOUT_AVATAR_GRAVATAR_DEFAULT_PICTURE', [
					'mm' => 'Mystery Man',
					'identicon' => 'Identicon',
					'monsterid' => 'Monsterid',
					'wavatar' => 'Wavatar',
					'retro' => 'Retro'
				], '', '', '', [
					'wrapperClass' => $this->config->get('layout_avatar_integration') === 'gravatar' && !$this->config->get('layout_avatar_character') ? '' : 't-hidden',
					'wrapperAttributes' => 'data-avatar-option data-avatar-gravatar'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_avatar_style', 'COM_KT_LAYOUT_AVATAR_STYLE', [
						'rounded' => 'COM_KT_LAYOUT_STYLE_AVATAR_OPTION_ROUNDED',
						'square' => 'COM_KT_LAYOUT_STYLE_AVATAR_OPTION_SQUARE'
					]
				); ?>

				<?php echo $this->fd->html('settings.text', 'layout_phpbb_path', 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_PATH', '', [
					'wrapperAttributes' => 'data-avatar-option data-avatar-phpbb',
					'visible' => $this->config->get('layout_avatar_integration') == 'phpbb' && !$this->config->get('layout_avatar_character')
				]); ?>

				<?php echo $this->fd->html('settings.text', 'layout_phpbb_url', 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_URL', '', [
					'wrapperAttributes' => 'data-avatar-option data-avatar-phpbb',
					'visible' => $this->config->get('layout_avatar_integration') == 'phpbb' && !$this->config->get('layout_avatar_character')
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_rank_bar', 'COM_KOMENTO_SETTINGS_RANK_BAR_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'admin_label', 'COM_KT_SETTINGS_DISPLAY_ADMIN_LABEL'); ?>
				<?php echo $this->fd->html('settings.toggle', 'author_label', 'COM_KT_SETTINGS_DISPLAY_AUTHOR_LABEL'); ?>
				<?php echo $this->fd->html('settings.toggle', 'guest_label', 'COM_KOMENTO_SETTINGS_LAYOUT_GUEST_LABEL'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
	</div>
</div>


