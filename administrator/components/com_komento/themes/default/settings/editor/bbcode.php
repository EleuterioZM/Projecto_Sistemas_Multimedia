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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_FORM_BBCODE'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_bbcode', 'COM_KOMENTO_SETTINGS_BBCODE_ENABLE', '', '', '', '', [
					'dependency' => '[data-kt-bbcode-enabled]', 
					'dependencyValue' => 1]); ?>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('enable_bbcode', 1) ? '' : 't-hidden'; ?>" data-kt-bbcode-enabled>
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_FORM_BBCODE_BUTTONS'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'bbcode_show_buttons', 'COM_KOMENTO_SETTINGS_BBCODE_SHOW_BUTTONS', '', '', '', 'data-kt-bbcode-enabled', [
					'dependency' => '[data-kt-show-buttons]', 
					'dependencyValue' => 1
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_bold', 'COM_KOMENTO_SETTINGS_BBCODE_BOLD', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) && $this->config->get('bbcode_show_buttons') ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_italic', 'COM_KOMENTO_SETTINGS_BBCODE_ITALIC', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_underline', 'COM_KOMENTO_SETTINGS_BBCODE_UNDERLINE', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_link', 'COM_KOMENTO_SETTINGS_BBCODE_LINK', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_picture', 'COM_KOMENTO_SETTINGS_BBCODE_PICTURE', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_video', 'COM_KOMENTO_SETTINGS_BBCODE_VIDEO', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_bulletlist', 'COM_KOMENTO_SETTINGS_BBCODE_BULLETLIST', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_numericlist', 'COM_KOMENTO_SETTINGS_BBCODE_NUMERICLIST', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_bullet', 'COM_KOMENTO_SETTINGS_BBCODE_BULLET', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_quote', 'COM_KOMENTO_SETTINGS_BBCODE_QUOTE', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_code', 'COM_KOMENTO_SETTINGS_BBCODE_CODE', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_spoiler', 'COM_KT_SETTINGS_BBCODE_SPOILER', '', '', '', 'data-kt-show-buttons', [
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_giphy', 'COM_KT_SETTINGS_BBCODE_GIPHY', '', '', '', 'data-kt-show-buttons', [
					'overlay' => KT::isFreeVersion(),
					'upgradeUrl' => KT_PRODUCT_PAGE,
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'bbcode_emoji', 'COM_KT_SETTINGS_BBCODE_EMOJI', '', '', '', 'data-kt-show-buttons', [
					'overlay' => KT::isFreeVersion(),
					'upgradeUrl' => KT_PRODUCT_PAGE,
					'wrapperClass' => $this->config->get('bbcode_show_buttons', 1) ? '' : 't-hidden'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('overlay.form', KT::isFreeVersion(), '', KT_PRODUCT_PAGE); ?>

			<?php echo $this->fd->html('panel.heading', 'COM_KT_GIPHY'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->fd->html('settings.toggle', 'giphy_enabled', 'COM_KT_ENABLE_GIPHY_SETTINGS'); ?>

					<?php echo $this->fd->html('settings.text', 'giphy_apikey', 'COM_KT_GIPHY_API_KEY_SETTINGS'); ?>

					<?php echo $this->fd->html('settings.text', 'giphy_limit', 'COM_KT_GIPHY_LIMIT_SETTINGS', '', [
						'size' => 6,
						'postfix' => 'Items'
					]); ?>
				</div>
			</div>
		</div>
		
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LOCATION_SHARING'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_location', 'COM_KOMENTO_SETTINGS_LOCATION_ENABLE'); ?>
				<?php echo $this->fd->html('settings.dropdown', 'location_service_provider', 'COM_KT_SETTINGS_LOCATIONS_SERVICE_PROVIDER', [
					'maps' => 'COM_KT_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEMAPS',
					'osm' => 'COM_KT_SETTINGS_LOCATIONS_SERVICE_PROVIDER_OPENSTREETMAP',
				], '', 'data-location-integration'); ?>
				<?php echo $this->fd->html('settings.text', 'location_key', 'COM_KOMENTO_SETTINGS_LOCATION_MAPS_API_KEY');?>
			</div>
		</div>
	</div>
</div>
