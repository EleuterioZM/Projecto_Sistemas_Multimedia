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
<a href="<?php echo $url; ?>" class="o-btn
	o-btn--<?php echo $type; ?>
	<?php echo $block ? 'flex w-full' : '';?>
	<?php echo $class;?>
	
	<?php if ($icon || !$iconOnly) { ?> 
		<?php echo $size === 'xs' ? 'text-xs leading-xs px-xs py-2xs' : '';?>
		<?php echo $size === 'sm' ? 'text-sm leading-xs px-xs py-xs' : '';?>
		<?php echo $size === 'md' ? 'text-sm leading-sm px-sm py-xs' : '';?>
		<?php echo $size === 'lg' ? 'text-sm leading-sm px-sm py-sm' : '';?>
		<?php echo $size === 'xl' ? 'text-md leading-md px-md py-sm' : '';?>
		<?php echo $size === '2xl' ? 'text-lg leading-md px-md py-md' : '';?>
	<?php } ?>

	<?php if ($icon && $iconOnly) { ?>
		<?php echo $size === 'xs' ? 'text-xs leading-xs px-2xs py-2xs' : '';?>
		<?php echo $size === 'sm' ? 'text-sm leading-xs px-xs py-xs' : '';?>
		<?php echo $size === 'md' ? 'text-sm leading-sm px-xs py-xs' : '';?>
		<?php echo $size === 'lg' ? 'text-sm leading-sm px-sm py-sm' : '';?>
		<?php echo $size === 'xl' ? 'text-md leading-md px-sm py-sm' : '';?>
		<?php echo $size === '2xl' ? 'text-lg leading-md px-md py-md' : '';?>
	<?php } ?>
" <?php echo $attributes;?>>

	<?php if ($icon) { ?>
	<i class="<?php echo $icon; ?>"></i>
	<?php } ?>

	<?php if (!$icon && $imageIcon) { ?>
		<img src="<?php echo $imageIcon; ?>" width="16" height="16" />
	<?php } ?>

	<?php if ($text) { ?>
	&nbsp; <?php echo $text; ?>
	<?php } ?>
</a>