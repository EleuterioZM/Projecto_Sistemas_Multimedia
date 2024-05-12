Komento.ready(function($){

jQuery('[data-component]').on('click', function() {
	var component = jQuery(this).data('component');
	var filter = jQuery('select[name=filter_component]');

	filter.val(component).trigger('change');
});

});