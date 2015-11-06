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
		 	 * @param  {Object} options: an object/map of options for the plugin.
		 	 * @return {jQuery} The jQuery selection object which the plugin is being applied to.
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

				//return this.each(function(){

				var $smartSearch = $(this);
				var opts = $.extend(true, {}, defaultOptions, options);

				if (opts.fields.length === 0) {
					opts.fields.push($smartSearch.attr('name'));
				}

				$smartSearch.data(stateKey, opts);

				// Create a container to hold the input as well as selected items.
				methods.private.createInputControl($smartSearch);

				//});

			}

		}, // end of collection of public methods


		private: {

			/**
			 * This function is responsible for create a new form component with the desired
			 * "smart search" functionality from an existing text input element.
			 * @param  {jQuery}  $smartSearch [description]
			 * @return {void}
			 */
			createInputControl: function($smartSearch) {
			}


		}, // end of collection of private methods


		/**
		 * This function acts as a wrapper to the initialization of the plugin.
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
