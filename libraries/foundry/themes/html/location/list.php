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
<?php if ($locations) { ?>
	<?php foreach ($locations as $item) { ?>
		<a href="javascript:void(0);" class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800" 
			data-location-suggestion
			data-location-suggestion-value="<?php echo $item->name; ?>"
			data-location-suggestion-latitude="<?php echo $item->latitude; ?>"
			data-location-suggestion-longitude="<?php echo $item->longitude; ?>"
			>
			<?php echo $item->name; ?>
		</a>
	<?php } ?>
<?php } ?>

<?php echo $this->fd->html('html.emptyList', 'MOD_SI_NO_RECORD_FOUND', [
	'icon' => 'fdi fa fa-search',
	'class' => (!$locations) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>

