
Komento.ready(function($) {

	Komento.ajax('admin/views/languages/getLanguages', {})
		.done(function() {
			window.location = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_komento&view=languages';
		})
		.fail(function(error) {
			$('[data-languages-wrapper]').addClass('has-error');
			$('[data-languages-error]').html(error);

			return;
		});

});