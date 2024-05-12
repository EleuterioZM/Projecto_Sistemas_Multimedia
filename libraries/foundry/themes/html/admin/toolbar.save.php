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
<div class="t-hidden" data-fd-toolbar-save-group>
	<div id="toolbar-dropdown-save-group" class="btn-group dropdown-save-group" role="group">
		<button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split" 
			data-toggle="dropdown" 
			data-bs-toggle="dropdown"
			data-target="#toolbar-dropdown-save-group" 
			data-display="static" 
			aria-haspopup="true" 
			aria-expanded="false" 
			data-bs-reference="parent"
		>
			<span class="sr-only"><?php echo JText::_('Toggle Dropdown');?></span>
		</button>

		<div class="dropdown-menu" data-dropdown-items>
		</div>
	</div>
</div>
