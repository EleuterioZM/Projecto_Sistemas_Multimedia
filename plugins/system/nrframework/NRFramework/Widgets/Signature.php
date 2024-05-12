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
 *  Signature
 */
class Signature extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		// The base64 image data of the signature. 
		'value' => '',

		// The width of the signature in pixels or empty for auto width. The width will be taken from the signature container.
		'width' => '',

		// The height of the signature in pixels.
		'height' => '300px',

		// The background color of the signature.
		'background_color' => '#ffffff',

		// The border color of the canvas.
		'border_color' => '#dedede',

		/**
		 * The border radius of the canvas.
		 * 
		 * Example values: 0, 0px, 50px, 50%
		 */
		'border_radius' => 0,

		/**
		 * The border width of the canvas.
		 * 
		 * Example values: 0, 1px, 5px
		 */
		'border_width' => '1px',

		// Whether to show the horizontal line within the canvas
		'show_line' => true,
		
		/**
		 * The line color.
		 * 
		 * If `null`, retrieves the value from `border_color`
		 */
		'line_color' => null,
		
		// The pen color
		'pen_color' => '#000'
	];

	/**
	 * Class constructor
	 *
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		parent::__construct($options);

		if ($this->options['readonly'])
		{
			$this->options['css_class'] .= ' readonly';
		}

		if (!empty($this->options['value']))
		{
			$this->options['css_class'] .= ' painted has-value';
		}
		
		if ($this->options['show_line'])
		{
			$this->options['css_class'] .= ' show-line';
		}
	}
}