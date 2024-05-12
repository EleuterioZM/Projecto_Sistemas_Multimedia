Komento.ready(function($) {

	$('select[name=antispam_captcha_type]').on('change', function() {
		var value = $(this).val();
		var panels = $('[data-panel-captcha]');
		var panel = $('[data-panel-captcha=' + value + ']');

		panels.addClass('t-hidden');
		
		if (panel.length > 0) {
			panel.removeClass('t-hidden');

			return;	
		}

		return;
	});
});