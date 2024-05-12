Komento.ready(function($){

	$('[data-migrate-zoo]').on('click', function() {

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

		// try get the post from selected categories
		var categoryId = $('[data-zoo-category]').val();

		Komento.ajax('admin/views/migrators/migrateComments',
		{
			"component"	: "com_zoo",
			"categoryId" : categoryId,
			"getPosts" : true
		}).done(function(postIds)
		{	
			if (!postIds) {
				$('[data-migrate-zoo]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('No post to be migrated for the selected category');?>');
				return;
			}

			// Once get the posts, we will migrate the comment
			window.migrateComment(postIds);
		});
	});

	$('[data-zoo-category]').on('change', function() {
		$('[data-migrate-zoo]').removeAttr('disabled');
		$('[data-migrate-zoo]').html('<?php echo JText::_('COM_KOMENTO_MIGRATORS_RUN_NOW');?>');
	});

window.migrateComment = function(postIds) {

		var total = postIds.length;
		
		$.each(postIds, function(index, value) {
		  
		  // Get the values from the form
		  var publishState = $('[data-migrate-comment-state]').val();

		  Komento.ajax('admin/views/migrators/migrateComments',
		  {
		  	"component"	: "com_zoo",
		  	"publishState": publishState,
		  	"itemId": value
		  },
		  {
		  	append: function(selector, message) {
		  		$(selector).append(message);
		  	}
		  })
		  .done(function(results)
		  {
		  	if (index === total - 1) {
		  		//remove loading icon.
		  		$('[data-progress-loading]').addClass('hide');

		  		$('[data-migrate-zoo]').removeAttr('disabled');
		  		$('[data-migrate-zoo]').html('<i class="fdi fa fa-check"></i> <?php echo JText::_('COM_KOMENTO_COMPLETED', true);?>');
		  	}
		  });
		});
	}
});
