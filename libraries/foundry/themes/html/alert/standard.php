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
<div class="o-alert o-alert--<?php echo $class;?> <?php echo $dismissible ? 'o-alert--dismissible' : '';?> <?php echo $customClass;?>" data-fd-alert="<?php echo $this->fd->getName();?>" <?php echo $attributes;?>>
	<div class="flex items-center">
		<div class="flex-grow" data-fd-alert-message>
			<?php if ($icon) { ?>
				<i class="fdi <?php echo $icon;?>"></i>&nbsp;
			<?php } ?>
			<?php echo JText::_($text); ?>

			<?php if ($button) { ?>
				<?php echo $button; ?>
			<?php } ?>
		</div>

		<?php if ($dismissible) { ?>
		<div class="flex-shrink-0 pl-sm">
			<a href="javascript:void(0);" class="o-alert__close" data-fd-dismiss>×</a>
		</div>
		<?php } ?>
	</div>
</div>
