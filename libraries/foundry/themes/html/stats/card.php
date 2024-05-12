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
<div>
	<a href="<?php echo $url ? $url : 'javascript:void(0);';?>" class="db-post-item">
		<div class="flex-grow min-w-0 pr-md">
			<div class="flex w-full items-start">
				<div class="mr-sm">
					<i class="<?php echo $icon;?> text-gray-500"></i>
				</div>
				<div class="min-w-0 flex-1">
					<div class="overflow-hidden truncate whitespace-nowrap"><?php echo JText::_($label);?></div>
				</div>
			</div>
		</div>
		<div class=" ml-auto t-hidden md:t-block">
			<div>
				<b><?php echo $count;?></b>
			</div>
		</div>
	</a>
</div>