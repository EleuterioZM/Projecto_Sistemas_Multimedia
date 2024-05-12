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
	<width>860</width>
	<height>640</height>
	<selectors type="json">
	{
		"{close}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{close} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_($mailer->subject);?></title>
	<content type="text"><?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_komento&view=mailq&layout=preview&id=<?php echo $mailer->id;?>&tmpl=component</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>
