
Komento.ready(function($) {

	$('[data-mailer-list]').implement(Komento.Controller.Mailer)

	// Handle submit button.
	$.Joomla('submitbutton' , function(action) {
		var selected = [];

		if (action == 'reset') {
			$('[data-fd-grid]').find('input[name=cid\\[\\]]:checked').each(function(i , el ) {
				selected.push($(el).val());
			});

			Komento.dialog({
				"content": Komento.ajax('admin/views/mailq/confirmReset', {"files": selected})
			});

			return false;
		}

		$.Joomla('submitform', [action]);
	});

	$(document).on('click.preview', '[data-mail-preview]', function(event) {
		event.preventDefault();
		event.stopPropagation();

		var button = $(this);
		var file = button.data('mail-preview');

		Komento.dialog({
			"content": Komento.ajax('admin/views/mailq/templatePreview', {"file": file})
		});
	});
});
