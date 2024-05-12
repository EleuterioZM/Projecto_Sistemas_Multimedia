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
<div class="t-hidden" data-fd-popover-wrapper>
	<div id="fd" data-fd-popover-block data-appearance="<?php echo $appearance;?>">
		<div class="<?php echo $appearance;?> si-theme-<?php echo $accent;?>">
			<div class="o-popover">
				<div class="space-y-xs">
					<div class="font-bold t-hidden" data-fd-popover-block-title></div>
					<div data-fd-popover-block-content></div>
				</div>
			</div>
		</div>
	</div>
</div>