<?php
/**
* @package      Foundry
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<a href="javascript:void(0);"
	data-<?php echo $this->fd->getShortName();?>-provide="tooltip"
	data-title="<?php echo JText::sprintf('FD_SORT_BY', $title);?>"
	data-fd-table-sort
	data-sort="<?php echo $column;?>"
	data-direction="<?php echo $currentDirection === 'desc' ? 'asc' : 'desc';?>"
	class="<?php echo $class; ?>"
>
	<?php echo $title; ?>

	<?php if (str_ireplace('.', '', $column) === $currentOrdering) { ?>
		<?php if ($currentDirection === 'asc') { ?>
		<i class="fdi fa fa-caret-up"></i>
		<?php } ?>

		<?php if ($currentDirection === 'desc') { ?>
		<i class="fdi fa fa-caret-down"></i>
		<?php } ?>
	<?php } ?>
</a>
