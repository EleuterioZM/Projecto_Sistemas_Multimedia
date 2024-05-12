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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="flex items-center gap-xs flex-wrap" data-fd-rating-wrapper>
	<div class="flex-shrink-0">
		<div class="fd-rating"
			data-fd-rating
			data-read-only="<?php echo $readOnly ? 1 : 0; ?>"
			data-extension="<?php echo $this->fd->getName(); ?>" 
			data-rtl="<?php echo $isRTL ? 1 : 0; ?>"

			<?php if ($readOnly && $lockedMessage) { ?>
			data-fd-tooltip
			data-fd-tooltip-title="<?php echo JText::_($lockedMessage); ?>"
			data-fd-tooltip-placement="top"
			<?php } ?>

			<?php echo $attributes; ?>
		>
		</div>
	</div>
	<div class="flex-grow-1 min-w-0">

		<div class="text-xs">
			<?php if ($totalRates === 0 && $showTotalRatings) { ?>
			<span class="text-gray-500">
				<?php echo JText::_('FD_RATING_NO_RATINGS_YET'); ?>
			</span>
			<?php } ?>
	
			<?php if ($score && $showScore) { ?>
			<span class="font-bold">
				<?php echo JText::sprintf('FD_RATING_RATED_MESSAGE', $score / 2, 5);?>
			</span>
			<?php } ?>
	
			<?php if ($totalRates && $showTotalRatings) { ?>
			<span>
				&middot;
			</span>
			<span class="text-gray-500">
				<?php echo JText::sprintf('FD_RATING_TOTAL_RATINGS', FH::toCurrencyFormat($totalRates));?>
			</span>
			<?php } ?>
		</div>
	
		<?php if (!$readOnly && $reset) { ?>
		<div class="leading-xs">
			<span class="border-l border-solid border-gray-300 pr-xs"></span>
			<a href="javascript:void(0);" class="no-underline text-xs" data-fd-rating-reset>
				<?php echo JText::_('FD_RATING_ACTION_RESET');?>
			</a>
		</div>
		<?php } ?>
	
		<?php if (!$readOnly) { ?>
		<input type="hidden" name="ratings" data-fd-rating-input />
		<?php } ?>
	</div>
</div>