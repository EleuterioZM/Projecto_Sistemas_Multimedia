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
<div class="o-input-group" id="<?php echo $id;?>" data-fd-datetimepicker data-appearance="<?php echo $appearance;?>" data-uid="<?php echo $uid;?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
	<input type="text" data-input
		name="<?php echo $name;?>" 
		<?php echo $id ? 'id="' . $uid . '"' : '';?>
		class="o-form-control <?php echo $class; ?>"
		value="<?php echo $this->fd->html('str.escape', $value);?>"
		<?php echo $attributes;?>
		data-td-target="#<?php echo $uid;?>"
	/>

	<?php echo $this->fd->html('button.link', null, '<i class="fdi far fa-calendar-alt"></i>', 'default', 'default', [
		'attributes' => 'data-toggle'
	]); ?>
</div>