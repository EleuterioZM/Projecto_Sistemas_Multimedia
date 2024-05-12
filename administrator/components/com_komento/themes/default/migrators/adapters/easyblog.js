Komento.ready(function($){

	$('[data-migrate-easyblog]').on('click', function() {

		// Disable the button from being clicked twice
		$(this).attr('disabled', "true");

		// Update the buttons message
		$(this).html('<i class="fdi fa fa-cog fa-spin"></i> <?php echo JText::_('COM_KOMENTO_MIGRATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		// clear the stats.
		$('[data-progress-stat]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		window.migrateComment();
	});

	$('[data-easyblog-category]').on('change', function() {
		$('[data-migrate-easyblog]').removeAttr('disabled');
		$('[data-migrate-easyblog]').html('<?php echo JText::_('COM_KOMENTO_MIGRATORS_RUN_NOW');?>');
	});

window.migrateComment = function() {
		// Get the values from the form
		var publishState = $('[data-migrate-comment-state]').val();
		var migrateLikes = $('[data-migrate-comment-likes]').val();
		var categoryId = $('[data-easyblog-category]').val();

		Komento.ajax('admin/views/migrators/migrateComments',
		{
			"component"	: "com_easyblog",
			"publishState": publishState,
			"migrateLikes": migrateLikes,
			"categoryId": categoryId
		}).done(function(results, status) {
			$('[data-progress-status]').append(status);

			// if there is still item to migrate. keep running the migration
			if (results == true) {
				window.migrateComment();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			$('[data-migrate-easyblog]').removeAttr('disabled');
			$('[data-migrate-easyblog]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_KOMENTO_COMPLETED', true);?>');
		});
	}
});
