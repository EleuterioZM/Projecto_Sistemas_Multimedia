Komento.require()
.script('site/dashboard/default')
.done(function($) {
	$('[data-kt-dashboard]').implement(Komento.Controller.Dashboard, {
		"return": "<?php echo $returnURL;?>"
	});

	<?php if ($this->config->get('bbcode_code')) { ?>
	FD.require()
	.script('vendor/prism')
	.done(function() {
		Prism.highlightAll();
	});
	<?php } ?>
});