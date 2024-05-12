Komento.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task === 'cancel') {
			window.location = '<?php echo rtrim(JURI::root(), '/');?>/administrator/index.php?option=com_komento&view=acl';
			return;
		}

		$.Joomla('submitform', [task]);
	});

	jQuery('[data-acl-toggle]').on('change', function() {
		var input = $(this);
		var parent = input.parents('[data-acl-wrap]');
		var enabled = input.val() == 1;
		var data = enabled ? 'on' : 'off';

		parent.find('[data-info]')
			.addClass('t-hidden');

		parent.find('[data-' + data + ']')
			.removeClass('t-hidden');

	});
});