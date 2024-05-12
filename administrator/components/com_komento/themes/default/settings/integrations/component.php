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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_CATEGORIES'); ?>

			<div class="panel-body">
				<p><?php echo JText::_('COM_KOMENTO_SETTINGS_CATEGORIES_INFO'); ?></p>

				<?php echo $this->fd->html('settings.dropdown', 'mode_categories_' . $tab->id, 'COM_KOMENTO_SETTINGS_CATEGORIES_ASSIGNMENT', [
					'0' => 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES',
					'1' => 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_SELECTED_CATEGORIES',
					'2' => 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES_EXCEPT_SELECTED',
					'3' => 'COM_KOMENTO_SETTINGS_CATEGORIES_NO_CATEGORIES'
				]); ?>

				<?php echo $this->fd->html('settings.multilist', 'allowed_categories_' . $tab->id, 'COM_KOMENTO_SETTINGS_ENABLE_ON_CATEGORIES', $tab->categories); ?>
			</div>
		</div>
	</div>
	
	<?php if ($tab->componentSettings) { ?>
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_COMPONENT'); ?>

			<div class="panel-body">
				<?php foreach ($tab->componentSettings as $setting) { ?>
					<?php echo $this->fd->html($setting->type, $setting->name, 'COM_KOMENTO_SETTINGS_COMPONENT_' . strtoupper($setting->name), $setting->values); ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>