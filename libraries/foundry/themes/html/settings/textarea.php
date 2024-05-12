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
<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $visible ? '' : 't-hidden';?>" <?php echo $wrapperAttributes;?>
	data-fd-form-group
>
	<?php echo $this->fd->html('form.label', $title, $name, JText::_($title), JText::_($desc)); ?>

	<div class="flex-grow">
		<?php echo $this->fd->html('form.textarea', $name, $this->fd->config()->get($name), $name, $options); ?>

		<?php if ($instructions) { ?>
		<div class="mt-md">
			<?php echo JText::_($instructions);?>
		</div>
		<?php } ?>
	</div>
</div>
