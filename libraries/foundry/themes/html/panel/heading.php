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
<div class="panel-head">
	<div class="flex">
		<div class="flex-grow">
			<b class="text-md font-bold leading-md text-gray-800 m-no mb-2xs"><?php echo JText::_($header);?></b>
			<div class="panel-info text-sm text-gray-500 leading-sm"><?php echo JText::_($desc);?></div>
		</div>

		<?php if ($helpLink) { ?>
		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.link', $helpLink, '<i class="fdi far fa-life-ring"></i>&nbsp; ' . JText::_('JHELP'), 'default', 'sm', ['attributes' => 'target="_blank"']); ?>
		</div>
		<?php } ?>
	</div>
</div>
