jQuery(document).ready(function($) {
	FD.require().script('vendor/flatpickr')
	.done(function() {
		var element = document.querySelector('[data-fd-datetimepicker][data-uid="<?php echo $uid;?>"]');
		var wrapper = $('[data-fd-datetimepicker-wrapper]');
		var appearance = $(element).data('appearance') || 'dark';

		if (wrapper.length <= 0) {
			$('body').append('<div class="t-hidden" data-fd-datetimepicker-wrapper><div id="fd"><div class="' + appearance + '" data-fd-datetimepicker-contents></div></div></div>');	
		}

		flatpickr(element, {
			enableTime: '<?php echo $enableTime ? true : false; ?>',
			wrap: true,
			locale: "<?php echo $languageTag;?>",
			appendTo: document.querySelector("[data-fd-datetimepicker-contents]"),
			mode: "<?php echo $mode; ?>",
			defaultDate: "<?php echo $value;?>",
			onOpen: function() {
				var wrapper = $('[data-fd-datetimepicker-wrapper]');
				wrapper.removeClass('t-hidden');
			},
			onClose: function() {
				var wrapper = $('[data-fd-datetimepicker-wrapper]');
				wrapper.addClass('t-hidden');
			}
		});
	})
});