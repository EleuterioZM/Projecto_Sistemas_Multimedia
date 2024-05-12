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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_WORKFLOW_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_komento', 'COM_KOMENTO_SETTINGS_ENABLE_SYSTEM'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_reply', 'COM_KOMENTO_SETTINGS_REPLY_ENABLE'); ?>
				<?php echo $this->fd->html('settings.text', 'max_threaded_level', 'COM_KOMENTO_SETTINGS_COMMENTS_MAX_THREADED_LEVEL', '', [
					'size' => 6,
					'postfix' => 'Levels'
				]);?>
				<?php echo $this->fd->html('settings.toggle', 'enable_report', 'COM_KOMENTO_SETTINGS_REPORT_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_likes', 'COM_KOMENTO_SETTINGS_LIKES_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_mention', 'COM_KOMENTO_SETTINGS_MENTION_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_ratings', 'COM_KOMENTO_SETTINGS_RATINGS_ENABLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_minimize', 'COM_KT_ENABLE_MINIMIZE_COMMENTS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_rss', 'COM_KOMENTO_SETTINGS_RSS_ENABLE'); ?>
				<?php echo $this->fd->html('settings.text', 'rss_max_items', 'COM_KOMENTO_SETTINGS_RSS_MAX_ITEMS', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_COMMENTS'
				]);?>
				
				<?php echo $this->fd->html('settings.user', 'orphanitem_ownership', 'COM_KOMENTO_SETTINGS_ORPHANITEM_OWNERSHIP', '', ['columns' => 12]);?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('overlay.form', KT::isFreeVersion(), '', KT_PRODUCT_PAGE); ?>

			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LIVE_NOTIFICATIONS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_live_notification', 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_LIVE_NOTIFICATION'); ?>
				<?php echo $this->fd->html('settings.text', 'live_notification_interval', 'COM_KOMENTO_SETTINGS_ADVANCE_LIVE_NOTIFICATION_INTERVAL', '', [
					'postfix' => 'COM_KOMENTO_SECONDS',
					'size' => 6
				]);?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_USER_DOWNLOAD'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_gdpr_download', 'COM_KT_USER_ALLOW_DOWNLOAD'); ?>
				<?php echo $this->fd->html('settings.text', 'userdownload_expiry', 'COM_KT_USER_DOWNLOAD_EXPIRY', '', [
					'size' => 4,
					'class' => 'text-center'
				]);?>
				
			</div>
		</div>
	</div>
</div>