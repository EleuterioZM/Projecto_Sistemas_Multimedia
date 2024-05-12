Komento.require()
.script('site/subscriptions/default')
.done(function($) {
	$('[data-kt-subscriptions]').implement(Komento.Controller.Subscriptions, {
		"return": "<?php echo $returnURL;?>",
		"userid": "<?php echo KT::user()->id;?>"
	});
});