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

if (count($field->choices) > 1)
{
	JHtml::_('script', 'com_convertforms/checkbox.js', array('version' => 'auto', 'relative' => true));
}

$choiceLayout = (isset($field->choicelayout) && !empty($field->choicelayout)) ? 'cf-list-' . $field->choicelayout . '-columns' : '';

?>

<div class="cf-list <?php echo $choiceLayout; ?>">
	<?php foreach ($field->choices as $choiceKey => $choice) { ?>
		<div class="cf-checkbox-group <?php if (isset($field->required) && $field->required) { ?> cf-checkbox-group-required <?php } ?>">
			<input type="checkbox" name="<?php echo $field->input_name ?>[]" id="<?php echo $field->input_id . "_" . $choiceKey ?>"
				value="<?php echo htmlspecialchars($choice['value']); ?>"
				data-calc-value="<?php echo htmlspecialchars($choice['calc-value']) ?>"

				<?php if (in_array($choice['value'], $field->value)) { ?> checked <?php } ?>

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