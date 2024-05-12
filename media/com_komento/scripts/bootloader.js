(function(ns){
// Enqueue class
var enqueue = function(fn) {
	var queue = [], locked = 1, working = 0, fn = fn,
		instance = function(){
			queue.push([this, arguments]);
			if (!locked) instance.execute();
		};
		instance.execute = function(){
			if (working) return;
			working = 1; locked = 0;
			var q; while(q = queue.shift()) { fn.apply(q[0], q[1]) };
			working = 0;
		};
		instance.lock = function(){
			locked = 0;
		};
	return instance;
};

// Private variables
var $, options, components = {}, initialized = 0, installers = [];

var self = window[ns] = {

	setup: function(o) {
		options = o; // Keep a copy of the options
		self.init(); // Try to initialize.
	},

	jquery: function(jquery) {
		if ($) return; // If jquery is already available, stop.
		$ = jquery; // Set self.$ to jquery object
		self.init(); // Try to initialize.
	},

	init: function() {
		if (initialized) {
			return; // If initialized, stop.
		}

		if ($ && options) { // If options & jquery is available,
			self.$ = $.initialize(options); // Initialize jquery
			self.plugin.execute(); // Execute any pending plugins
			initialized = 1;
		}
	},

	plugin: enqueue(function(name, factory) {
		factory.apply(self, [$]);
	}),

	module: enqueue(function(name, factory) {
		$.module(name, factory);
	}),

	installer: function(recipient, name, factory) {
		if (!installers[recipient]) installers[recipient] = []; // Create package array if this is the first time
		if (!name) return installers[recipient];
		var component = components[recipient]; // Get component
		if (component.registered) return component.install(name, factory); // If component exist, install straight away
		installers[recipient].push([name, factory]); // Keep the package to install later
	},

	component: function(name, options) {

		// Getter
		if (!name) {
			return components; // return list of components
		}

		if (!options) {
			return components[name]; // return component
		}

		// Registering
		if (typeof options === "function") {
			var component = options;
			component.registered = true;
			return components[name] = component;
		}

		// Setter
		var queue = [];

		var abstractQueue = function(method, context, args) {
			return {method: method, context: this, args: args};
		};

		var abstractMethod = function(method, parent, chain) {
			return function(){
				(chain || queue).push(abstractQueue(method, this, arguments));
				return parent;
			};
		};

		var abstractInstance = function(instance, methods, chain) {
			var i = 0;
			for (; i < methods.length; i++) {
				var method = methods[i];
				instance[method] = abstractMethod(method, instance, chain);
			};
			return instance;
		};

		var abstractChain = function(name, methods) {
			return function(){
				var chain = [abstractQueue(name, this, arguments)];
					queue.push(chain);
				return abstractInstance({}, methods, chain);
			};
		};

		queue.execute = function(){
			var component = components[name], i = 0;
			for (; i < queue.length; i++) {
				var fn = queue[i];
				if (Object.prototype.toString.call(fn)==='[object Array]') {
					var chain = fn, context = component, j = 0;
					for (; j < chain.length; j++) {
						context = context[chain[j].method].apply(context, chain[j].args);
					}
				} else {
					component[fn.method].apply(component, fn.args)
				}
			}
		};

		// Create abstract component
		var component = abstractInstance(
				function(){component.run.apply(this.arguments)},
				["run","ready","template","dialog"]
			);

			// Set reference to options & queue
			component.className = name;
			component.options = options;
			component.queue = queue;

			// Create abstract module method
			component.module = abstractChain(
				"module",
				["done","always","fail","progress"]
			);

			// Create abstract require method
			component.require = abstractChain(
				"require",
				["library","script","stylesheet","language","template","app","view","done","always","fail","progress"]
			);

		// Register component in global namespace
		window[name] = components[name] = component;

		if (initialized) {
			$.Component.register(component);
		}

		return component;
	}
};

})("KTVendors");

// Setup foundry
KTVendors.setup({
	"environment": window.kt.environment,
	"source": "local",
	"mode": window.kt.environment == "production" ? "compressed" : "uncompressed",
	"path": window.kt.rootUrl + "/media/com_komento/scripts/vendors",
	"cdn": "",
	"extension":".js",
	"cdnPath": "",
	"rootPath": window.kt.rootUrl,
	"basePath": window.kt.rootUrl,
	"indexUrl": window.kt.rootUrl + '/index.php',
	"token": window.kt.token,
	"joomla":{
		"appendTitle": window.kt.appendTitle,
		"sitename": window.kt.siteName
	},
	"locale":{
		"lang": window.kt.locale
	}
});

KTVendors.component("Komento", {
	"environment": window.kt.environment,
	"source":"local",
	"mode": window.kt.environment == "production" ? "compressed" : "uncompressed",
	"mode": "compressed",
	"version":"3.0",
	"momentLang": window.kt.momentLang,
	"ajaxUrl": window.kt.ajaxUrl
});