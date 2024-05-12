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
<label class="o-form-check" <?php echo $allowed ? 'data-table-grid-checkbox' : '';?> 
	<?php if (!$allowed) { ?>
	data-original-title="<?php echo JText::_('FD_SELECTION_DISABLED');?>"
	data-<?php echo strtolower($this->fd->getShortName());?>-provide="tooltip"
	<?php } ?>
>
	<input type="checkbox" id="cb<?php echo $number;?>" name="<?php echo $name;?>[]" value="<?php echo $value;?>" class="fd-custom-check"
		<?php if ($allowed) { ?>
		onclick="Joomla.isChecked(this.checked);" 
		data-fd-table-id="<?php echo $this->fd->getName();?>"
		<?php } else { ?>
		disabled="disabled"
		<?php } ?>
	/>
	<span class="o-form-check__text"></span>
</label>