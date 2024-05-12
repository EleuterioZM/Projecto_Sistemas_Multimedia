Komento.ready(function($) {

	$('[data-avatar-integration]').on('change', function() {
		var value = $(this).val();

		$('[data-avatar-option]').addClass('t-hidden');

		var wrapper = $('[data-avatar-' + value);
		wrapper.removeClass('t-hidden');
	});

	$('[data-avatar-character-based]').on('change', function() {
		var value = $(this).val();

		if (value == 1) {
			// Hide integration settings
			$('[data-avatar-option]').addClass('t-hidden');
			$('[data-avatar-integration-option]').addClass('t-hidden');


			$('[data-avatar-character-based-background-color]').removeClass('t-hidden');
			$('[data-avatar-character-based-font-color]').removeClass('t-hidden');

			return;
		}

		$('[data-avatar-character-based-background-color]').addClass('t-hidden');
		$('[data-avatar-character-based-font-color]').addClass('t-hidden');

		$('[data-avatar-integration-option]').removeClass('t-hidden');

		var avatarValue = $('[data-avatar-integration]').val();

		// Hide everything
		$('[data-avatar-option]').addClass('t-hidden');

		// Show selected
		$('[data-avatar-' + avatarValue).removeClass('t-hidden');
	});
});