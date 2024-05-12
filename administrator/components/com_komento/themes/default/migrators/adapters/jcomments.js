Komento.ready(function($){
	var running = false;

	$('[data-migrate-jcomments]').on('click', function() {
		var button = $(this);

		if (running) {
			return;
		}

		running = true;

		button.attr('disabled', "disabled");
		button.html('<i class="fdi fa fa-cog fa-spin"></i>&nbsp; <?php echo JText::_('COM_KOMENTO_MIGRATING', true);?>');


		$('[data-progress-empty]').addClass('hide');
		$('[data-progress-status]').html('');
		$('[data-progress-stat]').html('');
		$('[data-progress-loading]').removeClass('hide');

		window.migrateComment();
	});

window.migrateComment = function() {
		// Get the values from the form
		var publishState = $('[data-migrate-comment-state]').val();
		var migrateLikes = $('[data-migrate-comment-likes]').val();
		var selectedComponent = $('[data-jcomments-components]').val();

		Komento.ajax('admin/views/migrators/migrateComments', {
			"component"	: "jcomments",
			"publishState": publishState,
			"migrateLikes": migrateLikes,
			"selectedComponent" : selectedComponent
		}).done(function(results, status) {
			$('[data-progress-status]').append(status);

			// if there is still item to migrate. keep running the migration
			if (results == true) {
				window.migrateComment();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			$('[data-migrate-jcomments]').removeAttr('disabled');
			$('[data-migrate-jcomments]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_KOMENTO_COMPLETED', true);?>');

			running = false;
		});
	}
});
