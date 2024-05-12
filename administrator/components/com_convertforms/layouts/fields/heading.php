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

// Load custom fonts into the document
\NRFramework\Fonts::loadFont($field->font_family);

$styles = [
	'font-size: ' . (int) $field->font_size . 'px',
	'font-family: ' . $field->font_family,
	'line-height: ' . (int) $field->line_height . 'px',
	'letter-spacing:' . (int) $field->letter_spacing . 'px',
	'text-align:' . $field->content_alignment
];

// init vars
$link_start = $link_end = '';

// link
if ($field->use_link == '1')
{
	$link_atts = ($field->open_new_tab == '1') ? 'target="_blank"' : '';

	$link_start = '<a href="' . $field->link_url . '"' . $link_atts . '>';
	$link_end = '</a>';
}
?>
<<?php echo $field->heading_type; ?> style="<?php echo implode(';', $styles); ?>"><?php echo $link_start . $field->label . $link_end; ?></<?php echo $field->heading_type; ?>>