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
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-cancel-button]",
		"{submit}": "[data-submit-button]",
		"{form}": "[data-spam-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{submit} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_SPAM_CONFIRMATION_DIALOG_TITLE_' . strtoupper($action)); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-spam-form>
			<p class="mt-md"><?php echo JText::_('COM_KOMENTO_SPAM_CONFIRMATION_DIALOG_CONTENT_' . strtoupper($action)); ?></p>

			<?php foreach ($items as $id) { ?>
			<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
			<?php } ?>

			<?php echo $this->fd->html('form.returnUrl', '', $return); ?>
			<?php echo $this->fd->html('form.action', 'spam', 'comments'); ?>
			<?php echo $this->fd->html('form.hidden', 'action', $action); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.cancelButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_SPAM_BUTTON_' . strtoupper($action), 'danger'); ?>
	</buttons>
</dialog>