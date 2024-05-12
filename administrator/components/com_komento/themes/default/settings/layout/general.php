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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LAYOUT_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'layout_appearance', 'COM_KT_APPEARANCE', [
					'light' => 'Light (Default)',
					'dark' => 'Dark'
				], '', '', '', ['overlay' => KT::isFreeVersion(), 'upgradeUrl' => KT_PRODUCT_PAGE]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_accent', 'COM_KT_ACCENT_COLOR', [
					'foundry' => 'Standard (Default)',
					'blue-violet' => 'Violet',
					'dodger-blue' => 'Dodger Blue',
					'teal' => 'Teal',
					'tomato' => 'Tomato'
				], '', '', '', ['overlay' => KT::isFreeVersion(), 'upgradeUrl' => KT_PRODUCT_PAGE]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_comment_placement', 'COM_KT_COMMENT_PLACEMENT', [
					'inline' => 'COM_KT_STANDARD_DEFAULT',
					'left' => 'COM_KOMENTO_ALIGNMENT_LEFT',
					'right' => 'COM_KOMENTO_ALIGNMENT_RIGHT'
				], '', '', '', ['overlay' => KT::isFreeVersion(), 'upgradeUrl' => KT_PRODUCT_PAGE]); ?>

				<?php echo $this->fd->html('settings.toggle', 'load_initial_comment', 'COM_KT_COMMENT_LOAD_INITIAL'); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_sort_buttons', 'COM_KOMENTO_SETTINGS_SHOW_SORT_BUTTONS'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'default_sort', 'COM_KOMENTO_SETTINGS_DEFAULT_SORT', [
					'oldest' => 'COM_KOMENTO_SETTINGS_SORT_OLDEST',
					'latest' => 'COM_KOMENTO_SETTINGS_SORT_LATEST',
					'popular' => 'COM_KT_SORT_MOST_LIKES'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_threaded', 'COM_KOMENTO_SETTINGS_THREADED_VIEW_ENABLE', '', '', '', '', [
					'dependency' => '[data-comment-indent-level]'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'thread_indentation', 'COM_KOMENTO_SETTINGS_THREAD_INDENTATION', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_PIXELS', 
					'visible' => $this->config->get('enable_threaded'), 
					'wrapperAttributes' => 'data-comment-indent-level'
				]); ?>

				<?php echo $this->fd->html('settings.text', 'max_comments_per_page', 'COM_KOMENTO_SETTINGS_COMMENTS_MAX_PER_PAGE', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_COMMENTS'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'reply_autohide', 'COM_KOMENTO_SETTINGS_REPLY_AUTOHIDE', '', '', '', '', [
					'dependency' => '[data-comment-autohide]'
				]); ?>
				<?php echo $this->fd->html('settings.text', 'reply_autohide_threshold', 'COM_KOMENTO_SETTINGS_REPLY_AUTOHIDE_THRESHOLD', '', [
					'size' => 6,
					'postfix' => 'Replies',
					'visible' => $this->config->get('reply_autohide'), 
					'wrapperAttributes' => 'data-comment-autohide'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_conversation_bar', 'COM_KOMENTO_SETTINGS_CONVERSATION_BAR_ENABLE'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_COMMENT_APPEARENCE'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_info', 'COM_KOMENTO_SETTINGS_DISPLAY_LAST_EDITED_INFO'); ?>
				<?php echo $this->fd->html('settings.toggle', 'auto_hyperlink', 'COM_KOMENTO_SETTINGS_LAYOUT_AUTO_HYPERLINK'); ?>
				<?php echo $this->fd->html('settings.toggle', 'links_nofollow', 'COM_KOMENTO_SETTINGS_LAYOUT_LINKS_NOFOLLOW'); ?>
				<?php echo $this->fd->html('settings.toggle', 'comment_enable_truncation', 'COM_KOMENTO_SETTINGS_COMMENTS_ENABLE_AUTOMATIC_TRUNCATE'); ?>
				<?php echo $this->fd->html('settings.text', 'comment_truncation_length', 'COM_KOMENTO_SETTINGS_COMMENTS_ENABLE_AUTOMATIC_TRUNCATE_LENGTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_CHARACTERS'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_lapsed_time', 'COM_KOMENTO_SETTINGS_COMMENTS_USE_LAPSED_TIME', '', '', '', '', [
					'dependency' => '[data-comment-date-format]',
					'dependencyValue' => 0
				]); ?>
				<?php echo $this->fd->html('settings.text', 'date_format', 'COM_KOMENTO_SETTINGS_LAYOUT_DATE_FORMAT', '', [
					'visible' => !$this->config->get('enable_lapsed_time'), 
					'wrapperAttributes' => 'data-comment-date-format',
					'help' => 'http://php.net/manual/en/function.date.php'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'enable_media_max_width', 'COM_KT_SETTINGS_LAYOUT_MEDIA_MAXWIDTH', '', '', '', '', [
					'dependency' => '[data-comment-media-size]',
					'dependencyValue' => 0
				]); ?>
				
				<?php echo $this->fd->html('settings.text', 'max_image_width', 'COM_KOMENTO_SETTINGS_LAYOUT_IMAGE_WIDTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_PIXELS', 
					'wrapperAttributes' => 'data-comment-media-size', 
					'visible' => !$this->config->get('enable_media_max_width')
				]); ?>
				
				<?php echo $this->fd->html('settings.text', 'max_image_height', 'COM_KOMENTO_SETTINGS_LAYOUT_IMAGE_HEIGHT', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_PIXELS', 
					'wrapperAttributes' => 'data-comment-media-size', 
					'visible' => !$this->config->get('enable_media_max_width')
				]); ?>

				<?php echo $this->fd->html('settings.text', 'bbcode_video_width', 'COM_KOMENTO_SETTINGS_LAYOUT_VIDEO_WIDTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_PIXELS', 
					'wrapperAttributes' => 'data-comment-media-size', 
					'visible' => !$this->config->get('enable_media_max_width')
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'defer_attachments', 'COM_KT_SETTINGS_DEFER_ATTACHMENTS'); ?>
			</div>
		</div>
	</div>
</div>

