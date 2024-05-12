Komento.require().script('admin/maintenance/database').done(function($) {
	$('[data-maintenance-database]').addController('Komento.Controller.Maintenance.Execute');
});
