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
<dialog>
	<width>500</width>
	<height>260</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{url}": "[data-kt-bbcode-video]",
		"{submit}": "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{url} keypress": function(input, event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				this.submit().click();
			}
		},
		
		"{submit} click": function() {
			var url = this.url().val();

			if (url == '') {
				this.url().parents('.o-form-group').addClass('has-error');
				return;
			}

			// Find the textarea to insert the item now
			var tag = '[video]' + url + '[/video]';
			
			var formController = $('[data-kt-form]').controller();
			formController.insertText(tag, <?php echo $position;?>);

			// Hide the window
			this.closeButton().click();
		},

		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_INSERT_VIDEO'); ?></title>
	<content>
		<div class="o-form-horizontal mb-lg">
			<div class="o-form-group">
				<label for="video-url" class="o-control-label">
					<?php echo JText::_('COM_KOMENTO_INSERT_VIDEO_URL');?>
				</label>
				<div class="o-control-input">
					<?php echo $this->fd->html('form.text', 'video-url', '', 'video-url', [
						'attributes' => 'data-kt-bbcode-video',
						'placeholder' => JText::_('COM_KOMENTO_INSERT_VIDEO_URL_HERE')
					]); ?>
				</div>
			</div>
		</div>

		<?php echo $this->fd->html('html.well', '<div class="font-bold">' . JText::_('COM_KT_SUPPORTED_VIDEO_PROVIDERS') . '</div><div class="mt-xs">' . JText::_('COM_KT_VIDEO_PROVIDERS') . '</div>'); ?>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_INSERT_VIDEO', 'primary'); ?>
	</buttons>
</dialog>