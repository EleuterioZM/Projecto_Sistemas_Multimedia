
Komento.ready(function($) {

try {

	var task = <?php echo $task->toJSON(); ?>;
	var	method = {
			"success": "info",
			"info": "info",
			"warning": "warn",
			"error": "warn"
		};

	if (task.state == 'error') {
		console.error('There was an error building the stylesheets. View log below.');
	}
	
	console.groupCollapsed('Stylesheet builder ...');

	$.each(task.details, function(i, detail){

		console[method[detail.type]](detail.message);
	});
	console.log("Total time: " + (Math.round(task.time_total * 1000) / 1000) + "s");
	console.log("Peak memory usage: " + (task.mem_peak/1048576).toFixed(2) + "mb");
	console.log("View complete log: ", task);

	console.groupEnd();



} catch(e) {};


});