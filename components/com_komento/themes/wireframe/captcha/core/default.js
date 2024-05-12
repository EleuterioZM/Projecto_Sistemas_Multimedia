Komento.ready(function($) {

$(document).on('onReloadCaptcha', function(event) {
	var captchaReload = $('[data-kt-captcha-reload]');

	captchaReload.addClass('is-loading');

	var captchaId = $('[data-kt-captcha-id]');

	// Standard built in captcha
	Komento.ajax('site/views/captcha/reload', {
		"id": captchaId.val()
	}).done(function(data) {

		captchaReload.removeClass('is-loading');
		
		var image = $('[data-kt-captcha-image]');
		var response = $('[data-kt-captcha-response]');
		var reload = $('[data-kt-captcha-reload]');

		image.attr('src', data.image);
		captchaId.val(data.id);
		response.val('');
	});

});

});