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

if (!$readonly && !$disabled)
{
	\JHtml::script('plg_system_nrframework/widgets/colorpicker.js', ['relative' => true, 'version' => 'auto']);
}

if ($load_stylesheet)
{
	\JHtml::stylesheet('plg_system_nrframework/widgets/colorpicker.css', ['relative' => true, 'version' => 'auto']);
}

if ($load_css_vars)
{
	JFactory::getDocument()->addStyleDeclaration('
		.nrf-colorpicker-wrapper.' . $id . ' {
			--input-background-color: ' . $input_bg_color . ';
			--input-border-color: ' . $input_border_color . ';
			--input-border-color-focus: ' . $input_border_color_focus . ';
			--input-text-color: ' . $input_text_color . ';
		}
	');
}
?>
<div class="nrf-widget nrf-colorpicker-wrapper<?php echo $css_class; ?>">
	<input type="color"
		value="<?php echo $value; ?>"
		<?php if ($readonly || $disabled): ?>
		disabled
		<?php endif; ?>
	/>
	<input type="text"
		id="<?php echo $id; ?>"
		name="<?php echo $name; ?>"
		class="<?php echo $input_class; ?>"
		value="<?php echo $value; ?>"
		placeholder="<?php echo $placeholder; ?>"
		<?php if ($required) { ?>
			required
		<?php } ?>
		<?php if ($readonly): ?>
		readonly
		<?php endif; ?>
	/>
</div>