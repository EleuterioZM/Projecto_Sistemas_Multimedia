<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Widgets;

defined('_JEXEC') or die;

/**
 * Countdown
 */
class Countdown extends Widget
{
	/**
	 * Widget default options
	 *
	 * @var array
	 */
	protected $widget_options = [
		/**
		 * The Countdown type:
		 * 
		 * - static: Counts down to a specific date and time. Universal deadline for all visitors.
		 * - evergreen: Set-and-forget solution. The countdown starts when your visitor sees the offer.
		 */
		'countdown_type' => 'static',

		// The Static Countdown Date
		'value' => '',

		/**
		 * The timezone that will be used.
		 * 
		 * - server - Use server's timezone
		 * - client - Use client's timezone
		 */
		'timezone' => 'server',

		// Dynamic Days
		'dynamic_days' => 0,

		// Dynamic Hours
		'dynamic_hours' => 0,

		// Dynamic Minutes
		'dynamic_minutes' => 0,

		// Dynamic Seconds
		'dynamic_seconds' => 0,
		
		/**
		 * The countdown format.
		 * 
		 * Available tags:
		 * {years}
		 * {months}
		 * {days}
		 * {hours}
		 * {minutes}
		 * {seconds}
		 */
		'format' => '{days} days, {hours} hours, {minutes} minutes and {seconds} seconds',

		/**
		 * The countdown theme.
		 * 
		 * Available themes:
		 * default
		 * oneline
		 * custom
		 */
		'theme' => 'default',

		/**
		 * Set the action once countdown finishes.
		 * 
		 * Available values:
		 * keep 	- Keep the countdown visible
		 * hide 	- Hide the countdown
		 * restart 	- Restart the countdown
		 * message	- Show a message
		 * redirect	- Redirect to a URL
		 */
		'countdown_action' => 'keep',

		/**
		 * The message appearing after the countdown has finished.
		 * 
		 * Requires `countdown_action` to be set to `message`
		 * 
		 * Example: Countdown finished.
		 */
		'finish_text' => '',

		/**
		 * The redirect URL once the countdown expires.
		 * 
		 * Requires `countdown_action` to be set to `redirect`
		 */
		'redirect_url' => '',

		/**
		 * Widget Settings
		 */
		// Alignment
		'align' => '',

		// Padding
		'padding' => null,

		// Margin
		'margin' => null,

		// Gap
		'gap' => 20,
		
		// Background Color
		'background_color' => '',

		/**
		 * Unit Display Settings
		 */
		// Whether to display Days
		'days' => true,

		// Days Label
		'days_label' => 'Days',
		
		// Whether to display Hours
		'hours' => true,

		// Hours Label
		'hours_label' => 'Hrs',
		
		// Whether to display Minutes
		'minutes' => true,

		// Minutes Label
		'minutes_label' => 'Mins',
		
		// Whether to display Seconds
		'seconds' => true,
		
		// Seconds Label
		'seconds_label' => 'Secs',
		
		// Whether to display a separator between the units
		'separator' => false,
		
		// Whether to display numbers in 00 or 0 format
		'double_zeroes_format' => true,

		/**
		 * Unit Item Settings
		 */
		// The size (width, height) of the unit item in pixels
		'item_size' => null,
		
		// The unit item border width
		'item_border_width' => '',

		// The unit item border style
		'item_border_style' => '',

		// The unit item border color
		'item_border_color' => '',

		// The unit item border radius
		'item_border_radius' => null,

		// Item Background Color
		'item_background_color' => '',

		/**
		 * Unit Digits Container Settings
		 */
		// Digits wrapper Min Width
		'digits_wrapper_min_width' => 0,

		// The digits wrapper padding
		'digits_wrapper_padding' => null,

		// The digits wrapper border radius
		'digits_wrapper_border_radius' => null,

		// The digits wrapper background color.
		'digits_wrapper_background_color' => '',

		/**
		 * Unit Digit Settings
		 */
		// Digits Font Size
		'digits_font_size' => 25,

		// Digits Font Weight
		'digits_font_weight' => '400',

		// Digit Min Width
		'digit_min_width' => 0,

		// The digits padding
		'digits_padding' => null,

		// The digits border radius
		'digit_border_radius' => null,

		// Digits Gap
		'digits_gap' => null,

		// Digit Item Background Color. This applies for each of the 2 digits on a unit.
		'digit_background_color' => '',

		// Digit Item Text Color
		'digit_text_color' => '',

		/**
		 * Unit Label Settings
		 */
		// Label Font Size
		'label_font_size' => 13,

		// Label Font Weight
		'label_font_weight' => '400',

		// Unit Label Margin Top. The spacing between the unit and its label.
		'unit_label_margin_top' => 5,

		// Unit Label Color
		'unit_label_text_color' => '',

		// Extra attributes added to the widget
		'atts' => '',

		// Custom CSS printed after the widget assets
		'custom_css' => '.foo {}',

		// Preview HTML used prior to JS initializing the Countdown
		'preview_html' => ''
	];

