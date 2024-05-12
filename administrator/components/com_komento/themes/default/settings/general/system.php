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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_ADVANCED'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'komento_jquery', 'COM_KOMENTO_SETTINGS_LOAD_JQUERY'); ?>
				<?php echo $this->fd->html('settings.toggle', 'komento_ajax_index', 'COM_KOMENTO_SETTINGS_USE_INDEX'); ?>
				<?php echo $this->fd->html('settings.text', 'komento_cdn_url', 'COM_KOMENTO_SETTINGS_CDN_URL'); ?>
				<?php echo $this->fd->html('settings.toggle', 'secure_cron', 'COM_KT_SETTINGS_SECURE_CRON', '', 'data-secure-cron'); ?>

				<?php echo $this->fd->html('settings.text', 'secure_cron_key', 'COM_KT_SETTINGS_SECURE_CRON_KEY','','',JText::_('COM_KT_SETTINGS_SECURE_CRON_KEY_INFO'));?>

				<?php if ($this->config->get('secure_cron_key')) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $this->config->get('secure_cron') ? '' : 't-hidden';?>" data-secure-cron-settings>
					<div class="inline-flex align-middle md:mb-0 md:pr-md md:w-5/12 w-full flex-shrink-0">
						<label class="m-0 pr-xs leading-sm text-sm flex-grow text-gray-800">&nbsp;</label>
					</div>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.textCopy', 'cron_secure_url', JURI::root() . 'index.php?option=com_komento&cron=true&phrase=' . $this->config->get('secure_cron_key')); ?>
					</div>
				</div>
				<?php } ?>

				<?php echo $this->fd->html('settings.dropdown', 'trigger_method', 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD', [
					'none' => 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_NONE',
					'component' => 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_COMPONENT_PLUGIN',
					'joomla' => 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_JOOMLA_PLUGIN'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KT_SETTINGS_FONTAWESOME'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_fontawesome', 'COM_KT_SETTINGS_ENABLE_FONTAWESOME'); ?>
			</div>
		</div>
	</div>
</div>