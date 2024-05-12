<?php if (!$browse) { ?>
Komento.ready(function($) {

	$.Joomla('submitbutton' , function(action) {
		if (action == 'requestArchiveUserData') {
			Komento.dialog({
				"content": Komento.ajax('admin/views/users/confirmArchiveUserData'),
				"bindings": {
					"{submitButton} click" : function() {
						Joomla.submitform([action]);
					}
				}
			});

			return false;
		}

		$.Joomla('submitform', [action]);
	});

});

<?php } ?>