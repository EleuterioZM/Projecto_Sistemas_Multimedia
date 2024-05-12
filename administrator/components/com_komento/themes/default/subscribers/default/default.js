
Komento.ready(function($) {
	$.Joomla('submitbutton', function(action) {

		if (action == 'add') {
			window.location.href = '<?php echo rtrim(JURI::root() , '/');?>/administrator/index.php?option=com_komento&view=subscribers&layout=form';
			return;
		}

		$.Joomla('submitform', [action]);
	});
});