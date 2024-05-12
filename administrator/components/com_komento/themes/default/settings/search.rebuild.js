Komento.ready(function() {

	$ = jQuery;

	var pages = <?php echo json_encode($items); ?>;

	$(document).trigger('fd.search.rebuild', [pages, '<?php echo JURI::root();?>/administrator/index.php?option=com_komento&view=settings']);

	$(document).on('fd.search.rebuildCompleted', function(event, jsonString) {
		// Finalize and send the data to the server
		Komento.ajax('admin/views/settings/rebuildSearch', {
			"dataString": jsonString
		}).done(function() {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_komento&view=settings';
		});
	});
});
