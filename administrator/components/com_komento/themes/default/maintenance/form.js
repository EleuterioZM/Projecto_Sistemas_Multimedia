Komento.require().script('admin/maintenance/maintenance').done(function($) {
	$('[data-maintenance-container]').addController('Komento.Controller.Maintenance.Execute');
});
