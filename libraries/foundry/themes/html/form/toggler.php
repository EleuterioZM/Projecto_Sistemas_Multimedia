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
<div class="o-form-check" data-fd-toggler="<?php echo $this->fd->getName(); ?>" data-disabled="<?php echo $disabled; ?>" data-disabled-title="<?php echo JText::_($disabledTitle); ?>" data-disabled-desc="<?php echo JText::_($disabledDesc); ?>" <?php if ($dependency) { ?> data-dependency="<?php echo $dependency; ?>" data-dependency-value="<?php echo $dependencyValue; ?>" <?php } ?>>
	<input type="checkbox" id="<?php echo $id; ?>" class="fd-custom-check is-switch" value="1" data-fd-toggler-checkbox="<?php echo $this->fd->getName(); ?>" <?php echo $checked ? 'checked="checked"' : ''; ?> <?php echo $disabled ? 'disabled="disabled"' : ''; ?> />
	<label class="o-form-check__text" for="<?php echo $id ? $id : $name; ?>"></label>

	<?php echo $this->fd->html('form.hidden', $name, $checked ? '1' : '0', '', $attributes); ?>
</div>