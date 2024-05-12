<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

use Joomla\CMS\Language\Text;
?>
<div class="tf-template-library-item-info">
	<div class="item-description"></div>
	<div class="template-details">
		<div class="items tf-library-info-grid cols-3">
			<div class="tf-cell category">
				<div class="title is-grey"><?php echo $category_label; ?>:</div>
				<div class="content"></div>
			</div>
			<div class="tf-cell solution">
				<div class="title is-grey"><?php echo Text::_('NR_SOLUTION'); ?>:</div>
				<div class="content"></div>
			</div>
			<div class="tf-cell goal">
				<div class="title is-grey"><?php echo Text::_('NR_GOAL'); ?>:</div>
				<div class="content"></div>
			</div>
		</div>
	</div>
	<div class="template-details compatibility-details">
		<div class="details-header-items">
			<div class="tf-library-info-grid cols-3">
				<div class="tf-cell"><?php echo Text::_('NR_REQUIREMENTS'); ?></div>
				<div class="tf-cell"><?php echo Text::_('NR_DETECTED'); ?></div>
				<div class="tf-cell"><?php echo Text::_('NR_CHECK'); ?></div>
			</div>
		</div>
		<div class="dependency-items"></div>
		<div class="template dependency-item tf-library-info-grid cols-3">
			<div class="tf-cell requirement"></div>
			<div class="tf-cell detected"></div>
			<div class="tf-cell value">
				<svg class="checkmark is-hidden" width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 8.65556L6.89552 14.5L19 1.5" stroke="#82DE78" stroke-width="2" stroke-linecap="round"/></svg>
			</div>
		</div>
	</div>
</div>