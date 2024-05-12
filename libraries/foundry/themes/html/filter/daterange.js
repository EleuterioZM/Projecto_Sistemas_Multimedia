FD.require()
.script('vendor/moment', 'vendor/daterangepicker')
.done(function($) {

	var moment = $.moment;

	var start = moment("<?php echo $start; ?>");
	var end = moment("<?php echo $end; ?>");
	var wrapper = $('[data-fd-date-range-<?php echo $uid;?>]');

	function update(start, end) {
		var display = wrapper.find('[data-fd-date-range-display]');
		display.html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}

	wrapper.daterangepicker({
		locale: {
			format: 'DD-MM-YYYY'
		},
		"applyClass" : 'o-btn--primary',
		"cancelClass" : 'o-btn--default-o',
		"startDate": <?php echo $start ? 'start' : 'moment()';?>,
		"endDate": <?php echo $end ? 'end' : 'moment()';?>,
		ranges: {
			'<?php echo JText::_('FD_TODAY');?>': [moment(), moment()],
			'<?php echo JText::_('FD_YESTERDAY');?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'<?php echo JText::_('FD_LAST_7_DAYS');?>': [moment().subtract(6, 'days'), moment()],
			'<?php echo JText::_('FD_LAST_30_DAYS');?>': [moment().subtract(29, 'days'), moment()],
			'<?php echo JText::_('FD_THIS_MONTH');?>': [moment().startOf('month'), moment().endOf('month')],
			'<?php echo JText::_('FD_LAST_MONTH');?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	}, update);

	<?php if ($start && $end) { ?>
	update(start, end);
	<?php } ?>

	wrapper.on('apply.daterangepicker', function(event, picker) {
		var start = picker.startDate.format('DD-MM-YYYY');
		var end = picker.endDate.format('DD-MM-YYYY');

		$('[data-fd-date-start]').val(start);
		$('[data-fd-date-end]').val(end);

		// Use native jquery method to submit
		<?php if ($submitonclick) { ?>
			$(this).closest('form').submit();
		<?php } ?>
		
	});

	$('[data-fd-date-range-reset]').on('click', function() {
		$('[data-fd-date-start]').val('');
		$('[data-fd-date-end]').val('');

		// Use native jquery method to submit
		<?php if ($submitonclick) { ?>
			$(this).closest('form').submit();
		<?php } ?>
	});
});