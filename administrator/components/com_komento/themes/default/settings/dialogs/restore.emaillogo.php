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
		"{restoreButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_RESTORE_EMAIL_LOGO_TITLE');?></title>
	<content>
		<p><?php echo JText::sprintf('COM_KOMENTO_RESTORE_EMAIL_LOGO_DESC'); ?></p>
	</content>
	<buttons>
		<?php echo $this->html('dialog.cancelButton'); ?>

		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_RESTORE_BUTTON', 'primary'); ?>
	</buttons>
</dialog>