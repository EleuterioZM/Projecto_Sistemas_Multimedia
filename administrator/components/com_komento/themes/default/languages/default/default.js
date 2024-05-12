
Komento
.require()
.done(function($){

	$.Joomla('submitbutton' , function(task) {

		if (task == 'discover') {
			window.location 	= 'index.php?option=com_komento&view=languages&layout=discover';
			return false;
		}

		if (task == 'uninstall') {
			var selected = [];

			$('[data-kt-table]').find('input[name=cid\\[\\]]:checked').each(function(i , el ){
				selected.push($(el).val());
			});

			Komento.dialog({
				content: Komento.ajax('admin/views/languages/confirmDelete', {
					"cid": selected
				})
			});

			return;
		}

		$.Joomla('submitform', [task]);

	});
});