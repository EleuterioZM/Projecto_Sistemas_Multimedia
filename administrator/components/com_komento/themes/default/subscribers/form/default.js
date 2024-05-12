Komento.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'cancel') {
			window.location.href = '<?php echo rtrim(JURI::root() , '/');?>/administrator/index.php?option=com_komento&view=subscribers';
			return;
		}

		$.Joomla('submitform', [task]);
	});
});