<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div
	<?php if ($type === 'inline') { ?>
	id="kt"
	class="kt-frontend
	<?php echo $this->config->get('layout_appearance'); ?> si-theme-<?php echo $this->config->get('layout_accent');?>
	<?php echo $this->isMobile() ? ' is-mobile' : '';?>
	<?php echo $this->isTablet() ? ' is-tablet' : '';?>"
	<?php } else { ?>
	id="fd"
	class="kt-compact is-hide at-<?php echo $type; ?>"
	<?php } ?>
	data-kt-compact-wrapper
	data-component="<?php echo $component; ?>"
	data-cid="<?php echo $cid; ?>"
	data-type="<?php echo $type; ?>"
>
	<div
		<?php if ($type !== 'inline') { ?>
		id="kt"
		class="theme-layer
		<?php echo $this->config->get('layout_appearance'); ?> si-theme-<?php echo $this->config->get('layout_accent');?>
		<?php echo $this->isMobile() ? ' is-mobile' : '';?>
		<?php echo $this->isTablet() ? ' is-tablet' : '';?>"
		<?php } ?>
		data-kt-wrapper
		data-component="<?php echo $component;?>"
		data-cid="<?php echo $cid;?>" 
		data-url="<?php echo base64_encode(FH::getURI(true));?>"
		data-live="<?php echo $liveNotification ? 1 : 0;?>"
		data-live-interval="<?php echo $this->config->get('live_notification_interval');?>"
	>
		<?php if ($type !== 'inline') { ?>
		<div class="kt-compact-container">
			<div class="kt-compact-container__hd">
				<div class="kt-compact-container__close text-right">
					<?php echo $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-times fa-fw'), 'default', 'sm', [
						'ghost' => true,
						'iconOnly' => true,
						'attributes' => 'data-kt-compact-close'
					]); ?>
				</div>
			</div>

			<div class="kt-compact-container__loader flex-grow items-center px-md t-hidden " data-kt-loader>
				<?php echo $this->fd->html('placeholder.box', 'rounded', 10, false, [
					'width' => 'full'
				]); ?>
			</div>

			<div class="kt-compact-container__bd" data-kt-container-content></div>
		</div>
		<?php } else { ?>
		<div data-kt-loader class="t-hidden">
			<?php echo $this->fd->html('placeholder.box', 'rounded', 10, false, [
				'width' => 'full'
			]); ?>
		</div>

		<div class="space-y-md" data-kt-container-content><?php echo $comments; ?></div>
		<?php } ?>
	</div>
</div>