	/**
	 * Class constructor
	 *
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		parent::__construct($options);

        \JText::script('NR_AND_LC');

		$this->prepare();
		
		if ($this->options['theme'] !== 'custom')
		{
			$this->setCSSVars();
			$this->setResponsiveCSS();
		}
	}

	/**
	 * Prepares the countdown.
	 * 
	 * @return  void
	 */
	private function prepare()
	{
		$this->setCSSVars();

		$this->options['css_class'] .= ' is-preview ' . $this->options['theme'] . ' ' . $this->options['align'];

		if (!empty($this->options['value']) && $this->options['value'] !== '0000-00-00 00:00:00')
		{
			if ($this->options['countdown_type'] === 'static' && $this->options['timezone'] === 'server')
			{
				// Get timezone
				$tz = new \DateTimeZone(\JFactory::getApplication()->getCfg('offset', 'UTC'));

				// Convert given date time to UTC
				$this->options['value'] = date_create($this->options['value'], $tz)->setTimezone(new \DateTimeZone('UTC'))->format('c');
				
				// Apply server timezone
				$this->options['value'] = (new \DateTime($this->options['value']))->setTimezone($tz)->format('c');
			}
		}

		$this->options['preview_html'] = $this->getPreviewHTML();

		// Set countdown payload
		$payload = [
			'data-countdown-type="' . $this->options['countdown_type'] . '"',
			'data-value="' . $this->options['value'] . '"',
			'data-timezone="' . $this->options['timezone'] . '"',
			'data-separator="' . (json_decode($this->options['separator']) ? 'true' : 'false') . '"',
			'data-double-zeroes-format="' . (json_decode($this->options['double_zeroes_format']) ? 'true' : 'false') . '"',
			'data-dynamic-days="' . $this->options['dynamic_days'] . '"',
			'data-dynamic-hours="' . $this->options['dynamic_hours'] . '"',
			'data-dynamic-minutes="' . $this->options['dynamic_minutes'] . '"',
			'data-dynamic-seconds="' . $this->options['dynamic_seconds'] . '"',
			'data-finish-text="' . htmlspecialchars($this->options['finish_text']) . '"',
			'data-redirect-url="' . $this->options['redirect_url'] . '"',
			'data-theme="' . $this->options['theme'] . '"',
			'data-countdown-action="' . $this->options['countdown_action'] . '"',
			'data-days="' . (json_decode($this->options['days']) ? 'true' : 'false') . '"',
			'data-days-label="' . $this->options['days_label'] . '"',
			'data-hours="' . (json_decode($this->options['hours']) ? 'true' : 'false') . '"',
			'data-hours-label="' . $this->options['hours_label'] . '"',
			'data-minutes="' . (json_decode($this->options['minutes']) ? 'true' : 'false') . '"',
			'data-minutes-label="' . $this->options['minutes_label'] . '"',
			'data-seconds="' . (json_decode($this->options['seconds']) ? 'true' : 'false') . '"',
			'data-seconds-label="' . $this->options['seconds_label'] . '"'
		];

		// Only set the format for custom-themed countdown instances
		if ($this->options['theme'] === 'custom')
		{
			$payload[] = 'data-format="' . htmlspecialchars($this->options['format']) . '"';
		}

		$this->options['atts'] = implode(' ', $payload);
	}

	/**
	 * Set widget CSS vars
	 * 
	 * @return  mixed
	 */
	private function setCSSVars()
	{
		if (!$this->options['load_css_vars'])
		{
			return;
		}

		$atts = [];

		if (!empty($this->options['digits_wrapper_background_color']))
		{
			$atts['digits-background-color'] = $this->options['digits_wrapper_background_color'];
		}

		if (!empty($this->options['background_color']))
		{
			$atts['background-color'] = $this->options['background_color'];
		}

		if (!empty($this->options['item_background_color']))
		{
			$atts['item-background-color'] = $this->options['item_background_color'];
		}

		if (!empty($this->options['unit_label_text_color']))
		{
			$atts['unit-label-text-color'] = $this->options['unit_label_text_color'];
		}

		if (!empty($this->options['digit_background_color']))
		{
			$atts['digit-background-color'] = $this->options['digit_background_color'];
		}

		if (!empty($this->options['digit_text_color']))
		{
			$atts['digit-text-color'] = $this->options['digit_text_color'];
		}

		if (!empty($this->options['unit_label_margin_top']))
		{
			$atts['unit-label-margin-top'] = $this->options['unit_label_margin_top'] . 'px';
		}

		if (!empty($this->options['digits_wrapper_min_width']))
		{
			$atts['digits-wrapper-min-width'] = $this->options['digits_wrapper_min_width'] . 'px';
		}

		if (!empty($this->options['digit_min_width']))
		{
			$atts['digit-min-width'] = $this->options['digit_min_width'] . 'px';
		}

		if (!empty($this->options['digits_font_weight']))
		{
			$atts['digits-font-weight'] = $this->options['digits_font_weight'];
		}

		if (!empty($this->options['label_font_weight']))
		{
			$atts['label-font-weight'] = $this->options['label_font_weight'];
		}

		if (!empty($this->options['item_border_width']) && !empty($this->options['item_border_style']) && !empty($this->options['item_border_color']))
		{
			$atts['item-border'] = $this->options['item_border_width'] . 'px ' . $this->options['item_border_style'] . ' ' . $this->options['item_border_color'];
		}

		if (empty($atts))
		{
			return;
		}

		if (!$css = \NRFramework\Helpers\CSS::cssVarsToString($atts, '.nrf-countdown.' . $this->options['id']))
		{
			return;
		}

		$this->options['custom_css'] = $css;
	}

