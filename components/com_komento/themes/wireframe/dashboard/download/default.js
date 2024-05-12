Komento.require()
.done(function($) {

	$('[data-kt-gdpr-request]').on('click', function() {
		Komento.dialog({
			content: Komento.ajax('site/views/dashboard/confirmDownload')
		});
	});
});