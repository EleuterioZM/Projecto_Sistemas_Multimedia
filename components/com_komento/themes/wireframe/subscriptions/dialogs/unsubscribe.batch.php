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
	<height>100</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{submit}": "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_UNSUBSCRIBE_DIALOG_TITLE'); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-unsubscribe-form>
			<p>
				<?php echo JText::_('COM_KT_UNSUBSCRIBE_BULK_CONFIRMATION'); ?>
			</p>
		</form>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
		<?php echo $this->html('dialog.submitButton', 'COM_KOMENTO_UNSUBSCRIBE_BUTTON', 'primary'); ?>
	</buttons>
</dialog>
