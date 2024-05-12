Komento.ready(function($) {

	$.Joomla("submitbutton", function(task) {

		$('#submenu li').children().each(function(){
			if ($(this).hasClass('active')) {
				$('#active').val($(this).attr('id'));
			}
		});

		$('dl#subtabs').children().each(function(){
			if ($(this).hasClass('open')) {
				$('#activechild').val($(this).attr('class').split(" ")[0]);
			}
		});

		$.Joomla("submitform", [task]);
	});

	window.changeComponent = function(component) {
		document.adminForm.target.value = component;
		document.adminForm.submit();
	}
});

Komento.require().script('migrator.actions').done(function($) {
	$(document).ready(function() {
		$('.tab-pane').implement('Komento.Controller.Migrator.Actions');
	});
});