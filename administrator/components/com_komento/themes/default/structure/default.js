Komento.ready(function($) {
	<?php if (FH::isJoomla4()) { ?>
	$('[data-fd-structure]')
		.removeClass('is-loading')
		.addClass('is-done-loading');

	$('[data-fd-body]').removeClass('t-hidden');
	<?php } ?>
});