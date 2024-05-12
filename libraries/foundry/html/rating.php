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
use Foundry\Libraries\Scripts;

class Rating extends Base
{
	/**
	 * Renders the rating item markup
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function item($options = [])
	{
		Scripts::load('shared');
		Scripts::load('raty');

		$readOnly = \FH::normalize($options, 'readOnly', true);
		$showScore = \FH::normalize($options, 'showScore', false);
		$showTotalRatings = \FH::normalize($options, 'showTotalRatings', false);
		$score = \FH::normalize($options, 'score', null);
		$totalRates = \FH::normalize($options, 'totalRates', 0);
		$reset = \FH::normalize($options, 'reset', true);
		$lockedMessage = \FH::normalize($options, 'lockedMessage', '');
		$attributes = \FH::normalize($options, 'attributes', '');

		$language = \JFactory::getLanguage();
		$isRTL = $language->isRTL();

		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		if ($score) {
			$attributes .= ' data-score="' . $score / 2 . '"';
		}

		$theme = $this->getTemplate();
		$theme->set('showTotalRatings', $showTotalRatings);
		$theme->set('showScore', $showScore);
		$theme->set('score', $score);
		$theme->set('totalRates', $totalRates);
		$theme->set('reset', $reset);
		$theme->set('readOnly', $readOnly);
		$theme->set('isRTL', $isRTL);
		$theme->set('lockedMessage', $lockedMessage);
		$theme->set('attributes', $attributes);

		$output = $theme->output('html/rating/item');

		return $output;
	}

	/**
	 * Renders the rating overall markup
	 *
	 * @since	1.1.2
	 * @access	public
	 */
	public function overall($totalRating, $totalRatingCount)
	{
		Scripts::load('shared');
		Scripts::load('raty');

		$themes = $this->getTemplate();
		$themes->set('totalRating', $totalRating);
		$themes->set('totalRatingCount', $totalRatingCount);

		$output = $themes->output('html/rating/overall');

		return $output;
	}
}