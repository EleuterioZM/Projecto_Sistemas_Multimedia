<?php if (!$invisible) { ?>
Komento.require()
.script("<?php echo $server;?>")
.done(function($) {

	Komento.Recaptcha = true;

	window.recaptchaCallback = function(val) {
		$('[data-kt-recaptcha-response]')
			.val(val)
			.trigger('change');
	};
});
<?php } ?>



<?php if ($invisible) { ?>
Komento.ready(function($) {

var widgetId = null;

// Create recaptcha task
var task = [
	'recaptcha', {
		'sitekey': '<?php echo $key;?>',
		'theme': '<?php echo $layout;?>'
	}
];


var runTask = function(task) {
	widgetId = grecaptcha.render($('[data-kt-recaptcha-invisible]')[0], {
				"sitekey": "<?php echo $key;?>"
	});
}

window.ktRecaptchaDfd = $.Deferred();
window.ktGetRecaptchaResponse = function() {

	var token = grecaptcha.getResponse(widgetId);
	var responseField = $('[data-kt-recaptcha-response]');

	if (token) {
		responseField.val(token).trigger('change');

		window.ktRecaptchaDfd.resolve();

		return;
	}

	grecaptcha.reset(widgetId);

	window.ktRecaptchaDfd.reject();
};

$(document).on('onSubmitComment', function(event, save) {
	save.push(window.ktRecaptchaDfd);

	grecaptcha.execute(widgetId);
});

$(document).on('onReloadCaptcha', function(event) {
	setTimeout(function() {
		grecaptcha.reset(widgetId);

		window.ktRecaptchaDfd = $.Deferred();
	}, 500);
});

// If grecaptcha is not ready, add to task queue
if (!window.recaptchaScriptLoaded) {
	var tasks = window.recaptchaTasks || (window.recaptchaTasks = []);

	var found = false;
	// check if this task already registered or not.
	$(tasks).each(function(idx, item) {
		if (item[0] == task[0]) {
			found = true;
			return false;
		}
	});

	if (found === false) {
		tasks.push(task);
		window.recaptchaTasks = tasks;
	}

// Else run task straightaway
} else {
	runTask(task);
}

// If recaptacha script is not loaded
if (!window.recaptchaScriptLoaded) {

	// Load the recaptcha library
	Komento.require()
			.script("//www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=<?php echo $language;?>");

	window.recaptchaCallback = function() {
		var task;
		while (task = window.recaptchaTasks.shift()) {
			runTask(task);
		}
	};

	window.recaptchaScriptLoaded = true;
}

});

<?php } ?>