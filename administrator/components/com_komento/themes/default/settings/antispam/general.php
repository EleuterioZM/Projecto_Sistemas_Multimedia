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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_ANTISPAM_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'antispam_min_length_enable', 'COM_KOMENTO_SETTINGS_COMMENT_MINIMUM_LENGTH_CHECK_ENABLE', '', '', '', '', [
					'dependency' => '[data-min-length]'
				]); ?>
				<?php echo $this->fd->html('settings.text', 'antispam_min_length', 'COM_KOMENTO_SETTINGS_MINIMUM_COMMENT_LENGTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_CHARACTERS',
					'wrapperAttributes' => 'data-min-length',
					'visible' => $this->config->get('antispam_min_length_enable')
				]);?>

				<?php echo $this->fd->html('settings.toggle', 'antispam_max_length_enable', 'COM_KOMENTO_SETTINGS_COMMENT_MAXIMUM_LENGTH_CHECK_ENABLE', '', '', '', '', [
					'dependency' => '[data-max-length]'
				]); ?>
				<?php echo $this->fd->html('settings.text', 'antispam_max_length', 'COM_KOMENTO_SETTINGS_MAXIMUM_COMMENT_LENGTH', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_CHARACTERS',
					'wrapperAttributes' => 'data-max-length',
					'visible' => $this->config->get('antispam_max_length_enable')
				]);?>

				<?php echo $this->fd->html('settings.toggle', 'antispam_flood_control', 'COM_KOMENTO_SETTINGS_FLOOD_CONTROL_ENABLE', '', '', '', '', [
					'dependency' => '[data-flood]'
				]); ?>
				<?php echo $this->fd->html('settings.text', 'antispam_flood_interval', 'COM_KOMENTO_SETTINGS_FLOOD_INTERVAL', '', [
					'size' => 6,
					'postfix' => 'COM_KOMENTO_SECONDS',
					'wrapperAttributes' => 'data-flood',
					'visible' => $this->config->get('antispam_flood_control')
				]);?>

				<?php echo $this->fd->html('settings.toggle', 'filter_word', 'COM_KOMENTO_SETTINGS_WORD_CENSORING_ENABLE', '', '', '', '', [
					'dependency' => '[data-censor]'
				]); ?>
				<?php echo $this->fd->html('settings.textarea', 'filter_word_text', 'COM_KOMENTO_SETTINGS_WORDS_TO_CENSOR', '', 'COM_KOMENTO_SETTINGS_WORDS_TO_CENSOR_ADVANCE', [
					'wrapperAttributes' => 'data-censor',
					'visible' => $this->config->get('filter_word')
				]); ?>
			</div>
		</div>

	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_IPADDRESS_BLACKLIST'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.textarea', 'blacklist_ip', 'COM_KOMENTO_SETTINGS_IP_ADDRESSES', '', '', 'COM_KOMENTO_SETTINGS_IP_ADDRESSES_INFO'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_SETTINGS_CONTENT_FILTERING'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.textarea', 'blocked_words', 'COM_KT_SETTINGS_CONTENT_FILTERING_BLOCKED_WORDS', '', 'COM_KT_SETTINGS_CONTENT_FILTERING_INFO'); ?>
			</div>
		</div>
	</div>
</div>