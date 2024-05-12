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
<div class="app-alert o-alert o-alert--<?php echo $type;?> <?php echo $class;?>" style="margin-bottom: 0;padding: 15px 24px;font-size: 12px;" 
	<?php echo $attributes;?>
>
	<div class="flex items-center">
		<div class="flex-grow">
			<?php if ($icon) { ?>
				<?php echo $this->fd->html('icon.font', $icon); ?> &nbsp;
			<?php } ?>

			<?php echo JText::_($message);?>
		</div>

		<div class="flex-shrink-0 pl-sm">
			<?php if ($button) { ?>
				<?php echo $this->fd->html('button.link', $button->url, $button->text, $button->type, 'default', ['attributes' => $button->attributes]); ?>
			<?php } ?>

			<?php if ($dismissible) { ?>
				<a href="javascript:void(0);" class="o-alert__close" <?php echo $dismissAttribute; ?>>×</a>
			<?php } ?>
		</div>
	</div>
</div>
