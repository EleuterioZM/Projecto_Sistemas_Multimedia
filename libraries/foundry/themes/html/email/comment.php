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
<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#f9f9fa;background-color:#f9f9fa;margin:0px auto;max-width:480px;">
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9fa;background-color:#f9f9fa;width:100%;">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:16px 20px;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:440px;">
			<![endif]-->

			<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
				<tbody>
				<tr>
					<td style="vertical-align:top;padding:0;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
						<?php if ($author) { ?>
						<tr>
							<td align="left" style="font-size:0px;padding:0;padding-top:10px;padding-bottom:10px;word-break:break-word;">
								<div style="font-family:'Roboto', Arial, sans-serif;font-size:18px;line-height:22px;text-align:left;">
									<a href="<?php echo $author->link;?>" style="color: #4E92DF; text-decoration: none;"><?php echo $author->name;?></a>
								</div>
							</td>
						</tr>
						<?php } ?>

						<?php if ($date) { ?>
						<tr>
							<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
								<div style="font-family:'Roboto', Arial, sans-serif;font-size:14px;line-height:22px;text-align:left;color:#888888;"><?php echo $date;?></div>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td align="left" style="font-size:0px;padding:0;word-break:break-word;padding-bottom: 20px;">&nbsp;
							</td>
						</tr>
						<tr>
							<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
								<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;line-height:22px;text-align:left;color:#888888;"><?php echo $comment;?></div>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
			</table>

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
