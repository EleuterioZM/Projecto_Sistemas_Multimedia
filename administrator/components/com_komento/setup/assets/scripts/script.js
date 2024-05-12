
$(document).ready(function(){
	loading = $('[data-installation-loading]'),
	submit = $('[data-installation-submit]'),
	retry = $('[data-installation-retry]'),
	form = $('[data-installation-form]'),
	completed = $('[data-installation-completed]'),
	source = $('[data-source]'),
	installAddons = $('[data-installation-install-addons]');
});

var kt = {

	init: function() {
	},

	options: {
		"apikey": "<?php echo $input->get('apikey', '');?>",
		"path": null,
		"controller": "install"
	},

	ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_komento&ajax=1",
	contract: "<?php echo SI_CONTRACT; ?>",
	
	ajax: function(task, properties, callback) {

		var prop = $.extend(kt.options, properties);

		var dfd = $.Deferred();

		$.ajax({
			type: "POST",
			url: kt.ajaxUrl + "&controller=" + prop.controller + "&task=" + task,
			data: prop
		}).done(function(result) {
			callback && callback.apply(this, [result]);

			dfd.resolve(result);
		});

		return dfd;
	},

	addons: {

		installModule: function(element, path) {

			return kt.ajax('installModule', {
				"controller": "addons",
				"path": path,
				"module": element
			});

		},

		installPlugin: function(plugin, path) {
			return kt.ajax('installPlugin', {
				"controller": "addons",
				"path": path,
				"element": plugin.element,
				"group": plugin.group
			});
		},

		runScript: function(script) {
			// Run the maintenace scripts
			return $.ajax({
				type: 'POST',
				url: kt.ajaxUrl + '&controller=maintenance&task=execute',
				data: {
					script: script
				}
			});
		},

		retrieveList: function() {

			var progress = $('[data-addons-progress]');
			var selection = $('[data-addons-container]');
			var syncProgress = $('[data-sync-progress]');

			// Show loading
			loading.removeClass('d-none');

			// Hide submit
			submit.addClass('d-none');

			kt.ajax('list', {"controller": "addons", "path": kt.options.path}, function(result) {

				// Hide the retrieving message
				$('[data-addons-retrieving]').addClass('d-none');

				loading.addClass('d-none');
				installAddons.removeClass('d-none');

				selection.html(result.html);

				// Get files for maintenance
				var scripts = result.scripts;
				var maintenanceMsg = result.maintenanceMsg;

				// Set the submit
				installAddons.on('click', function() {

					// Hide the container
					selection.addClass('d-none');

					// Show the installation progress
					progress.removeClass('d-none');
					syncProgress.removeClass('d-none');

					// Install the selected items
					var modules = [];
					var plugins = [];

					$('[data-checkbox-module]:checked').each(function(i, el) {
						modules.push($(el).val());
					});

					$('[data-checkbox-plugin]:checked').each(function(i, el) {
						var plugin = {
										"element": $(el).val(),
										"group": $(el).data('group')
									};

						plugins.push(plugin);
					});

					var total = modules.length + plugins.length;
					var each = 100 / total;
					var progressBar = $('[data-progress-bar]');
					var progressBarResult = $('[data-progress-bar-result]');

					var totalScripts = scripts.length;
					var eachScript = 100 / totalScripts;
					var syncProgressBar = $('[data-sync-progress-bar]');
					var syncProgressBarResult = $('[data-sync-progress-bar-result]');

					var runMaintenance = function() {

						var frame = $('[data-progress-execscript]');

						frame.addClass('active')
							.removeClass('pending');

						var item = $('<li>');
						
						item.html(maintenanceMsg);

						$('[data-script-logs]').append(item);

						var scriptIndex = 0,
							dfd = $.Deferred();

						var runNextScript = function() {
							if (scripts[scriptIndex] == undefined) {

								$.ajax({
									type: 'POST',
									url: kt.ajaxUrl + '&controller=maintenance&task=finalize'
								}).done(function(result) {
									var item = $('<li>');
									item.addClass('text-success').html(result.message);
									$('[data-progress-execscript-items]').append(item);

									$('[data-progress-execscript]')
										.find('.progress-state')
										.html(result.stateMessage)
										.addClass('text-success')
										.removeClass('text-info');
								});

								dfd.resolve();
								return;
							}

							kt.addons
								.runScript(scripts[scriptIndex])
								.done(function(data) {
									scriptIndex++;

									// update the progress bar here
									var currentWidth = parseInt(syncProgressBar[0].style.width);
									var percentage = Math.round(currentWidth + eachScript);
									
									syncProgressBar.css('width', percentage + '%');
									syncProgressBarResult.html(percentage + '%');

									var item = $('<li>'),
										className = data.state ? 'text-success' : 'text-error';

									item.addClass(className).html(data.message);

									$('[data-progress-execscript-items]').append(item);

									runNextScript();
								});

						};

						runNextScript();

						return dfd;
					};

					var installModules = function() {

						var moduleIndex = 0,
							dfd = $.Deferred();

						var installNextModule = function() {
							if (modules[moduleIndex] == undefined) {

								dfd.resolve();
								return;
							}

							kt.addons
								.installModule(modules[moduleIndex], result.modulePath)
								.done(function(data) {
									moduleIndex++;

									var currentWidth = parseInt(progressBar[0].style.width);
									var percentage = Math.round(currentWidth + each);

									$('[data-progress-active-message]').html(data.message);

									progressBar.css('width', percentage + '%');
									progressBarResult.html(percentage + '%');

									installNextModule();
								});
						};

						installNextModule();

						return dfd;
					};

					var installPlugins = function() {

						var pluginIndex = 0;
						var dfd = $.Deferred();


						var installNextPlugin = function() {

							if (plugins[pluginIndex] == undefined) {

								dfd.resolve();
								return;
							}

							kt.addons.installPlugin(plugins[pluginIndex], result.pluginPath)
								.done(function(data) {

									pluginIndex++;

									var progressBarResult = $('[data-progress-bar-result]');
									var currentWidth = parseInt(progressBar[0].style.width);
									var percentage = Math.round(currentWidth + each) + '%';

									$('[data-progress-active-message]').html(data.message);
										
									// Update the width of the progress bar
									progressBar.css('width', percentage);

									installNextPlugin();
								});
						};

						installNextPlugin();

						return dfd;
					};

					// Show loading indicator
					loading.removeClass('d-none');
					installAddons.addClass('d-none');

					// Install Modules
					installModules().done(function() {
						installPlugins().done(function() {
							
							// Show complete
							$('[data-progress-active-message]').addClass('d-none');
							$('[data-progress-complete-message]').removeClass('d-none');
							$('[data-progress-bar]').css('width', '100%');
							$('[data-progress-bar-result]').html('100%');

							runMaintenance().done(function() {

								// When everything is done, update the submit button
								loading.addClass('d-none');
								submit.removeClass('d-none');

								$('[data-sync-progress-active-message]').addClass('d-none');
								$('[data-sync-progress-complete-message]').removeClass('d-none');
								$('[data-sync-progress-bar]').css('width', '100%');
								$('[data-sync-progress-bar-result]').html('100%');

								submit.on('click', function() {
									form.submit();
								});
							})
						});
					});
				});
			});
		}
	},

	installation: {
		path: null,

		showRetry: function(step) {

			retry
				.data('retry-step', step)
				.removeClass('d-none');

			// Hide the submit
			submit.addClass('d-none');

			// Hide the loading
			loading.addClass('d-none');
		},

		extract: function(packageName) {

			kt.ajax('extract', {"package": packageName}, function(result) {

				// Update the progress
				kt.installation.update('data-progress-extract', result, '10%');

				if (!result.state) {
					kt.installation.showRetry('extract');
					return false;
				}

				// Set the path
				kt.options.path = result.path;

				// Run the next command
				kt.installation.runSQL();
			});
		},

		download: function() {
			kt.installation.setActive('data-progress-download');

			kt.ajax('download', {}, function(result) {

				// Set the progress
				kt.installation.update('data-progress-download', result, '20%');

				if (!result.state) {
					kt.installation.showRetry('download');
					return false;
				}

				// Set the installation path
				kt.options.path = result.path;

				kt.installation.runSQL();
			});
		},

		runSQL: function() {
			
			// Install the SQL stuffs
			kt.installation.setActive('data-progress-sql');

			kt.ajax('sql', {}, function(result) {

				// Update the progress
				kt.installation.update('data-progress-sql', result, '25%');

				if (!result.state) {
					kt.installation.showRetry('runSQL');
					return false;
				}

				// Run the next command
				kt.installation.installFoundry();
			});
		},

		installFoundry : function() {

			// Install the foundry package
			kt.installation.setActive('data-progress-foundry');

			kt.ajax('foundry', {}, function(result) {
				// Set the progress
				kt.installation.update('data-progress-foundry', result, '18%');

				if (!result.state) {
					kt.installation.showRetry('installFoundry');
					return false;
				}

				kt.installation.installAdmin();
			});
		},

		installAdmin: function() {

			// Install the admin stuffs
			kt.installation.setActive('data-progress-admin');

			// Run the ajax calls now
			kt.ajax('copy', {"type": "admin"}, function(result) {
				
				// Update the progress
				kt.installation.update('data-progress-admin', result, '35%');

				if (!result.state) {
					kt.installation.showRetry('installAdmin');
					return false;
				}

				kt.installation.installSite();
			});
		},

		installSite : function() {
			
			// Install the admin stuffs
			kt.installation.setActive('data-progress-site');

			kt.ajax('copy' , { "type" : "site" }, function(result) {


				// Update the progress
				kt.installation.update('data-progress-site', result, '50%');

				if (!result.state) {
					kt.installation.showRetry('installSite');
					return false;
				}

				kt.installation.installLanguages();
			});
		},

		installLanguages : function() {
			// Install the admin stuffs
			kt.installation.setActive('data-progress-languages');

			kt.ajax('copy', {"type": "languages"}, function(result) {
				
				// Set the progress
				kt.installation.update( 'data-progress-languages' , result , '55%');

				if (!result.state) {
					kt.installation.showRetry('installLanguages');
					return false;
				}

				kt.installation.installMedia();
			});

		},
		
		installMedia : function() {
			
			// Install the admin stuffs
			kt.installation.setActive('data-progress-media');

			kt.ajax('copy', {"type": "media"}, function(result) {
				// Set the progress
				kt.installation.update( 'data-progress-media' , result , '65%');

				if (!result.state) {
					kt.installation.showRetry('installMedia');
					return false;
				}

				kt.installation.installToolbar();
			});
		},

		installToolbar : function() {

			if (kt.contract === 'free') {
				kt.installation.syncDB();
			}

			// Install the toolbar package
			kt.installation.setActive('data-progress-toolbar');

			kt.ajax('toolbar', {}, function(result) {
				// Set the progress
				kt.installation.update('data-progress-toolbar', result, '75%');

				if (!result.state) {
					kt.installation.showRetry('installToolbar');
					return false;
				}

				kt.installation.syncDB();
			});
		},

		syncDB: function() {

			// Synchronize the database
			kt.installation.setActive('data-progress-syncdb');

			kt.ajax('sync', {}, function(result) {
				kt.installation.update('data-progress-syncdb', result, '85%');

				if (!result.state) {
					kt.installation.showRetry('syncDB');
					return false;
				}

				kt.installation.postInstall();
			});
		},

		postInstall : function() {
			
			// Perform post installation stuffs here
			kt.installation.setActive('data-progress-postinstall');

			kt.ajax('post', {}, function(result) {
				
				// Set the progress
				kt.installation.update('data-progress-postinstall' , result , '100%');

				if (!result.state) {
					kt.installation.showRetry('postInstall');
					return false;
				}

				completed
					.removeClass('d-none')
					.show();

				loading
					.addClass('d-none');

				submit
					.removeClass('d-none');

				submit.on('click', function() {

					source.val(kt.options.path);

					form.submit();
				});

			});
		},

		update: function(element, obj) {
			var logItem = $('[' + element + ']');

			logItem.removeClass("is-loading")
				.addClass(obj.state ? 'is-complete' : 'is-error');
		},

		setActive: function(item) {
			$('[data-progress-active-message]').html($('[' + item + ']').find('.split__title').html() + ' ...');
			$('[' + item + ']').removeClass('pending').addClass('active is-loading');
			$('[' + item + ']').find('.progress-icon > i') .removeClass('icon-radio-unchecked') .addClass('loader');
		}
	},
	maintenance: {
		
		init: function() {
			// Initializes the installation process.
			kt.maintenance.update();
		},

		update: function() {

			var frame = $('[data-progress-execscript]');

			frame.addClass('active')
				.removeClass('pending');

			$.ajax({
				type: 'POST',
				url: kt.ajaxUrl + '&controller=maintenance&task=getScripts'
			}).done(function(result){

				var item = $('<li>');
				item.addClass('text-success').html(result.message);

				$('[data-progress-execscript-items]').append(item);

				kt.maintenance.runScript(result.scripts, 0);
			});
		},

		runScript: function(scripts, index) {

			if (scripts[index] === undefined) {
				// If the logics come here, means we are done with running scripts

				// run script completed. lets update the scriptversion
				$.ajax({
					type: 'POST',
					url: kt.ajaxUrl + '&controller=maintenance&task=finalize'
				}).done(function(result) {
					var item = $('<li>');
					item.addClass('text-success').html(result.message);
					$('[data-progress-execscript-items]').append(item);

					$( '[data-progress-execscript]' )
						.find( '.progress-state' )
						.html( result.stateMessage )
						.addClass( 'text-success' )
						.removeClass( 'text-info' );

					kt.maintenance.complete();
				});

				return true;
			}

			// Run the maintenace scripts
			$.ajax({
				type: 'POST',
				url: kt.ajaxUrl + '&controller=maintenance&task=execute',
				data: {
					script: scripts[index]
				}
			})
			.always(function(result) {

				var item = $('<li>'),
					className	= result.state ? 'text-success' : 'text-error';

				item.addClass(className).html(result.message);

				$('[data-progress-execscript-items]').append(item);

				kt.maintenance.runScript(scripts, ++index);
			});
		},

		complete: function() {
			$('[data-installation-loading]').hide();
			$('[data-installation-submit]').show();

			var form = $('[data-installation-form]');

			// Bind the submit button
			$('[data-installation-submit]').on('click', function() {
				form.submit();
			});
		}
	}
}