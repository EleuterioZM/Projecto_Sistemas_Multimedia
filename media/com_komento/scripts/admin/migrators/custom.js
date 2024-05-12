Komento.module('admin/migrators/custom', function($) {

var module = this;

Komento.Controller('Migrator.Custom', {
		defaults: {
			'{migrateTable}': '[data-custom-table]',
			'{componentFilter}': '[data-custom-component-filter]',
			'{tableColumns}': '[data-table-columns]',
			'{cycleAmount}': '[data-migrate-cycle]'
		}
	},
	function(self)
	{ return {
		init: function() {
			self.loadTableColumns();
		},

		'{migrateTable} change': function() {
			self.loadTableColumns();
		},

		loadTableColumns: function() {
			var tableName = self.migrateTable().val();

			Komento.ajax('admin/views/migrators/getColumns', {
				"tableName": tableName
			}).done(function(columns) {
				self.tableColumns().each(function(index, element) {
					
					var tmp = columns;

					if (!$(element).data('required')) {
						tmp = '<option value="notavailable">none</option>' + columns;
					}

					$(element).html(tmp);
				});
			});

		},

		getData: function() {
			var data = {};

			data.table = self.migrateTable().val();

			self.tableColumns().each(function(index, element) {
				var key = $(element).attr('id').slice(15);
				data[key] = $(element).val();
			});

			data.componentFilter = self.componentFilter().val();
			data.cycle = self.cycleAmount().val();

			return data;
		}
	} }
);

module.resolve();
});
