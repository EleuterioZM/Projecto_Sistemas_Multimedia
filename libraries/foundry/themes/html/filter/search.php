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
<div class="app-filter-bar__cell" data-fd-table-search="<?php echo $this->fd->getName();?>">
	<input type="text" value="<?php echo $this->fd->html('str.escape', $value);?>" placeholder="<?php echo JText::_($placeholder);?>" data-table-search-input
		<?php if ($tooltip) { ?>
		data-<?php echo $this->fd->getShortName();?>-provide="tooltip"
		data-title="<?php echo JText::_($tooltip);?>"
		<?php } ?>
		class="app-filter-bar__search-input" name="search"
	/>
	<span class="app-filter-bar__search-btn-group">
		<button class="app-filter-bar__search-btn <?php echo empty($this->fd->html('str.escape', $value)) ? 't-invisible' : '';?>" data-table-reset>
			<i class="fdi fa fa-times text-danger"></i>
		</button>
		<button class="app-filter-bar__search-btn" data-table-search>
			<i class="fdi fa fa-search"></i>
		</button>
	</span>
</div>
