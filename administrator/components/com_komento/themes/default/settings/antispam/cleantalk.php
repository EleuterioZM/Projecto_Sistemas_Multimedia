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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_CLEANTALK_GENERAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('panel.info', 'COM_KOMENTO_SETTINGS_CLEANTALK_ABOUT', 'https://cleantalk.org/?pid=141225', 'Learn More', 'sm'
										, JURI::root() . 'media/com_komento/images/integrations/cleantalk.png', 150); ?>


				<?php echo $this->fd->html('settings.toggle', 'cleantalk_enabled', 'COM_KOMENTO_SETTINGS_CLEANTALK_ENABLE'); ?>
				<?php echo $this->fd->html('settings.text', 'cleantalk_key', 'COM_KOMENTO_SETTINGS_CLEANTALK_KEY'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
	</div>
</div>