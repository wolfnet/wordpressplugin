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
			init: function() {

			}
		},

		private: {
		},

		/**
		 * This function is a wrapper for the initialization of the plugin.
		 * @param  {Object}  options  An object/map of options for the plugin.
		 * @return {jQuery}  The jQuery selection object which the plugin is being applied to.
		 */
		init : function() {

			methods.public.init();

		}

	}

	$.fn[pluginName] = function(method)
	{
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );
		}
	}

})(jQuery);
