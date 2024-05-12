Komento.ready(function($) {
	$('[data-kt-login-submit="<?php echo $uniqid; ?>"]').on('click', function() {
		$('[data-kt-login-form="<?php echo $uniqid; ?>"]').submit();
	});
});