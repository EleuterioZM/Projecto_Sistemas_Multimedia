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
<?php if ($this->my->allow('download_attachment')) { ?>
	<?php if ($attachments) { ?>
	<div class="mt-md">
		<?php if ($this->config->get('defer_attachments')) { ?>
		<div class="kt-editor-attachments__title text-gray-500">
			<a href="javascript:void(0);" class="kt-attachment-link" data-kt-attachment-viewlink>
				<?php echo JText::_('COM_KT_ATTACHMENT_VIEWLINK'); ?>
			</a>

			(<span class="fileCounter"><?php echo count($attachments); ?></span> / <?php echo $this->config->get('upload_max_file'); ?>)
		</div>

		<div data-kt-attachment-item-preview data-id="<?php echo $comment->id; ?>">
			<div class="o-loader o-loader--sm o-loader--inline"></div>
		</div>
		<?php } else { ?>
			<?php echo $this->output('site/comments/attachments.list', ['attachments' => $attachments]); ?>
		<?php } ?>
	</div>
	<?php } ?>
<?php } else { ?>
	<?php echo $this->fd->html('html.well', JText::_('COM_KOMENTO_COMMENT_ATTACHMENTS_NO_PERMISSION_TO_VIEW'), [
		'class' => 'bg-white mt-sm'
	]); ?>
<?php } ?>
