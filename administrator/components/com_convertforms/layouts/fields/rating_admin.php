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

$css = @file_get_contents(JPATH_ROOT . '/media/plg_system_nrframework/css/widgets/rating.css');

echo '
	<style>
		' . $css . '
		.nrf-rating-wrapper.' . $field->input_id . ' {
			--rating-selected-color: ' . $field->selected_color . ';
			--rating-unselected-color: ' . $field->unselected_color . ';
			--rating-size: ' . $field->size . 'px;
		}
	</style>
';

$atts = [
	'icon' => basename($field->icon, '.svg'),
	'size' => (int) $field->size,
	'selected_color' => $field->selected_color,
	'unselected_color' => $field->unselected_color,
	'max_rating' => $field->max_rating
];

echo $class->toWidget($atts);

?>