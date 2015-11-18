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

				return this.each(function(){

					var $smartSearch = $(this);
					var opts = $.extend(true, {}, defaultOptions, options);

					if (opts.fields.length === 0) {
						opts.fields.push($smartSearch.attr('name'));
					}

					// Store the plugin options with the element.
					$smartSearch.data(stateKey, opts);

					// Create a container to hold the input as well as selected items.
					methods.private.createInputControl($smartSearch);

					// Create a container for the suggestions list.
					methods.private.createSuggestionControl($smartSearch);

					// Establish any events and event handlers for the input control.
					methods.private.defineEvents($smartSearch);

				});

			}

		}, // END of public


		private: {

			/**
			 * This function is responsible for create a new form component with the desired
			 * "smart search" functionality from an existing text input element.
			 * @param  {jQuery}  $smartSearch [description]
			 * @return {void}
			 */
			createInputControl: function($smartSearch) {
				var $form = $($smartSearch[0].form);
				var pluginData = $smartSearch.data(stateKey);

				// Create a new input which will be used instead of the original
				var $searchInput = $('<input>')
					.attr({
						autocomplete:'off',
						name: $smartSearch.attr('name') == 'q' ? 'q' : '',
						placeholder: $smartSearch.attr('placeholder'),
						type: 'text'
					})
					.css({
						border: 0,
						padding:0,
						outline:'none',
						minWidth: '5em',
						width: '100%'
					})
					.val($smartSearch.val());

				// Create a place within the smart search container to hold the selected criteria
				var $inputListItem = $('<span>')
					.addClass('wnt-smart-search-input')
					.css({
						display: 'inline-block',
						width: '100%'
					})
					.append($searchInput);

				// Create a container to wrap the entire smart search component.
				var $container = $('<span>')
					.addClass($smartSearch.attr('class'))
					.css({
						display:'inline-block',
						'text-align': 'left'
					})
					.append($inputListItem)
					.click(function(){
						$searchInput.focus();
					})
					.resize(function(){
						methods.private.resizeSuggestionsList($smartSearch);
					})
					.insertBefore($smartSearch);

				// If the original input field was in focus when we started creating the smart search
				// bring focus to the new input container.
				if ($smartSearch.is(':focus')) {
					$searchInput.focus();
				}

				// Hide the original input element and move it to the beginning of the form.
				$smartSearch.hide();
				$smartSearch.prependTo($form);

				// If the original input element has name "q" (the primary open text field) remove
				// its name attribute to avoid issues later on.
				if ($smartSearch.attr('name') == 'q') {
					$smartSearch.removeAttr('name');
					$searchInput.val($smartSearch.val());
				}


				// Store references to the new input and the outer smart search container.
				pluginData.searchInput = $searchInput;
				pluginData.listContainer = $container;

				$smartSearch.data(stateKey, pluginData);

			},


			/**
			 * This function is responsible for creating a place to display and select suggested
			 * search criteria for what was typed into the smart search input field.
			 * @param  {jQuery}  $smartSearch [description]
			 * @return {void}
			 */
			createSuggestionControl: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var $searchInput = pluginData.searchInput;

				// Create a container to hold the default placeholder text. This text is an
				// indicator to the user that what they have typed has triggered an action.
				var $searchingMessage = $('<div>')
					.addClass('wnt-message-searching')
					.text('Searching ...');

				// Create an outer container for holding all of the smart search suggested criteria.
				// This container will be hidden and empty by default.
				// If a user's mouse enters the container and then leaves the container should hide.
				var $suggestions = $('<div>')
					.addClass('wnt-suggestions')
					.css({
						position: 'absolute',
						left: 0,
						width: $list.outerWidth() + 1,
						backgroundColor: 'white',
						zIndex: 9000000
					})
					.hide()
					.append($searchingMessage)
					.appendTo($('.wnt-search'));

				// Store a reference to the suggestion container with the smart search element.
				pluginData.suggestionContainer = $suggestions;

				$smartSearch.data(stateKey, pluginData);

			},


			defineEvents: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $searchInput = pluginData.searchInput;
				var $list = pluginData.listContainer;
				var $suggestions = pluginData.suggestionContainer;
				var $form = $($smartSearch[0].form);

				$smartSearch.on('wntFocus', function(event){
					$searchInput.focus();
				});

				// Listen to each field for changes.
				for (var i=0,l=pluginData.fields.length; i<l; i++) {
					var $field = $form.find('input[name="' + pluginData.fields[i] + '"]');
					$field.on('change', {$smartSearch:$smartSearch}, methods.private.eventHandler.fieldChange);
				}

			},


			eventHandler: {

				fieldChange: function(event) {
					var $smartSearch = event.data.$smartSearch;

					// If there was a change and "I" didn't make it take action.
					if (event.relatedTarget != $smartSearch[0]) {
						methods.private.refreshExistingValues($smartSearch, event);
					}

				}

			} // END of eventHandler

		} // END of private


	}

	$.fn[pluginName] = function(method)
	{
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.public.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );
		}
	}

})(jQuery);
