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
			<?php echo $this->fd->html('panel.heading', 'COM_KOMENTO_SETTINGS_LAYOUT_ADVANCED'); ?>
			
			<div class="panel-body">
				<p><?php echo JText::_('COM_KOMENTO_SETTINGS_LAYOUT_CSS_CONTROL_DESC'); ?></p>

				<?php echo $this->fd->html('settings.text', 'layout_css_admin', 'COM_KOMENTO_SETTINGS_CSS_CLASS_ADMIN', '', array('attributes' => 'data-custom-css data-original="kmt-comment-item-admin"')); ?>
				<?php echo $this->fd->html('settings.text', 'layout_css_registered', 'COM_KOMENTO_SETTINGS_CSS_CLASS_REGISTERED', '', array('attributes' => 'data-custom-css data-original="kmt-comment-item-registered"')); ?>
				<?php echo $this->fd->html('settings.text', 'layout_css_author', 'COM_KOMENTO_SETTINGS_CSS_CLASS_CONTENT_AUTHOR', '', array('attributes' => 'data-custom-css data-original="kmt-comment-item-author"')); ?>
				<?php echo $this->fd->html('settings.text', 'layout_css_public', 'COM_KOMENTO_SETTINGS_CSS_CLASS_PUBLIC', '', array('attributes' => 'data-custom-css data-original="kmt-comment-item-public"')); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md ">
					<div class="inline-flex align-middle md:mb-0 md:pr-md md:w-5/12 w-full flex-shrink-0 "></div>
					<div class="flex-grow">
						<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-undo mr-xs') . JText::_('COM_KOMENTO_REVERT_ORIGINAL_CSS'), 'default', 'sm', [
							'attributes' => 'data-reset-css'
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
	</div>
</div>

