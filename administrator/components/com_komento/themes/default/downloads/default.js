
Komento.ready(function($) {

	// $('[data-mailer-list]').implement(Komento.Controller.Mailer)

	// Handle submit button.
	$.Joomla('submitbutton' , function(action) {
		
		if (action == 'purgeAll') {
			Komento.dialog({
				"content": Komento.ajax('admin/views/downloads/confirmPurgeAll'),
				"bindings": {
					"{purgeButton} click" : function() {
						Joomla.submitform([action]);
					}
				}
			});

			return false;
		}

		if (action == 'removeRequest') {
			Komento.dialog({
				"content": Komento.ajax('admin/views/downloads/removeRequest'),
				"bindings": {
					"{removeButton} click" : function() {
						Joomla.submitform([action]);
					}
				}
			});

			return false;
		}

		$.Joomla('submitform', [action]);
	});

});
