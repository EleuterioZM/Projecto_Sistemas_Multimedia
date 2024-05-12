Komento.ready(function($) {

	$.Joomla('submitbutton', function(action) {
		
		if (action == 'purgeAll') {
			Komento.dialog({
				"content": Komento.ajax('admin/views/mailq/confirmPurgeAll'),
				"bindings": {
					"{purgeButton} click" : function() {
						Joomla.submitform([action]);
					}
				}
			});

			return false;
		}

		if (action == 'purgeSent') {
			Komento.dialog({
				content: Komento.ajax('admin/views/mailq/confirmPurgeSent'),
				bindings: {
					"{purgeButton} click": function() {
						Joomla.submitform([action]);
					}
				}
			});
			return false;
		}

		if (action == 'purgePending') {
			Komento.dialog({
				content : Komento.ajax('admin/views/mailq/confirmPurgePending'),
				bindings : {
					"{purgeButton} click" : function() {
						Joomla.submitform([action]);
					}
				}
			});
			return false;
		}

		$.Joomla('submitform', [action]);
	});


	$(document)
		.on('click.preview.mail', '[data-mailer-item-preview]', function() {
			var link = $(this);
			var id = link.data('id');

			Komento.dialog({
				"content": Komento.ajax('admin/views/mailq/preview', {'id': id})
			});
		});

});
