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
<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
	<div class="app-filter-bar__filter-wrap">
		<div class="app-filter-select-group">
			<select class="o-form-control" name="<?php echo $name;?>" 
				data-fd-table-filter="<?php echo $this->fd->getName();?>" 
				data-fd-select2="<?php echo $this->fd->getName();?>" 
				data-theme="backend" 
				data-appearance="<?php echo $this->fd->getAppearance();?>"
			>
				<option value="all"<?php echo !$selected ? ' selected="selected"' : '';?>>
					<?php echo JText::_($selectText);?>
				</option>
				<option value="<?php echo $publishedValue; ?>"<?php echo $selected == $publishedValue ? ' selected="selected"' : '';?>>
					<?php echo JText::_($publishedText);?>
				</option>
				<option value="<?php echo $unpublishedValue; ?>"<?php echo $selected == $unpublishedValue ? ' selected="selected"' : '';?>>
					<?php echo JText::_($unpublishedText);?>
				</option>
			</select>
			<div class="app-filter-select-group__drop"></div>
		</div>
	</div>
</div>
