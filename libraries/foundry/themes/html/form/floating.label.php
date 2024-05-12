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
<div class="o-form-group o-form-group--ifta <?php echo $class; ?>" data-fd-label="<?php echo $this->fd->getName();?>" <?php echo $attributes; ?>>
	<?php if ($leadIcon) { ?>
	<a class="o-form-group__icon" href="javascript:void(0);" data-fd-floating-label-lead-icon>
		<i class="<?php echo $leadIcon; ?>"></i>
	</a>
	<?php } ?>

	<?php if ($trailIcon) { ?>
	<a class="o-form-group__icon" href="javascript:void(0);" data-fd-floating-label-trail-icon>
		<i class="<?php echo $trailIcon; ?>"></i>
	</a>
	<?php } ?>

	<?php if (!$html) { ?>
		<?php echo $this->fd->html('form.' . $type, $name, $value, $id, [
			'class' => 'o-form-control', 
			'autocomplete' => $autocomplete,
			'attributes' => $fieldAttributes
		]); ?>
	<?php } ?>

	<?php if ($html && is_callable($html)) { ?>
		<?php echo $html(); ?>
	<?php } ?>

	<?php if ($html && !is_callable($html)) { ?>
		<?php echo $html; ?>
	<?php } ?>

	<label class="o-form-label" for="<?php echo $id;?>"><?php echo $label;?></label>

	<?php if ($error) { ?>
	<div class="t-hidden" data-fd-floating-label-error-message <?php echo $errorAttributes; ?>>
		<div class="text-danger" data-error-message>
			<?php echo JText::_($error); ?>
		</div>
	</div>
	<?php } ?>
</div>