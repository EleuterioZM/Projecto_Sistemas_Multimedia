Komento.ready(function($){

	$('[data-preview-comment]').on('click', function() {
		var parent = $(this).parents('[data-comment-wrapper]');
		var content = parent.find('[data-comment-content]');

		content.toggleClass('t-hidden');
	});

	<?php if (KT::isFreeVersion()) { ?>
	jQuery(document).on('fd.upgrade.pro', function(event) {
		Komento.dialog({
			content: Komento.ajax('admin/views/komento/upgradeToPro'),
			bindings: {
				"{submitButton} click": function() {
					window.open('<?php echo KT_PRODUCT_PAGE;?>', '_blank').focus();
					Komento.dialog().close();
				}
			}
		});
	});
	<?php } ?>
});