Komento.ready(function($){

	$('[data-migrate-jlexcomment]').on('click', function() {

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

window.migrateComment = function() {
		// Get the values from the form
		var publishState = $('[data-migrate-comment-state]').val();
		var migrateLikes = $('[data-migrate-comment-likes]').val();

		Komento.ajax('admin/views/migrators/migrateComments',
		{
			"component"	: "jlexcomment",
			"publishState": publishState,
			"migrateLikes": migrateLikes
		}).done(function(results, status) {
			$('[data-progress-status]').append(status);

			// if there is still item to migrate. keep running the migration
			if (results == true) {
				window.migrateComment();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			$('[data-migrate-jlexcomment]').removeAttr('disabled');
			$('[data-migrate-jlexcomment]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_KOMENTO_COMPLETED', true);?>');
		});
	}
});
