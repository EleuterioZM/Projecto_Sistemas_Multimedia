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
		"{submitButton}" : "[data-submit-button]",
		"{cancelButton}" : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}		
	}
	</bindings>
	<title><?php echo JText::_('COM_KT_GDPR_DOWNLOAD_USER_INFORMATION'); ?></title>
	<content>
		<div class="t-lg-mb--xl"><?php echo JText::_('COM_KT_GDPR_DOWNLOAD_USER_INFORMATION_DESC');?></div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.cancelButton'); ?>

		<?php echo $this->html('dialog.submitButton', 'COM_KT_SUBMIT_BUTTON', 'primary'); ?>
	</buttons>
</dialog>
