<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

/**
 *  The Rating Widget
 */
class Rating extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		// The SVG icon representing the rating icon. Available values: check, circle, flag, heart, smiley, square, star, thumbs_up
		'icon' => 'star',

		// The default value of the widget. 
		'value' => 0,

		// How many stars to show?
		'max_rating' => 5,

		// Whether to show half ratings
		'half_ratings' => false,

		// The size of the rating icon in pixels.
		'size' => 24,

		// The color of the icon in the default state
		'selected_color' => '#f6cc01',

		// The color of the icon in the selected and hover state
		'unselected_color' => '#bdbdbd'
	];

	/**
	 * Class constructor
	 *
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->options['value'] = $this->options['value'] > $this->options['max_rating'] ? $this->options['max_rating'] : $this->options['value'];
		$this->options['icon_url'] = \JURI::root() . 'media/plg_system_nrframework/svg/rating/' . $this->options['icon'] . '.svg';
		$this->options['max_rating'] = $this->options['half_ratings'] ? 2 * $this->options['max_rating'] : $this->options['max_rating'];
	}
}