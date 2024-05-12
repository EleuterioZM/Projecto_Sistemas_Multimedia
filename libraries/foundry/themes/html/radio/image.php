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
<label class="o-form-check px-md py-md bg-white hover:bg-gray-50 <?php echo $inline ? 'inline-flex' : ''; ?> <?php echo $class; ?>" for="<?php echo $id; ?>">
	<input type="radio" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="fd-custom-radio" value="<?php echo $this->fd->html('str.escape', $value); ?>" <?php echo $checked ? 'checked="checked"' : ''; ?> />
	<span class="o-form-check__text"><?php echo JText::_($label); ?></span>

	<div class="ml-auto">
		<img src="<?php echo $url; ?>">
	</div>
</label>