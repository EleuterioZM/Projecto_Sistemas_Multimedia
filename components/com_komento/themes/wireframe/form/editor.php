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

<div class="o-editor-wrapper  space-y-md">
	<div class="o-editor <?php echo $this->config->get('enable_bbcode') ? 'is-markitup' : 'is-default';?>">
		<div class="o-markitup-editor <?php echo $this->config->get('bbcode_show_buttons') ? '' : 'fd-markitup-buttons-hidden';?>" data-editor-wrapper>
			<textarea name="comment" class="o-editor__textarea kt-form-bg--0" cols="50" rows="10" placeholder="<?php echo JText::_('COM_KOMENTO_FORM_WRITE_YOUR_COMMENTS'); ?>" data-kt-editor data-preset=""><?php echo $isEdit ? $comment->comment : ''; ?></textarea>
			
		</div>
	</div>

	<?php if ($this->config->get('enable_ratings') || $this->config->get('antispam_min_length_enable') || $this->config->get('antispam_max_length_enable')) { ?>
		<div class="kt-editor-info">
			<div class="kt-editor-action">
				<?php if ($this->config->get('enable_ratings')) { ?>
				<div class="kt-editor-action__ratings px-md">
					<?php echo $this->output('site/ratings/form', [
						'score' => $isEdit ? $comment->ratings : 0
					]); ?>
				</div>
				<?php } ?>

				<?php if ($this->config->get('antispam_min_length_enable') || $this->config->get('antispam_max_length_enable')) { ?>
				<div class="px-md">
					<span>
						<span class="o-label bg-info-100 text-info-500">
							<b data-kt-text-counter>0</b>
							<?php if ($this->config->get('antispam_max_length_enable')) { ?>
								<b>&nbsp;
									/ <?php echo $this->config->get('antispam_max_length'); ?>
								</b>
							<?php } ?>
							&nbsp; <?php echo JText::_('COM_KOMENTO_FORM_CHARACTERS_COUNTER'); ?>
						</span>
					</span>
				</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<div class="kt-editor-info"
		<?php echo $this->my->canUploadAttachments() ? 'data-kt-attachments' : '';?>
		<?php echo $this->my->canShareLocation() ? 'data-kt-location' : '';?>
	>

		<div class="kt-editor-data">
			<div class="kt-editor-attachments t-hidden" data-kt-attachments-wrapper>
				<div class="px-md">
					<div class="o-attachment-list" data-kt-attachments-list>
					</div>
				</div>
			</div>

			<div class="kt-editor-data__location t-hidden" data-kt-location-form>
				<div class="kt-editor-data__location-input px-md">
					<?php echo $this->fd->html('location.wrapper', [
						'preview' => false,
						'value' => ($isEdit && $comment->hasLocation()) ? $comment->getAddress(40) : '',
						'latitude' => $isEdit ? $comment->latitude : '',
						'longitude' => $isEdit ? $comment->longitude : ''
					]); ?>
				</div>
			</div>
		</div>

		<div class="kt-editor-action p-md border-t border-solid border-gray-300 <?php echo !$this->my->canUploadAttachments() && !$this->my->canShareLocation() ? 't-hidden' : '';?>">
			<?php if ($this->my->canUploadAttachments()) { ?>
			<div class="kt-editor-action__attach" data-kt-attachments-form>
				<a href="javascript:void(0);" class="kt-editor-action__btn kt-upload-btn" data-kt-attachments-button
					data-fd-tooltip
					data-fd-tooltip-title="<?php echo JText::sprintf('COM_KOMENTO_FORM_EXTENSION_ALLOWED_LIST', implode(', ', array_map('trim', explode(',', $this->config->get('upload_allowed_extension'))))); ?>"
					data-fd-tooltip-placement="top"
				>
				<?php echo JText::_('COM_KT_UPLOAD_ATTACHMENT'); ?>
				</a>

				<div class="o-attachment-list__item t-hidden" data-kt-attachments-item data-template>
					<?php echo $this->fd->html('attachment.template', [
						'download' => false
					]); ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($this->my->canUploadAttachments() && $this->my->canShareLocation()) { ?>
				<span class="border-l t-hidden md:t-inline border-solid border-gray-300 pr-xs"></span>
			<?php } ?>

			<?php if ($this->my->canShareLocation()) { ?>
			<div class="kt-editor-action__location">
				<a href="javascript:void(0);" class="kt-editor-action__btn" data-kt-location-button>
					<?php if ($isEdit && $comment->hasLocation()) { ?>
						<?php echo $comment->getAddress(40); ?>
					<?php } else { ?>
						<?php echo JText::_('COM_KT_SHARE_LOCATION');?>
					<?php } ?>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>