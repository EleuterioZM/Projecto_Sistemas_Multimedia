<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);
?>
<div class="tf-dimension-control">
	<div class="tf-dimension-controls">
		<?php
		foreach ($dimensions as $key => $label)
		{
			$item_name = $name . '[' . $key . ']';
			$item_value = isset($value->$key) ? $value->$key : '';
			$is_linked = isset($value->linked) ? $value->linked : $linked;
			?>
			<div class="item">
				<input type="number" value="<?php echo $item_value; ?>" class="tf-dimension-control-input" id="<?php echo $item_name; ?>" name="<?php echo $item_name; ?>" />
				<label for="<?php echo $item_name; ?>"><?php echo JText::_($label) ?></label>
			</div>
			<?php
		}
		?>
	</div>
	<a href="#" class="icon-link tf-dimension-control-link-button<?php echo $is_linked ? ' active' : ''; ?>"></a>
	<input type="hidden" class="tf-dimension-control-link-value" value="<?php echo $is_linked ? '1' : '0'; ?>" name="<?php echo $name; ?>[linked]" />
</div>