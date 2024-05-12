Komento.ready(function($) {
	$('[data-reset-css]').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		$('[data-custom-css]').each(function() {
			var original = $(this).data('original');
			$(this).val(original);
		});
	});
});