	/**
	 * Sets the CSS for the responsive settings.
	 * 
	 * @return  void
	 */
	private function setResponsiveCSS()
	{
		$initial_breakpoints = [
			'desktop' => [],
			'tablet' => [],
			'mobile' => []
		];
		$responsive_css = $initial_breakpoints;

		// Add digits wrapper padding
		if ($digits_wrapper_padding = \NRFramework\Helpers\Controls\Spacing::getResponsiveSpacingControlValue($this->options['digits_wrapper_padding'], '--digits-padding', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $digits_wrapper_padding);
		}
		
		// Add widget padding
		if ($padding = \NRFramework\Helpers\Controls\Spacing::getResponsiveSpacingControlValue($this->options['padding'], 'padding', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $padding);
		}

		// Add widget margin
		if ($margin = \NRFramework\Helpers\Controls\Spacing::getResponsiveSpacingControlValue($this->options['margin'], 'margin', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $margin);
		}
		
		// Add gap
		if ($gap = \NRFramework\Helpers\Controls\Responsive::getResponsiveControlValue($this->options['gap'], '--gap', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $gap);
		}
		
		// Add digits gap
		if ($gap = \NRFramework\Helpers\Controls\Responsive::getResponsiveControlValue($this->options['digits_gap'], '--digits-gap', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $gap);
		}

		// Add Item Size
		if ($item_size = \NRFramework\Helpers\Controls\Responsive::getResponsiveControlValue($this->options['item_size'], '--item-size', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $item_size);
		}

		// Add Digits Font Size
		if ($digits_font_size = \NRFramework\Helpers\Controls\Responsive::getResponsiveControlValue($this->options['digits_font_size'], '--digits-font-size', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $digits_font_size);
		}

		// Add Label Font Size
		if ($label_font_size = \NRFramework\Helpers\Controls\Responsive::getResponsiveControlValue($this->options['label_font_size'], '--label-font-size', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $label_font_size);
		}

		// Add Digits Padding
		if ($digitsPadding = \NRFramework\Helpers\Controls\Spacing::getResponsiveSpacingControlValue($this->options['digits_padding'], '--digit-padding', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $digitsPadding);
		}

		// Add item border radius
		if ($itemBorderRadius = \NRFramework\Helpers\Controls\BorderRadius::getResponsiveSpacingControlValue($this->options['item_border_radius'], '--item-border-radius', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $itemBorderRadius);
		}

		// Add digits wrapper border radius
		if ($borderRadius = \NRFramework\Helpers\Controls\BorderRadius::getResponsiveSpacingControlValue($this->options['digits_wrapper_border_radius'], '--digits-border-radius', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $borderRadius);
		}

		// Add digits border radius
		if ($borderRadius = \NRFramework\Helpers\Controls\BorderRadius::getResponsiveSpacingControlValue($this->options['digit_border_radius'], '--digit-border-radius', 'px'))
		{
			$responsive_css = array_merge_recursive($responsive_css, $borderRadius);
		}

		if ($css = \NRFramework\Helpers\Responsive::renderResponsiveCSS($responsive_css, '.nrf-countdown.' . $this->options['id']))
		{
			$this->options['custom_css'] .= $css;
		}
	}

	/**
	 * Returns preview HTML.
	 * 
	 * @return  string
	 */
	private function getPreviewHTML()
	{
		if ($this->options['theme'] === 'custom')
		{
			return $this->options['format'];
		}

		$format_items = [
			'days' => $this->options['days'],
			'hours' => $this->options['hours'],
			'minutes' => $this->options['minutes'],
			'seconds' => $this->options['seconds']
		];

		$html = '';

		foreach ($format_items as $key => $value)
		{
			$labelStr = !empty($this->options[$key . '_label']) ? '<span class="countdown-digit-label">' . $this->options[$key . '_label'] . '</span>' : '';
			$html .= '<span class="countdown-item"><span class="countdown-digit ' . $key . '"><span class="digit-number digit-1">0</span><span class="digit-number digit-2">0</span></span>' . $labelStr . '</span>';
		}
		
		return $html;
	}
}