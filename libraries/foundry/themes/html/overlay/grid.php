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
<div class="app-content-overlay grid">
	<div class="app-content-overlay__body">
		<div class="bg-white rounded-md p-2xl mt-2xl flex items-center flex-col gap-md">

			<div class="o-btn o-btn--primary-o">
				<i class="fdi fa fa-star text-danger-500 mr-xs"></i> <?php echo JText::_($buttonText); ?>
			</div>
			<div class=""><?php echo $description; ?></div>
		</div>
	</div>
</div>
