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

$pageIdxToShow = [];

if ($responsive->isMobile()) {

	$activeIdx = 1;
	$lastIdx = 10;

	foreach ($data->pages as $idx => $page) {
		if ($page->active) {
			$activeIdx = $idx;
		}
		$lastIdx = $idx;
	}

	// special handling when view with mobile
	$pageIdxToShow[] = $activeIdx;
	$totalPages = count($data->pages);

	// when render in mobile, max pagination elements to be displayed are 7.
	if ($totalPages >= 2) {
		if ($activeIdx == 1) {
			$pageIdxToShow[] = $activeIdx + 1; // 2nd index
			$pageIdxToShow[] = $activeIdx + 2; // 3rd index
		}

		if ($activeIdx > 1 && $activeIdx != $lastIdx) {
			$pageIdxToShow[] = $activeIdx - 1;
			$pageIdxToShow[] = $activeIdx + 1;
		}

		if ($activeIdx > 1 && $activeIdx == $lastIdx) {
			$pageIdxToShow[] = $activeIdx - 1; // 2nd last index
			$pageIdxToShow[] = $activeIdx - 2; // 3rd last index
		}

		$pageIdxToShow = array_unique($pageIdxToShow);
	}
}

?>
<div class="relative z-0 inline-flex">
	<?php if ($data->start) { ?>
		<?php echo $this->output('pagination/footer/link', [
			'item' => $data->start, 
			'icon' => 'fa-angle-double-left', 
			'class' => 'rounded-l-md px-xs',
			'isLink' => $isLink,
			'srtext' => JText::_('FD_PAGINATION_FIRST_PAGE')			
		]); ?>
	<?php } ?>

	<?php if ($data->previous) { ?>
		<?php echo $this->output('pagination/footer/link', [
			'item' => $data->previous, 
			'icon' => 'fa-angle-left', 
			'class' => '-ml-px px-xs',
			'isLink' => $isLink,
			'srtext' => JText::_('FD_PAGINATION_PREVIOUS_PAGE')			
		]); ?>
	<?php } ?>

	<?php foreach ($data->pages as $idx => $page) { ?>

		<?php 
			if ($responsive->isMobile() && $pageIdxToShow && !in_array($idx, $pageIdxToShow)) { 
				continue; 
			}
		?>

		<?php echo $this->output('pagination/footer/link', [
			'item' => $page, 
			'icon' => '', 
			'class' => '-ml-px px-sm',
			'isLink' => $isLink,
			'srtext' => ''			
		]); ?>
	<?php } ?>

	<?php if ($data->next) { ?>
		<?php echo $this->output('pagination/footer/link', [
			'item' => $data->next, 
			'icon' => 'fa-angle-right', 
			'class' => '-ml-px px-xs',
			'isLink' => $isLink,
			'srtext' => JText::_('FD_PAGINATION_NEXT_PAGE')			
		]); ?>
	<?php } ?>

	<?php if ($data->end) { ?>
		<?php echo $this->output('pagination/footer/link', [
			'item' => $data->end, 
			'icon' => 'fa-angle-double-right', 
			'class' => '-ml-px rounded-r-md px-xs',
			'isLink' => $isLink,
			'srtext' => JText::_('FD_PAGINATION_LAST_PAGE')			
		]); ?>
	<?php } ?>
</div>