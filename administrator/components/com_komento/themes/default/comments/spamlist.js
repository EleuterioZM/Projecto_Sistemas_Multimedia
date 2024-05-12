Komento.require()
.done(function($) {

	$('[data-action=spam]').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();

		if (document.adminForm.boxchecked.value == 0) {
		    alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST', true)?>');
		    return;
		}

		document.adminForm.action.value = 'spam';
		$.Joomla('submitform', ['trainAkismet']);
	});

	$('[data-action=ham]').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();

		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST', true)?>');
			return;
		}

		document.adminForm.action.value = 'ham';
		$.Joomla('submitform', ['trainAkismet']);
	});
});
