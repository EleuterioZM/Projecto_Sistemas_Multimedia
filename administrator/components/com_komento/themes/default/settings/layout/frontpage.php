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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'layout_frontpage_comment', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_SHOW_COMMENTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'layout_frontpage_hits', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_SHOW_HITS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'layout_frontpage_ratings', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_SHOW_RATINGS'); ?>
				<?php echo $this->fd->html('settings.dropdown', 'layout_frontpage_alignment', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_ALIGNMENT', [
					'left' => 'COM_KOMENTO_ALIGNMENT_LEFT',
					'right' => 'COM_KOMENTO_ALIGNMENT_RIGHT'
				]); ?>
			</div>
		</div>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_PREVIEW'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'layout_frontpage_preview', 'COM_KOMENTO_SETTINGS_LAYOUT_FRONTPAGE_SHOW_PREVIEW'); ?>
				<?php echo $this->fd->html('settings.text', 'preview_count', 'COM_KOMENTO_SETTINGS_PREVIEW_COUNT', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_COMMENTS'
				]); ?>
				<?php echo $this->fd->html('settings.dropdown', 'preview_sort', 'COM_KOMENTO_SETTINGS_PREVIEW_SORT', [
					'oldest' => 'COM_KOMENTO_SETTINGS_SORT_OLDEST',
					'latest' => 'COM_KOMENTO_SETTINGS_SORT_LATEST',
					'popular' => 'COM_KOMENTO_SETTINGS_SORT_POPULAR'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'preview_sticked_only', 'COM_KOMENTO_SETTINGS_PREVIEW_PINNED_ONLY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'preview_parent_only', 'COM_KOMENTO_SETTINGS_PREVIEW_PARENT_ONLY'); ?>
				<?php echo $this->fd->html('settings.text', 'preview_comment_length', 'COM_KOMENTO_SETTINGS_PREVIEW_COMMENT_LENGTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_CHARACTERS'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('overlay.form', KT::isFreeVersion(), '', KT_PRODUCT_PAGE); ?>

			<?php echo $this->fd->html('panel.heading', 'COM_KT_INSTANT_COMMENTS'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'layout_frontpage_instant_comment', 'COM_KT_SETTINGS_LAYOUT_FRONTPAGE_ENABLE_INSTANT_COMMENT', '', '', '', '', [
					'dependency' => '[data-instant-comment-placement]'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'layout_frontpage_instant_comment_placement', 'COM_KT_SETTINGS_LAYOUT_FRONTPAGE_INSTANT_COMMENT_PLACEMENT', [
					'left' => 'COM_KOMENTO_ALIGNMENT_LEFT',
					'right' => 'COM_KOMENTO_ALIGNMENT_RIGHT'
				], '', '', '', [ 
					'wrapperClass' => $this->config->get('layout_frontpage_instant_comment') ? '' : 't-hidden', 
					'wrapperAttributes' => 'data-instant-comment-placement'
				]); ?>
			</div>
		</div>
	</div>
</div>

