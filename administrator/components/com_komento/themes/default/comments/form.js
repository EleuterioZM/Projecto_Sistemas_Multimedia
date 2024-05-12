Komento.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task === 'cancel') {
			window.location = '<?php echo $cancelLink;?>';
			return;
		}

		$.Joomla('submitform', [task]);
	});
});