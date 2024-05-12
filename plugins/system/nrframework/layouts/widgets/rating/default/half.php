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

$css_class .= ' ' . $size;
?>
<div class="nrf-widget nrf-rating-wrapper half <?php echo $css_class; ?>">
	<?php
	$counter = 0;
	$prev_counter = 0;

	// initial value value
	$rating_value = 0.5;

	for ($i = 0; $i < $max_rating; $i++)
	{
		$label_class = '';

		// wrapper start - for half rating item (half and full star)
		if ($counter % 2 == 0)
		{
			$prev_counter = $counter;
			?><span class="rating_item_group"><?php
		}

		$rating_item_type = fmod($rating_value, 1) == 0.5 ? 'half' : 'full';
		$rating_id = $id . '_' . $i . '_' . $rating_item_type;

		if ($value && $rating_value <= $value)
		{
			$label_class = ' iconFilled';
		}
		?>
		<input type="radio"
			class="<?php echo $input_class; ?>"
			id="<?php echo $rating_id; ?>"
			name="<?php echo $name; ?>"
			value="<?php echo $rating_value; ?>"
			<?php if ($value && $rating_value == $value): ?>
			checked
			<?php endif; ?>
			<?php if ($readonly || $disabled): ?>
			disabled
			<?php endif; ?>
		/>
		<label class="<?php echo $rating_item_type . $label_class; ?>" for="<?php echo $rating_id; ?>" title="<?php echo $rating_value; ?> <?php echo sprintf(\JText::_('NR_STAR'), ($rating_value > 1 ? 's' : '')); ?>">
			<svg class="svg-item">
				<use xlink:href="<?php echo $icon_url; ?>#nrf-ratings-<?php echo $icon; ?>" />
			</svg>
		</label>
		<?php
		
		// wrapper end - for half rating item
		if ($counter == $prev_counter + 1)
		{
			?></span><?php
		}
		$counter++;

		// increase value
		$rating_value += 0.5;
	}
	?>
</div>