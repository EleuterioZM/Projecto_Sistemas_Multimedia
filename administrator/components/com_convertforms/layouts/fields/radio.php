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

$choiceLayout = (isset($field->choicelayout) && !empty($field->choicelayout)) ? 'cf-list-' . $field->choicelayout . '-columns' : '';

?>

<div class="cf-list <?php echo $choiceLayout; ?>">
	<?php foreach ($field->choices as $choiceKey => $choice) { ?>
		<div class="cf-radio-group">
			<input type="radio" name="<?php echo $field->input_name ?>[]" id="<?php echo $field->input_id . "_" . $choiceKey ?>"
				value="<?php echo htmlspecialchars($choice['value']); ?>"
				data-calc-value="<?php echo htmlspecialchars($choice['calc-value']) ?>"

				<?php if ($choice['value'] == $field->value) { ?> checked <?php } ?>

				<?php if (isset($field->required) && $field->required) { ?>
					required
				<?php } ?>

				class="<?php echo $field->class; ?>"
			>

			<label class="cf-label" for="<?php echo $field->input_id . "_" . $choiceKey; ?>">
				<?php echo $choice['label'] ?>
			</label>
		</div>
	<?php } ?>
</div>