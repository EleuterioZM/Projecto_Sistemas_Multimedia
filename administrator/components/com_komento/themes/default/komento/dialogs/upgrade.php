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
	<width>350</width>
	<height>50</height>
	<selectors type="json">
	{
		"{cancelButton}": "[data-close-button]",
		"{submitButton}": "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KT_UPGRADE_TO_PRO');?></title>
	<content type="text"><?php echo JText::_('COM_KT_UPGRADE_TO_PRO_DESC');?></content>
	<buttons>
		<?php echo $this->html('dialog.submitButton', 'COM_KT_UPGRADE_TO_PRO', 'primary'); ?>
	</buttons>
</dialog>