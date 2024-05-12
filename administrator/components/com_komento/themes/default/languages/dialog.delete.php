<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>400</width>
	<height>100</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]",
		"{form}": "[data-delete-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},
		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_UNINSTALL_LANGUAGE_TITLE');?></title>
	<content>
		<form name="deleteLanguage" method="post" action="<?php echo JRoute::_('index.php');?>" data-delete-form>
			<p>
				<?php echo JText::_('COM_KOMENTO_UNINSTALL_LANGUAGE_CONTENT');?>
			</p>

			<?php if ($ids) { ?>
				<?php foreach ($ids as $id) { ?>
				<input type="hidden" name="cid[]" value="<?php echo (int) $id;?>" />
				<?php } ?>
			<?php } ?>

			<?php echo $this->fd->html('form.action', 'uninstall', 'languages'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.cancelButton'); ?>

		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_UNINSTALL_BUTTON', 'danger'); ?>
	</buttons>
</dialog>
