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
<?php echo $this->fd->html('email.heading', 'COM_KT_EMAILS_MODERATE_HEADING', 'COM_KT_EMAILS_MODERATE_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', 'COM_KT_EMAILS_MODERATE_CONTENT', 'clear'); ?>

<?php echo $this->fd->html('email.comment',
	$templatePreview ? $lipsum : $commentContent,
	$templatePreview ? '13th August 2021' : $commentDate,
	$templatePreview ? 'John Doe' : ucfirst($commentAuthorName)
); ?>

<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#f9f9fa;background-color:#f9f9fa;margin:0px auto;max-width:480px;">	
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9fa;background-color:#f9f9fa;width:100%;">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:20px;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:440px;">
			<![endif]-->
			
			<?php if ($templatePreview) { ?>
				<?php for ($i = 1; $i < 5; $i++) { ?>
					<?php echo $this->fd->html('email.attachment', 'javascript:void(0);', 'Sample attachment ' . $i); ?>
				<?php } ?>
			<?php } ?>

			<?php if (isset($attachments) && $attachments) { ?>
				<?php foreach ($attachments as $attachment) { ?>
					<?php echo $this->fd->html('email.attachment', $attachment->link, $attachment->filename); ?>
				<?php } ?>
			<?php } ?>
				
			<!--[if mso | IE]>
			</td></tr></table>
			<![endif]-->
		</td>
	</tr>
	</tbody>
	</table>	
</div>
<!--[if mso | IE]>
</td></tr></table>
<![endif]-->

<?php echo $this->fd->html('email.spacer'); ?>

<!-- [if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
	<table role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:0;padding-bottom:0px;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:235px;">
			<![endif]-->
			<div class="mj-column-px-235 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<?php echo $this->fd->html('email.button', 'COM_KT_NOTIFICATION_REJECT_COMMENT', $templatePreview ? 'javascript:void(0);' : $rejectLink, 'danger'); ?>
			</div>
			<!--[if mso | IE]>
			</td>
			<td style="vertical-align:top;width:235px;">
			<![endif]-->
			<div class="mj-column-px-235 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<?php echo $this->fd->html('email.button', 'COM_KOMENTO_NOTIFICATION_APPROVE_COMMENT', $templatePreview ? 'javascript:void(0);' : $approveLink, 'primary'); ?>
			</div>
			<!--[if mso | IE]>
			</td></tr></table>
			<![endif]-->
		</td>
	</tr>
	</tbody>
	</table>
</div>

<!--[if mso | IE]>
</td></tr></table>
<![endif]-->
