<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

/**
 *  Color picker
 */
class ColorPicker extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		// The default value of the widget. 
		'value' => '#dedede',

		// The input border color
		'input_border_color' => '#dedede',

		// The input border color on focus
		'input_border_color_focus' => '#dedede',

		// The input background color 
		'input_bg_color' => '#fff',

		// Input text color
		'input_text_color' => '#333'
	];
}