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

<!-- start digest.item -->
<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480">
	<tr>
		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->

<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">

	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
		<tbody>
			<tr>
				<td style="direction:ltr;font-size:0px;padding:20px 10px 0px;text-align:center;">
					<!--[if mso | IE]>
					<table role="presentation" border="0" cellpadding="0" cellspacing="0">
						<tr>
						<?php if ($icon) {?><td class="" style="vertical-align:top;width:69px;"><?php } ?>
					<![endif]-->

					<?php if ($icon) { ?>
					<div class="mj-column-per-15 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">

						<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
							<tbody>
								<tr>
									<td style="vertical-align:top;padding:0;">
										<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
											<tr>
												<td align="center" style="font-size:0px;padding:0;word-break:break-word;">
													<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
														<tbody>
															<tr>
																<td style="width:36px;">
	  																<img height="auto" src="<?php echo $icon; ?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="36"></img>
	  															</td>
															</tr>
														</tbody>
													</table>
	
												</td>
											</tr>
	  									</table>
	  								</td>
		  						</tr>
							</tbody>
	  					</table>
	
	  				</div>
	  				<?php } ?>
	
					<!--[if mso | IE]>
						<?php if ($icon) {?></td><?php } ?>
						<td class="" style="vertical-align:top;width:391px;">
					<![endif]-->
			
					<div class="mj-column-per-<?php echo $icon ? '85' : '100'; ?> mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
		
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
							<tbody>
								<tr>
									<td style="vertical-align:top;padding:0;">

	  									<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
		
											<tr>
			  									<td align="left" style="font-size:0px;padding:0;padding-top:4px;padding-bottom:4px;word-break:break-word;">
				
													<div style="font-family:'Inter', Arial, sans-serif;font-size:16px;line-height:24px;text-align:left;color:#444444;">
														<?php echo $title; ?>
													</div>
	
			  									</td>
											</tr>
		  
											<tr>
			  									<td align="left" style="font-size:0px;padding:0;padding-top:10px;padding-bottom:4px;word-break:break-word;">
				
	  												<div style="font-family:'Inter', Arial, sans-serif;font-size:14px;line-height:20px;text-align:left;color:#222222;">
	  													<?php echo $content; ?>
	  												</div>
	
			  									</td>
											</tr>
		  
	  									</table>
	
									</td>
								</tr>
							</tbody>
						</table>
	
	  				</div>
	
					<!--[if mso | IE]>
							</td>
							</tr>
						</table>
					<![endif]-->
				</td>
			</tr>
		</tbody>
	</table>

</div>


<!--[if mso | IE]>
		</td>
	</tr>
</table>
<![endif]-->

<?php if ($divider) { ?>
<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480" >
	<tr>
		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->


	<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
		
		<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
			<tbody>
				<tr>
					<td style="direction:ltr;font-size:0px;padding:10px 10px;text-align:center;">
						<!--[if mso | IE]>
						<table role="presentation" border="0" cellpadding="0" cellspacing="0">
							<tr>
							<td class="" style="vertical-align:top;width:460px;">
						<![endif]-->

						<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
							<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
								<tbody>
									<tr>
										<td style="vertical-align:top;padding:0;">
											<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
												<tr>
													<td style="font-size:0px;padding:0;word-break:break-word;">
														<p style="border-top:solid 1px #ebebeb;font-size:1px;margin:0px auto;width:100%;"></p>

														<!--[if mso | IE]>
														<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 1px #ebebeb;font-size:1px;margin:0px auto;width:460px;" role="presentation" width="460px">
															<tr>
																<td style="height:0;line-height:0;">&nbsp;</td>
															</tr>
														</table>
														<![endif]-->
	
	
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
	
						<!--[if mso | IE]>
							</td>
							</tr>
						</table>
						<![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
		
	</div>

<!--[if mso | IE]>
		</td>
	</tr>
</table>
<![endif]-->
<?php } ?>

<!-- end digest.item -->