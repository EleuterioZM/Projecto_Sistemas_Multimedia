Komento.require().script('admin/migrators/custom').done(function($) {
    
    $('[data-migrator-custom-data]').addController('Komento.Controller.Migrator.Custom');


	$('[data-migrate-custom]').on('click', function() {

		// Disable the button from being clicked twice
		$(this).attr('disabled', "true");

		// Update the buttons message
		$(this).html('<i class="fdi fa fa-cog fa-spin"></i>&nbsp; <?php echo JText::_('COM_KOMENTO_MIGRATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		// clear the stats.
		$('[data-progress-stat]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		var data = $('[data-migrator-custom-data]').controller().getData();

		Komento.ajax('admin/views/migrators/migrateComments', {
			"component"	: "custom",
			"data" : data,
			"task" : "getStatistic"
		}).done(function(totalComments) {
			if (totalComments > 0) {
				window.migrateComment(data, totalComments, 0);
			} else {
				//remove loading icon.
		  		$('[data-progress-loading]').addClass('hide');

		  		$('[data-migrate-custom]').removeAttr('disabled');
		  		$('[data-progress-status]').append('No comments available to be migrated');
		  		$('[data-migrate-custom]').html('<i class="fdi fa fa-check"></i>&nbsp; <?php echo JText::_('COM_KOMENTO_MIGRATORS_NO_COMMENTS', true);?>');
			}
			
		});
	});

	window.migrateComment = function(data, totalComments, start) {
		if (start >= totalComments) {

	  		$('[data-progress-loading]').addClass('hide');
	  		$('[data-progress-status]').append('Total of ' + totalComments + ' are migrated successfully.');
	  		$('[data-migrate-custom]').removeAttr('disabled');
	  		$('[data-migrate-custom]').html('<i class="fdi fa fa-check"></i>&nbsp; <?php echo JText::_('COM_KOMENTO_COMPLETED', true);?>');

	  		return;
		}

		data['start'] = start;

		Komento.ajax('admin/views/migrators/migrateComments',
		{
			"component"	: "custom",
			"data" : data
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}
		})
		.done(function(newStart)
		{
			if (newStart == 'noitem') {
				//remove loading icon.
		  		$('[data-progress-loading]').addClass('hide');

		  		$('[data-migrate-custom]').removeAttr('disabled');
		  		$('[data-migrate-custom]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_KOMENTO_MIGRATORS_NO_COMMENTS', true);?>');
			}
			self.migrateComment(data, totalComments, newStart);
		});
	};
});
