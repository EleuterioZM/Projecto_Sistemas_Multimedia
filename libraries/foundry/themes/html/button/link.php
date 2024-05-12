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
<a href="<?php echo $link;?>" class="
	o-btn 
	o-btn--<?php echo $class;?>
	<?php echo $block ? 'flex w-full' : '';?>
	<?php echo $extraClass;?>
	<?php echo $size === 'xs' ? 'text-xs leading-xs px-sm py-2xs' : '';?>
	<?php echo $size === 'sm' ? 'text-sm leading-xs px-sm py-xs' : '';?>
	<?php echo $size === 'md' ? 'text-sm leading-sm px-md py-xs' : '';?>
	<?php echo $size === 'lg' ? 'text-sm leading-sm px-md py-sm' : '';?>
	<?php echo $size === 'xl' ? 'text-md leading-md px-lg py-sm' : '';?>
	<?php echo $size === '2xl' ? 'text-lg leading-md px-xl py-md' : '';?>
" <?php echo $attributes;?>>
	<?php if ($icon) { ?>
		<i class="<?php echo $icon;?>"></i>&nbsp;
	<?php } ?>
	<?php echo $text;?>
</a>
