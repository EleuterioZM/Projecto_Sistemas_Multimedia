<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

\JHtml::script('https://cdn.jsdelivr.net/npm/signature_pad@4.0.4/dist/signature_pad.umd.min.js');
\JHtml::script('plg_system_nrframework/widgets/signature.js', ['relative' => true, 'version' => 'auto']);

if ($load_stylesheet)
{
	\JHtml::stylesheet('plg_system_nrframework/widgets/signature.css', ['relative' => true, 'version' => 'auto']);
}

if ($load_css_vars)
{
	JFactory::getDocument()->addStyleDeclaration('
		.nrf-widget.signature.' . $id . ' {
			--width: ' . $width . ';
			--height: ' . $height . ';
			--input-border-width: ' . $border_width . ';
			--input-border-color: ' . $border_color . ';
			--input-border-radius: ' . $border_radius . ';
			--input-background-color: ' . $background_color . ';
			--line-color: ' . (is_null($line_color) ? $border_color : $line_color) . ';
		}
	');
}
?>
<div class="nrf-widget signature<?php echo $css_class; ?>">
	<div class="nrf-signature-body">
		<div class="actions">
			<svg class="nrf-signature-clear" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"></path></svg>
			<svg class="nrf-signature-reset" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M370.72 133.28C339.458 104.008 298.888 87.962 255.848 88c-77.458.068-144.328 53.178-162.791 126.85-1.344 5.363-6.122 9.15-11.651 9.15H24.103c-7.498 0-13.194-6.807-11.807-14.176C33.933 94.924 134.813 8 256 8c66.448 0 126.791 26.136 171.315 68.685L463.03 40.97C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.749zM32 296h134.059c21.382 0 32.09 25.851 16.971 40.971l-41.75 41.75c31.262 29.273 71.835 45.319 114.876 45.28 77.418-.07 144.315-53.144 162.787-126.849 1.344-5.363 6.122-9.15 11.651-9.15h57.304c7.498 0 13.194 6.807 11.807 14.176C478.067 417.076 377.187 504 256 504c-66.448 0-126.791-26.136-171.315-68.685L48.97 471.03C33.851 486.149 8 475.441 8 454.059V320c0-13.255 10.745-24 24-24z"></path></svg>
		</div>
		<canvas data-pen-color="<?php echo $pen_color; ?>"></canvas>
		<input type="hidden" value="<?php echo htmlspecialchars($value); ?>" name="<?php echo $name; ?>" />
		<input type="hidden" value="<?php echo htmlspecialchars($value); ?>" class="initial-value" />
	</div>
</div>