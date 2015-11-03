(function($){

	var pluginName = 'wolfnetSmartSearch';

	var stateKey = pluginName + '.state';

	var defaultOptions = {
		fields: [],
		minLength: 3,
		fieldMap: {},
		searchField: null,
		suggestionHoverClass: 'wnt-hover'
		//suggestionHoverClass: 'wnt-hover'
	};

	var methods = {

		public: {
			/**
			 * This function initializes the plugin.
			 */
			init: function(options) {

				// Enforce required arguments.
				if (!options.ajaxUrl) {
					$.error('The "ajaxUrl" option must be included when initializing the plugin.');
					return this;
				}
				if (!options.ajaxAction) {
					$.error('The "ajaxAction" option must be included when initializing the plugin.');
					return this;
				}

			}
		},

		private: {
		},

		/**
		 * This function acts as a wrapper to the initialization of the plugin.
		 * @param  {Object}  options  An object/map of options for the plugin.
		 * @return {jQuery}  The jQuery selection object which the plugin is being applied to.
		 */
		wrapper : function(options) {
			methods.public.init(options);
		}

	}

	$.fn[pluginName] = function(method)
	{

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.wrapper.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );
		}
	}

})(jQuery);
