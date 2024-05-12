Komento.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location = '<?php echo rtrim(JURI::root(), '/');?>/administrator/index.php?option=com_komento&view=settings';
			return;
		}

		$.Joomla('submitform', [task]);
	});

});