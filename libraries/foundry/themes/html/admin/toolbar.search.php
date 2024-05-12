<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-hidden btn-wrapper fd-settings-search" data-fd-toolbar-search="<?php echo $this->fd->getName();?>">
	<input type="text" class="fd-settings-search__input" data-fd-toolbar-search-input placeholder="<?php echo JText::_($placeholder);?>" />

	<div class="t-hidden fd-settings-search__result" data-fd-toolbar-search-result>
	</div>
</div>
