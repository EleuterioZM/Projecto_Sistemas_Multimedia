<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
namespace Foundry\Html;

defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Html\Base;
use Foundry\Libraries\Stylesheets;
use Foundry\Libraries\Scripts;

class Filter extends Base
{
	public function __construct($fd)
	{
		parent::__construct($fd);

		// We need the following dependencies from js
		Scripts::load('admin');
		Scripts::load('select2');
	}

	/**
	 * Allow caller to pass in html codes to be added into the filter bar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function custom($html)
	{
		Stylesheets::load('select2');

		$theme = $this->getTemplate();

		$theme->set('html', $html);
		$output = $theme->output('html/filter/custom');

		return $output;
	}

	/**
	 * Renders a dropdown to filter number of items per page
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function limit($selected = 5, $name = 'limit', $stepping = 5, $min = 5, $max = 100, $showAll = true, $options = [])
	{
		Stylesheets::load('select2');

		$theme = $this->getTemplate();

		$items = [];

		for ($i = $stepping; $i <= $max; $i = $i + $stepping) {
			$items[$i] = $i;
		}

		if ($showAll) {
			$items['all'] = \JText::_(\FH::normalize($options, 'showAllText', 'JALL'));
		}


		$theme->set('items', $items);
		$theme->set('selected', $selected);
		$theme->set('name', $name);
		$theme->set('showAll', $showAll);

		$contents = $theme->output('html/filter/limit');

		return $contents;
	}

	/**
	 * Renders a generic dropdown for filters
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function lists($name, $items = [], $selected = 'all', $options = [])
	{
		Stylesheets::load('select2');

		$initial = \FH::normalize($options, 'initial', '');
		$initialValue = \FH::normalize($options, 'initialValue', 'all');
		$minWidth = \FH::normalize($options, 'minWidth', false);
		$identicalMatch = \FH::normalize($options, 'identicalMatch', false); 

		$theme = $this->getTemplate();

		$theme->set('identicalMatch', $identicalMatch);
		$theme->set('minWidth', $minWidth);
		$theme->set('initialValue', $initialValue);
		$theme->set('initial', $initial);
		$theme->set('name', $name);
		$theme->set('items', $items);
		$theme->set('selected', $selected);

		$contents = $theme->output('html/filter/lists');

		return $contents;
	}

	/**
	 * Renders the published dropdown list on the filter bar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function published($name = 'state', $selected = 'all', $options = [])
	{
		Stylesheets::load('select2');

		$selectText = \FH::normalize($options, 'selectText', 'JSELECT');
		$publishedText = \FH::normalize($options, 'publishedText', 'JPUBLISHED');
		$unpublishedText = \FH::normalize($options, 'unpublishedText', 'JUNPUBLISHED');
		$valueType = \FH::normalize($options, 'valueType', 'letter');

		$publishedValue = 'P';
		$unpublishedValue = 'U';

		if ($valueType === 'numeric') {
			$publishedValue = '1';
			$unpublishedValue = '0';
		}

		$theme = $this->getTemplate();
		$theme->set('selectText', $selectText);
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('publishedText', $publishedText);
		$theme->set('unpublishedText', $unpublishedText);
		$theme->set('publishedValue', $publishedValue);
		$theme->set('unpublishedValue', $unpublishedValue);

		$contents = $theme->output('html/filter/published');

		return $contents;
	}

	/**
	 * Renders a search box in the filter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function search($value = '', $name = 'search', $options = [])
	{
		$tooltip = \FH::normalize($options, 'tooltip', '');
		$placeholder = \FH::normalize($options, 'placeholder', 'JSEARCH_FILTER');

		$theme = $this->getTemplate();
		$theme->set('placeholder', $placeholder);
		$theme->set('tooltip', $tooltip);
		$theme->set('value', $value);
		$theme->set('name', $name);

		$contents = $theme->output('html/filter/search');

		return $contents;
	}

	/**
	 * Renders a space on the filters bar
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function spacer()
	{
		$theme = $this->getTemplate();

		$contents = $theme->output('html/filter/spacer');

		return $contents;
	}

	/**
	 * Renders a date range filter
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function dateRange($selected = '', $name = 'dateRange', $placeholder = '', $options = [])
	{
		// Import the stylesheet
		Stylesheets::load('daterangepicker');

		if (!$placeholder) {
			$placeholder = 'FD_SELECT_DATE_RANGE';
		}

		$placeholder = \JText::_($placeholder);

		// Get today
		$start = false;
		$end = false;

		if ($selected && is_array($selected)) {
			$start = $selected['start'];
			$end = $selected['end'];
		}

		$class = \FH::normalize($options, 'class', '');
		$appearance = \FH::normalize($options, 'appearance', 'light');
		$accent = \FH::normalize($options, 'accent', 'foundry');
		$submitonclick = \FH::normalize($options, 'submitonclick', true);

		$uid = uniqid();

		$theme = $this->getTemplate();
		$theme->set('uid', $uid);
		$theme->set('start', $start);
		$theme->set('end', $end);
		$theme->set('name', $name);
		$theme->set('class', $class);
		$theme->set('appearance', $appearance);
		$theme->set('accent', $accent);
		$theme->set('placeholder', $placeholder);
		$theme->set('selected', $selected);
		$theme->set('submitonclick', $submitonclick);

		$output = $theme->output('html/filter/daterange');

		return $output;
	}
}