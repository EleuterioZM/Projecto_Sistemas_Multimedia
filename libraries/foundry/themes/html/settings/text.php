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
		<?php if ($size) { ?>
		<div class="grid grid-cols-12">
			<div class="col-span-12 md:col-span-<?php echo $size;?>">
		<?php } ?>

			<?php if ($prefix || $postfix) { ?>
			<div class="o-input-group">
			<?php }?>

				<?php if ($prefix) { ?>
				<span class="input-group-addon">
					<?php echo JText::_($prefix); ?>
				</span>
				<?php } ?>

				<?php echo $this->fd->html('form.text', $name, $this->fd->config()->get($name, ''), $name, [
					'attributes' => $attributes,
					'class' => $class,
					'help' => $help
				]); ?>

				<?php if ($postfix) { ?>
				<span class="o-btn o-btn--default-o">
					<?php echo JText::_($postfix); ?>
				</span>
				<?php } ?>

			<?php if ($prefix || $postfix) { ?>
			</div>
			<?php } ?>

		<?php if ($size) { ?>
			</div>
		</div>
		<?php } ?>

		<?php if ($instructions && !is_callable($instructions)) { ?>
		<div class="mt-md">
			<?php echo $instructions;?>
		</div>
		<?php } ?>

		<?php if ($instructions && is_callable($instructions)) { ?>
			<?php echo $instructions();?>
		<?php } ?>
	</div>
</div>
