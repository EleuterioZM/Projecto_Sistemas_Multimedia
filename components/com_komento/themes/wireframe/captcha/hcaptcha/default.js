Komento.require()
.script("https://hcaptcha.com/1/api.js?hl=<?php echo $this->config->get('antispam_hcaptcha_lang');?>")
.done(function($) {

	window.recaptchaCallback = function(val) {
		$('[data-kt-recaptcha-response]')
			.val(val)
			.trigger('change');
	};

	$(document).on('onReloadCaptcha', function(event) {
		setTimeout(function() {
			hcaptcha.reset();
		}, 500);
	});
});