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
<?php if ($giphies) { ?>
	<div>
		<div class="grid grid-cols-5 gap-xs">
		<?php foreach ($giphies as $giphy) { ?>
			<div class="o-aspect-ratio o-aspect-ratio--contain rounded-md overflow-hidden" style="--aspect-ratio: 1/1;">
				<a href="javascript:void(0);" 
					class="fd-giphy-item-holder bg-gray-50"
					data-type="<?php echo $type; ?>"
					data-fd-giphy-item
					data-original="<?php echo $giphy->images->original->url; ?>"
					style="background-image: url('<?php echo $giphy->images->fixed_width->url; ?>');"
					>
				</a>
			</div>
		<?php } ?>
		</div>
	</div>
<?php } ?>