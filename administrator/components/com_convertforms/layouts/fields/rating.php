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


$atts = [
	'half_ratings' => $field->half_ratings,
	'icon' => basename($field->icon, '.svg'),
	'size' => (int) $field->size,
	'selected_color' => $field->selected_color,
	'unselected_color' => $field->unselected_color,
	'max_rating' => $field->max_rating,
	'load_css_vars' => true,
 ];

echo $class->toWidget($atts);

?>