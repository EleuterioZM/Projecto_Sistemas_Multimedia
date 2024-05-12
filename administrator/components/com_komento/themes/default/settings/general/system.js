Komento.ready(function($) {

	$('[data-secure-cron]').on('change', function() {
		var input = $(this);
		var secureCronSettings = $('[data-secure-cron-settings]');

		if (input.val() == 1) {
			secureCronSettings.removeClass('t-hidden');
			return;
		}
		secureCronSettings.addClass('t-hidden');
	});
});