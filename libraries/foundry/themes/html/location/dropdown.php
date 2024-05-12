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
<div class="o-dropdown">
	<?php if ($title) { ?>
		<div class="o-dropdown__hd px-md py-sm">
			<span class="border border-solid border-gray-300 rounded-md px-2xs py-2xs"><i class="fdi far fa-compass fa-fw"></i></span> <b><?php echo JText::_('FD_LOCATION_DROPDOWN_TITLE'); ?></b>
		</div>
	<?php } ?>

	<?php if ($preview) { ?>
	<div class="o-dropdown__bd">
		<div class="flex">
			<div class="o-aspect-ratio overflow-hidden" style="--aspect-ratio: 16/6;">
				<img src="" alt="" class="">
				<div class="o-aspect-ratio__loader">
					<div class="o-loader block"></div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	
	<?php if ($footer) { ?>
	<div class="o-dropdown__ft px-sm py-xs">
		<div class="o-location p-no border-transparent">
			<div class="flex-grow">
				<?php echo $this->fd->html('form.text', 'address', $value, 'address', [
					'attributes' => 'autocomplete="off" data-location-address',
					'placeholder' => JText::_('FD_LOCATION_PLACEHOLDER'),
					'baseClass' => 'o-location__field'
				]); ?>
			</div>

			<div class="flex-shrink-0 space-x-xs pl-xs">
				<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-times'), 'danger', 'default', [
					'attributes' => 'data-location-remove',
					'ghost' => true,
					'class' => $value ? '' : 't-hidden'
				]); ?>

				<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-compass'), 'default', 'default', [
					'attributes' => 'data-location-detect data-fd-tooltip data-fd-tooltip-title="' . JText::_('FD_LOCATION_DETECT') . '" data-placement="top"',
					'outline' => true
				]); ?>
			</div>
		</div>
	</div>
	<?php } ?>

	<input type="hidden" name="latitude" data-location-lat value="<?php echo $latitude; ?>"/>
	<input type="hidden" name="longitude" data-location-lng value="<?php echo $longitude; ?>"/>
</div>