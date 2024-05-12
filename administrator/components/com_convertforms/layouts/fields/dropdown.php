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

<div class="cf-select <?php echo $field->size ?>">
	<select name="<?php echo $field->input_name ?>" id="<?php echo $field->input_id; ?>" 
			<?php if (isset($field->required) && $field->required) { ?>
				required
			<?php } ?>

			class="<?php echo $field->class ?>"
		>
		<?php foreach ($field->choices as $choiceKey => $choice) { ?>
			<option 
				value="<?php echo htmlspecialchars($choice['value']); ?>" 
				data-calc-value="<?php echo isset($choice['calc-value']) ? htmlspecialchars($choice['calc-value']) : '' ?>"
				<?php if ($choice['value'] == $field->value) { ?> selected <?php } ?>
				<?php if (isset($choice['disabled'])) { ?> disabled <?php } ?>>
				<?php echo $choice['label']; ?>
			</option>
		<?php } ?>
	</select>
</div>