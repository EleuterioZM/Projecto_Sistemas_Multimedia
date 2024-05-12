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
<div class="flex items-center min-w-[240px] bg-white cursor-pointer px-md <?php echo $class; ?>" data-fd-date-range-<?php echo $uid; ?> 
data-appearance="<?php echo $appearance; ?>" 
data-accent="<?php echo $accent; ?>">
	<div class="flex-grow flex-shrink-0">
		<i class="fdi far fa-calendar"></i>&nbsp;
		<span data-fd-date-range-display><?php echo !$start && !$end ? $placeholder : ''; ?></span>
	</div>
	<i class="fdi fa fa-caret-down ml-xs"></i>
</div>
<?php echo $this->fd->html('form.hidden', 'daterange[start]', '', '', 'data-fd-date-start'); ?>
<?php echo $this->fd->html('form.hidden', 'daterange[end]', '', '', 'data-fd-date-end'); ?>

<button type="button" class="app-filter-bar__search-input-reset <?php echo $start && $end ? '' : 't-hidden'; ?>" data-fd-date-range-reset>
	<i class="fdi fa fa-times"></i>
</button>