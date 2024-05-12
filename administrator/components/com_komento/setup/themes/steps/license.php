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
<form action="index.php" method="post" name="installation" data-form>
	<div class="si-alert si-alert--danger d-none" data-source-errors data-api-errors>
		<p data-error-message style="margin: 10px 0 30px;" class="text-center">
			<?php echo t('INSTALLATION_METHOD_API_KEY_INVALID'); ?>
		</p>
		<div class="text-center">
			<a href="https://stackideas.com/forums" class="btn btn-danger" target="_blank"><?php echo t('INSTALLATION_CONTACT_SUPPORT');?></a>
		</div>
	</div>

	<div class="form-inline d-none" data-licenses>
		<div>
			<p><?php echo JText::_('Multiple licenses detected from your account. Please select a license to associate with this installation.');?></p>

			<div data-licenses-placeholder></div>
		</div>
	</div>

	<div class="si-container-overflow mb-4 pr-3" style="height: 5vh" data-source-method>
		<ol class="si-install-logs" data-logs>
			<?php if (SI_INSTALLER == 'launcher') { ?>
			<li class="si-install-logs__item is-loading" data-log-checklicense>
				<div class="si-install-logs__title">
					<?php echo JText::_('Checking for a valid license...');?>
				</div>

				<?php include(__DIR__ . '/log.state.php'); ?>

				<input type="hidden" name="method" value="launcher" />
			</li>
			<?php } ?>

			<?php if (SI_INSTALLER == 'full' || SI_BETA) { ?>
				<input type="hidden" name="method" value="full" />
			<?php } ?>
		</ol>
	</div>

	<input type="hidden" name="option" value="<?php echo SI_IDENTIFIER;?>" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
</form>

<script>
$(document).ready(function() {

	<?php if (SI_INSTALLER == 'full') { ?>
		$('[data-form]').submit();
	<?php } ?>

	<?php if (SI_INSTALLER == 'launcher') { ?>
	var log = $('[data-log-checklicense]');
	var submitButton = $('[data-installation-submit]');
	var form = $('[data-form]');

	submitButton.addClass('d-none');

	// Validate api key
	$.ajax({
		type: 'POST',
		url: '<?php echo JURI::root();?>administrator/index.php?option=<?php echo SI_IDENTIFIER;?>&ajax=1&controller=license&task=verify',
	}).done(function(result) {
		var sourceMethod = $('[data-source-method]');

		log.hide();

		// User is not allowed to install
		if (result.state == 400) {
			$('[data-api-errors]').removeClass('d-none');
			sourceMethod.addClass('d-none');
			return false;
		}

		// Valid licenses
		if (result.state == 200) {
			var licenses = $('[data-licenses]');
			var licensePlaceholder = $('[data-licenses-placeholder]');

			// If there is only a single license detected, just submit the form
			if (result.licenses.length === 1) {
				var license = result.licenses[0].reference;
				var licenseInput = $('<input>').attr({
					type: 'hidden',
					name: 'license',
					value: license
				});

				licensePlaceholder.append(licenseInput);
				form.submit();
				return;
			}
			
			submitButton.removeClass('d-none');

			// If there are multiple licenses, we need to request them to submit
			if (result.licenses.length > 1) {
				licenses.removeClass('d-none');

				var output = $('<div>').html(result.html);
				output.find('select')
					.css('font-size', '14px')
					.css('padding', '6px')
					.css('width', '100%');

				licensePlaceholder.append(output);


				// Change the behavior of form submission
				submitButton.on('click', function() {
					form.submit();
				});
				return;
			}

			// If the user only has 1 license, just submit the form immediately.
			licensePlaceholder.append(result.html);
			form.submit();
		}
	});
	<?php } ?>
});
</script>