<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<span class="o-label bg-<?php echo $type; ?>-100 text-<?php echo $type; ?>-500 <?php echo $class; ?>">
	<?php if ($icon) { ?>
		<i class="fdi fa fa-home"></i>

		&nbsp;
	<?php } ?>

	<?php echo $text; ?>

	<?php if ($showRemove) { ?>
		<a class="ml-xs text-<?php echo $type; ?>-500 leading-reset" href="javascript:void(0);" data-fd-label-remove>
			<i class="fdi fa fa-times"></i>
		</a>
	<?php } ?>
</span>