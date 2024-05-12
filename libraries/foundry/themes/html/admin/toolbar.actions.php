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
<div id="toolbar-actions" class="btn-wrapper t-hidden" data-fd-toolbar-actions="others">
	<div class="dropdown">
		<button type="button" class="btn btn-small dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
			<span class="icon-cog"></span> <?php echo JText::_($title);?> &nbsp;<span class="caret"></span>
		</button>

		<ul class="dropdown-menu">
			<?php foreach ($actions as $action) { ?>
			<li>
				<a href="javascript:void(0);" data-action="<?php echo $action->cmd;?>" <?php echo isset($action->custom) && $action->custom ? 'data-custom="1"' : '';?>>
					<?php echo JText::_($action->title); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
