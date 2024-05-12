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
<div class="flex flex-col space-y-sm">

	<?php if ($preview) { ?>
	<div class="o-aspect-ratio overflow-hidden rounded-md" style="--aspect-ratio: 16/5;">
		<img src="" alt="" class="">
		<div class="o-aspect-ratio__loader">
			<div class="o-loader block"></div>
		</div>
	</div>
	<?php } ?>

	<div class="o-location">
		<div class="flex-grow">
			<?php echo $this->fd->html('form.text', 'address', $value, 'address', [
				'attributes' => 'autocomplete="off" data-location-address',
				'placeholder' => JText::_('FD_LOCATION_PLACEHOLDER'),
				'baseClass' => 'o-location__field'
			]); ?>

			<div class="t-hidden" data-fd-location-dropdown>
				<div id="fd">
					<div class="">
						<div class="o-dropdown divide-y divide-gray-200 w-full" data-fd-dropdown-wrapper>
							<div class="o-dropdown__hd px-md py-md">
								<div class="font-bold text-sm text-gray-800"><?php echo JText::_('FD_LOCATION_SUGGESTED_LOCATIONS'); ?></div>
							</div>
							<div class="o-dropdown__bd xpy-sm xpx-xs overflow-y-auto max-h-[380px] divide-y divide-gray-200 space-y-smx" data-fd-dropdown-body>
								<div class="px-sm py-sm hover:no-underline text-gray-800">
									<?php echo $this->fd->html('placeholder.line', 1); ?>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="flex-shrink-0 space-x-xs">
			<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-times'), 'danger', 'default', [
				'attributes' => 'data-location-remove',
				'ghost' => true,
				'class' => $value ? '' : 't-hidden'
			]); ?>

			<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-compass'), 'default', 'default', [
				'attributes' => 'data-location-detect data-fd-tooltip data-fd-tooltip-title="' . JText::_('FD_LOCATION_DETECT') . '" data-fd-tooltip-placement="top"',
				'outline' => true,
				'class' => 't-hidden'
			]); ?>

		</div>
		
	</div>

	<input type="hidden" name="latitude" data-location-lat value="<?php echo $latitude; ?>"/>
	<input type="hidden" name="longitude" data-location-lng value="<?php echo $longitude; ?>"/>
</div>