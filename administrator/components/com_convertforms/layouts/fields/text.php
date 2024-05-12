<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
extract($displayData);

?>

<input type="<?php echo $field->type; ?>" name="<?php echo $field->input_name ?>" id="<?php echo $field->input_id; ?>"
	<?php if (isset($field->required) && $field->required) { ?>
		required
	<?php } ?>

	<?php if (isset($field->placeholder) && $field->placeholder != '') { ?>
		placeholder="<?php echo htmlspecialchars($field->placeholder, ENT_COMPAT, 'UTF-8'); ?>"
	<?php } ?>

	<?php if (isset($field->value) && $field->value != '') { ?>
		value="<?php echo $field->value; ?>"
	<?php } ?>
	
	<?php if (isset($field->browserautocomplete) && $field->browserautocomplete == '1') { ?>
		autocomplete="off"
	<?php } ?>

	<?php if (isset($field->inputmask) && $field->inputmask != '') { ?>
		data-inputmask-mask="<?php echo $field->inputmask ?>"
	<?php } ?>

	<?php if (isset($field->readonly) && $field->readonly == '1') { ?>
		readonly
	<?php } ?>

	<?php if (isset($field->minchars) && $field->minchars > 0) { ?>
		minlength="<?php echo $field->minchars; ?>"
	<?php } ?>

	<?php if (isset($field->maxchars) && $field->maxchars > 0) { ?>
		maxlength="<?php echo $field->maxchars; ?>"
	<?php } ?>

	<?php if (isset($field->htmlattributes) && !empty($field->htmlattributes)) { ?>
		<?php foreach ($field->htmlattributes as $key => $value) { ?>
			<?php echo $key ?>="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8') ?>"
		<?php } ?>
	<?php } ?>

	class="<?php echo $field->class ?>"
>