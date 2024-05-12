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
<div class="inline-flex align-middle md:mb-0 md:pr-md md:w-<?php echo $columns;?>/12 w-full flex-shrink-0 <?php echo $classes; ?>">
	<a id="<?php echo $uniqueId;?>"></a>
	<label for="<?php echo $id;?>" class="m-0 pr-xs leading-sm text-sm flex-grow text-gray-800" data-uid="<?php echo $uniqueId;?>">
		<?php echo $text;?>
	</label>

	<?php if ($tooltip) { ?>
	<i data-fd-popover data-fd-popover-trigger="hover" data-fd-popover-placement="top" data-fd-popover-title="<?php echo $helpTitle; ?>" data-fd-popover-content="<?php echo $helpContent;?>" class="fdi fa fa-question-circle fa-fw text-gray-500"></i>
	<?php } ?>
</div>
