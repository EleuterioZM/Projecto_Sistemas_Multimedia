<?php
/**
* @package      Foundry
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-order-group">
	

	<div class="app-order-group__item">
		<?php echo $this->fd->html('form.text', $name, $key + 1, null, ['class' => 'order-value']); ?>
	</div>

	<div class="app-order-group__item">
		<?php if ($saveOrder) { ?>
			<span class="order-up">
				<?php echo $pagination->orderUpIcon($rowIndex, $showOrderUpIcon, $orderUpTask, 'Move Up', $accessControl); ?>
			</span>
			<span class="order-down">
				<?php echo $pagination->orderDownIcon($rowIndex, $total, $showOrderDownIcon, $orderDownTask, 'Move Down', $accessControl); ?>
			</span>
		<?php } ?>
	</div>
</div>