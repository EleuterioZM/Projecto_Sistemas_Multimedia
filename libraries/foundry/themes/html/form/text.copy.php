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
<div class="o-input-group">
	<input type="text" id="<?php echo $id;?>" <?php echo $attributes;?> class="o-form-control <?php echo $class;?>" value="<?php echo $value;?>" style="pointer-events:none;" />

	<?php echo $this->fd->html('button.standard',$this->fd->html('icon.font', 'fdi far fa-clipboard'), 'default', 'default', [
		'attributes' => '
				data-fd-copy="' . $this->fd->getName() . '"
				data-original-title="' . $tooltips->copy . '"
				data-copied="' . $tooltips->copied . '"
				data-copy="' . $tooltips->copy . '"
				data-placement="bottom"
				data-' . $this->fd->getComponentShortName() . '-provide="tooltip"
			'
	]); ?>
</div>