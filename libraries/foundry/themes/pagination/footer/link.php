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

$display = $item->text ? $item->text : '';

$href = 'javascript:void(0);';
if ($isLink) {
	$href = ($item->active || !$item->link) ? "javascript:void(0);" : $item->link;
}

$disabled = ($item->active || !$item->link) ? true : false;

// setup disabled class
$disabledClass = !$item->link ? ' text-gray-300 hover:text-gray-300' : '';
if ($item->active) {
	$disabledClass = !$item->link ? ' text-gray-500 hover:text-gray-500' : '';
}
if ($disabled) {
	$disabledClass .= ' cursor-not-allowed';
}

$activeClass = $item->active ? ' bg-gray-100' : '';
?>
<a
	class="o-pagination__btn <?php echo $class; ?><?php echo $disabledClass; ?><?php echo $activeClass; ?>"
	href="<?php echo $href; ?>"
	data-fd-pagination-link
	data-fd-pagination-link-limitstart="<?php echo $item->base ? $item->base : 0; ?>"
	<?php if ($disabled) { ?>
		data-fd-pagination-link-disabled

		<?php if (!$item->active) { ?>
			aria-disabled="true"
		<?php } ?>
	<?php } ?>

	<?php if ($item->active) { ?>
		aria-current="page"
	<?php } ?>

	<?php if ($display && !$icon) { ?>
		aria-label="<?php echo JText::sprintf('FD_PAGINATION_NUMBER', $display); ?>"
	<?php } ?>
>
	<?php if ($icon) { ?>
		<i aria-hidden="true" class="fdi fa fa-fw <?php echo $icon; ?>"></i>
		<span class="sr-only"><?php echo $srtext ;?></span>
	<?php } else if ($display) { ?>
		<span><?php echo $display; ?></span>
	<?php } ?>
</a>
