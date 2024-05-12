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
<div class="o-snackbar <?php echo $wrapperClass;?>">
	<div class="flex md:items-center flex-col md:flex-row space-y-md md:space-y-no">
		<div class="flex-grow <?php echo $textClass;?>"><?php echo $text;?></div>

		<?php if ($actions) { ?>
		<div class="flex items-center space-x-xs flex-shrink-0">
			<?php foreach ($actions as $action) { ?>
				<?php echo $action; ?>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>