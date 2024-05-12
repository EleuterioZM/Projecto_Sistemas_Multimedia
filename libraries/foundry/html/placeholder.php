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

class Placeholder extends Base
{
	/**
	 * Renders a placeholder for polls
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function polls()
	{
		static $output = null;

		if (is_null($output)) {
			$theme = $this->getTemplate();
			$output = $theme->output('html/placeholder/polls');
		}

		return $output;
	}

	/**
	 * Renders the main placeholder.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function box($shape = 'rounded', int $rows = 1, $aspectRatio = false, $args = [])
	{
		if (!in_array($shape, ['rounded', 'square'])) {
			$shape = 'rounded';
		}

		$class = 'rounded-full';

		if ($shape === 'square') {
			$class = 'rounded-md';
		}

		$ratio = \FH::normalize($args, 'ratio', false);
		$roundedAspectRatio = \FH::normalize($args, 'roundedAspectRatio', true);
		$shrinkAspectRatio = \FH::normalize($args, 'shrinkAspectRatio', true);
		$aspectRatioSize = \FH::normalize($args, 'aspectRatioSize', '64');

		$avatar = \FH::normalize($args, 'avatar', true);

		if (!in_array($aspectRatioSize, ['64', '300'])) {
			$aspectRatioSize = '64';
		}

		if ($aspectRatio && !in_array($ratio, ['1/1', '16/9', '4/3', '3/4'])) {
			$ratio = '1/1';
		}

		// standard or full
		$width = \FH::normalize($args, 'width', 'standard');
		$widthModifier = 0;

		if ($width === 'full') {
			$widthModifier = 5;
		}

		// standard width
		$widthRatio = [
			'row_1' => 6 + $widthModifier . '/12',
			'row_2' => 7 + $widthModifier . '/12',
			'row_3' => 4 + $widthModifier . '/12',
			'row_4' => 3 + $widthModifier . '/12',
		];

		$theme = $this->getTemplate();
		$theme->set('class', $class);
		$theme->set('rows', $rows);
		$theme->set('aspectRatio', $aspectRatio);
		$theme->set('ratio', $ratio);
		$theme->set('roundedAspectRatio', $roundedAspectRatio);
		$theme->set('shrinkAspectRatio', $shrinkAspectRatio);
		$theme->set('aspectRatioSize', $aspectRatioSize);
		$theme->set('avatar', $avatar);
		$theme->set('widthRatio', $widthRatio);

		$output = $theme->output('html/placeholder/box');

		return $output;
	}

	/**
	 * Renders the standard placeholder.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function standard($shape = 'rounded', int $rows = 3)
	{
		return $this->box($shape, $rows);
	}

	/**
	 * Renders the placeholder with no avatar.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function line(int $rows = 3)
	{
		// Avatar shape will be ignored when loading line.
		$shape = 'rounded';

		return $this->box($shape, $rows, false, [
			'avatar' => false
		]);
	}
}