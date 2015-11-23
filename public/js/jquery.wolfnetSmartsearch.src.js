(function($){

	var wntPlugin = 'wolfnetSmartSearch';
	var stateKey = wntPlugin + '.state';

	var defaultOptions = {
		fields: [],
		minLength: 3,
		fieldMap: {},
		searchField: null,
		suggestionHoverClass: 'wnt-hover'
	};

	var methods = {

		public: {

			/**
			 * This function initializes the plugin.
			 * @param  {Object}  options  An object/map of options for the plugin.
			 * @return {jQuery}  The jQuery selection object which the plugin is being applied to.
			 */
			init: function(options) {

				/* Enforce required arguments. */
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

					/* Store the plugin options with the element. */
					$smartSearch.data(stateKey, opts);

					/* Create a container to hold the input as well as selected items. */
					methods.private.createInputControl($smartSearch);

					/* Create a container for the suggestions list. */
					methods.private.createSuggestionControl($smartSearch);

					/* Establish any events and event handlers for the input control. */
					methods.private.defineEvents($smartSearch);

					/* Look for existing input fields and add values to the smart search input. */
					methods.private.refreshExistingValues($smartSearch, null, false);

				});

			},

			/**
			 * This function disables the smart search input field.
			 * @return  {jQuery}  The jQuery selection object which the plugin is being applied to.
			 */
			disable: function() {
				return this.each(function(){
					var $smartSearch = $(this);
					var pluginData = $smartSearch.data(stateKey);
					var $list = pluginData.listContainer;

					$smartSearch.prop('disabled', true);
					$list.hide();

				});

			},

			/**
			 * This function enables the smart search input field.
			 * @return  {jQuery}  The jQuery selection object which the plugin is being applied to.
			 */
			enable: function() {
				return this.each(function(){
					var $smartSearch = $(this);
					var pluginData = $smartSearch.data(stateKey);
					var $list = pluginData.listContainer;

					$smartSearch.prop('disabled', false);
					$list.show();

				});

			},

			/**
			 * This function retrieves the smart search container(s) for the selection.
			 * @return  {jQuery}  The jQuery selection object which the plugin is being applied to.
			 */
			getContainer: function() {
				var $container = $();

				this.each(function(){
					var $smartSearch = $(this);
					var pluginData = $smartSearch.data(stateKey);
					var $list = pluginData.listContainer;

					$container = $container.add($list);

				});

				return $container;

			}

		},

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
					/* If the user clicks anywhere within the container bring focus to the input
					 * text field. */
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

			updateSuggestionsList: function($smartSearch, data) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var $container = pluginData.suggestionContainer;
				var $searchInput = pluginData.searchInput;
				var hoverClass = pluginData.suggestionHoverClass;

				/* Resize the suggestion list in case the input size/position has changed. */
				methods.private.resizeSuggestionsList($smartSearch);

				/* Make sure the suggestions container is in its default state. */
				methods.private.resetSuggestionsList($smartSearch);

				/* Only operate if there was some data received. */
				if (data.length > 0) {

					/* Loop over the data and add options to the suggestion list. */
					for (var i in data) {

						var $valueLabel = $('<span>')
							.text(data[i].value)
							.addClass('value-label');

						var $fieldLabel = $('<span>')
							.text(data[i].label)
							.addClass('field-label');

						var $clearFix = $('<span>')
							.css('clear', 'both');

						/* Create the suggestion. */
						var $suggestion = $('<div>')
							.addClass('wnt-suggestion')
							.data('value', data[i].value)
							.data('field', data[i].field)
							.append($valueLabel)
							.append($fieldLabel)
							.append($clearFix)
							.appendTo($container)
							.on('click', methods.private.eventHandler.suggestionClick)
							.on('mouseover', {$smartSearch:$smartSearch}, methods.private.eventHandler.suggestionMouseover)
							.on('wntSelect', {$smartSearch:$smartSearch}, methods.private.eventHandler.suggestionOnWntSearch);

					}

					/* Highlight the first item in the suggestion list. */
					$container.children(".wnt-suggestion:first").addClass(hoverClass);

					methods.private.showSuggestionsList($smartSearch);

				} else {

					/* No data was received so put the suggestion container back in the default state. */
					methods.private.resetSuggestionsList($smartSearch);

				}

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

				/* Listen to each field for changes. */
				for (var i=0,l=pluginData.fields.length; i<l; i++) {
					var $field = $form.find('input[name="' + pluginData.fields[i] + '"]');

					$field.on('change', {$smartSearch:$smartSearch}, methods.private.eventHandler.fieldChange);

				}

				// Add event handlers to the search input element.
				$searchInput
				.on('keydown', {$smartSearch:$smartSearch}, methods.private.eventHandler.searchInputKeydown)
				.on('keyup', {$smartSearch:$smartSearch}, methods.private.eventHandler.searchInputKeyup)
				.on('blur', {$smartSearch:$smartSearch}, methods.private.eventHandler.searchInputBlur)
				.on('focus', {$smartSearch:$smartSearch}, methods.private.eventHandler.searchInputFocus);

				$suggestions
				.on('mouseover', {$smartSearch:$smartSearch}, methods.private.eventHandler.suggestionContainerMouseover)
				.on('mouseout', {$smartSearch:$smartSearch}, methods.private.eventHandler.suggestionContainerMouseout);

				$form.on('reset', function(){
					methods.private.removeAllValues($smartSearch);
				});

			},

			getFieldNameFromFieldMap: function($smartSearch, field) {
				var fieldMap = $smartSearch.data(stateKey).fieldMap || {};

				return fieldMap[field] || field;

			},

			suggestionsVisibleWithResults: function($smartSearch) {
				var $suggestionContainer = $smartSearch.data(stateKey).suggestionContainer;
				var isVisible = $suggestionContainer.is(':not(:hidden)');
				var hasResults = $suggestionContainer.find('.wnt-suggestion').length > 0;

				return isVisible && hasResults;

			},

			widestElementWidth: function($elements) {
				var width = 0;

				$elements.each(function(){
					var elementWidth = $(this).outerWidth();
					width = (elementWidth > width) ? elementWidth : width;
				});

				return width;

			},

			/**
			 * This function takes input and displays suggestions.
			 * @param  {jQuery}  $smartSearch  The jQuery selection object the plugin is operating on.
			 * @param  {String}  term  The search that should be used to retrieve suggestions.
			 */
			input: function($smartSearch, term) {
				var pluginData = $smartSearch.data(stateKey);
				var $container = pluginData.suggestionContainer;

				// If there was already a request in progress abort it.
				if (pluginData.xhr || null !== null && plugin.xhr.readyState != 4) {
					pluginData.xhr.abort();
				}

				// Only operate on the input data if the length is greater than or equal to the
				// min as defined in the plugin options.
				if (term.length >= pluginData.minLength) {

					var data = {
						term:term
					};

					if (pluginData.searchField) {
						data.field = pluginData.searchField;
					}

					pluginData.xhr = $.ajax({
						url: pluginData.ajaxUrl,
						data: { action:pluginData.ajaxAction, data:data },
						dataType: 'jsonp',
						context: $smartSearch, // Make the context of this request the smart search element.
						beforeSend: function(){methods.private.showSearchingMessage(this);}
					})
					.done(function(data){methods.private.updateSuggestionsList(this, data);})
					.always(function(data){methods.private.hideSearchingMessage(this);});

					// Save any altered state data back to the data object.
					$smartSearch.data(stateKey, pluginData);

				} else {

					// Since we are not retrieving any suggestions reset the suggestion list.
					methods.private.resetSuggestionsList($smartSearch);

				}

			},

			/**
			 * This function adds a value to the selected values list.
			 * @param String value The value to be added to the selected value list.
			 * @param String field The name of the field (if any) to add the value to.
			 * @return jQuery The jQuery selection object the plugin is operating on.
			 */
			addValue: function($smartSearch, value, field, isSilent) {
				isSilent = (typeof isSilent !== 'undefined') ? isSilent : false;

				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var $form = $($smartSearch[0].form);
				var $searchInput = pluginData.searchInput;
				var fieldInputChanged = false;

				// Retrieve the field name from the field map if necessary.
				field = methods.private.getFieldNameFromFieldMap($smartSearch, field);

				// Get the input field element based on the input name.
				var $fieldInput = $form.find('input[name="' + field + '"]');

				// Create a hidden input for the field if one doesn't already exist.
				if ($fieldInput.length === 0) {
					$fieldInput = $('<input>').attr({name:field,type:'hidden'}).appendTo($form);
				}

				// Append the new value to the field input if it isn't already there.
				var data = $fieldInput.val().split(',');
				if ($.inArray(value, data) == -1) {
					fieldInputChanged = true;
					data.push(value);
				}

				// Update the field input with the revised value.
				$fieldInput.val(data.join(',').replace(/(^,)|(,$)/g, ""));

				// Create a new list item (value item) to be added to the input control.
				var $item = $('<span>')
					.data('value', value)
					.data('field', field)
					.addClass('wnt-ss-value')
					.css('display', 'inline-block')
					.on('wntRemove', function(){
						var $item = $(this);
						var v = $item.data('value');
						var f = $item.data('field');

						methods.private.removeValue($smartSearch, v, f);

					});

				// Create a label for the new list item (value item) and add it to the list item.
				var $valueLabel = $('<span>')
					.text(value)
					.appendTo($item);

				// Create a close button for the new list item (value item) and add it to the list item.
				var $closeButton = $('<span>')
					.addClass('wnt-close-btn')
					.addClass('icon')
					.addClass('icon-close')
					.html('<span>x</span>')
					.appendTo($item)
					.click(function(){
						var $item = $(this).parent();
						$item.trigger('wntRemove');
					});

				// Add the new value item to the DOM immediately before the input field.
				$item.insertBefore($searchInput.parent());

				methods.private.resizeSuggestionsList($smartSearch);

				if (!isSilent) {

					// Since the input values have changed trigger an event to update the smart search.
					$smartSearch.trigger('wntSmartSearchUpdated');

					// If the added value was not previously in the value list trigger a change event on the input.
					if (fieldInputChanged) {
						var changeEvent = jQuery.Event("change");
						changeEvent.relatedTarget = $smartSearch[0];
						$fieldInput.trigger(changeEvent);
					}

				}

			},

			/**
			 * This function removes a value from the selected values list.
			 * @param  String value The value to be removed from the list if it exists.
			 * @param  String field The name of the field (if any) to remove the value from.
			 * @return jQuery The jQuery selection object the plugin is operating on.
			 */
			removeValue: function($smartSearch, value, field) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var $form = $($smartSearch[0].form);
				var fieldInputChanged = false;

				// Retrieve the field name from the field map if necessary.
				field = methods.private.getFieldNameFromFieldMap($smartSearch, field) || field;

				// Get the input field element based on the input name.
				var $fieldInput = $form.find('input[name="' + field + '"]');

				// If there is no input for the field do nothing (early exit).
				if ($fieldInput.length === 0) {
					return;
				}

				// Remove all instances of the value from the field input.
				var data = $.grep($fieldInput.val().split(','), function(val) {
					fieldInputChanged = (val == value) ? true : fieldInputChanged;
					return val != value;
				});

				// Update the field input with the revised value.
				$fieldInput.val(data.join(',').replace(/(^,)|(,$)/g, ""));

				// Loop over each list item in the input control.
				$list.children('.wnt-ss-value').each(function(){
					var $item = $(this);
					var v = $item.data('value') || '';
					var f = $item.data('field') || '';

					// If the list item is a value item remove it.
					if (v == value && (field && f == field)) {
						$item.remove();
					}

				});

				methods.private.resizeSuggestionsList($smartSearch);


				// Trigger an update of the smart search.
				$smartSearch.trigger('wntSmartSearchUpdated');

				// If a value was actually removed trigger a change even on the input field.
				if (fieldInputChanged) {
					var changeEvent = jQuery.Event("change");
					changeEvent.relatedTarget = $smartSearch[0];
					$fieldInput.trigger(changeEvent);
				}

			},

			/**
			 * This function loops over all values in the value list and removes them.
			 * @return jQuery The jQuery selection object the plugin is operating on.
			 */
			removeAllValues: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;

				// Loop over each list item in the input control.
				$list.children('.wnt-ss-value').each(function(){
					var $item = $(this);
					var v = $item.data('value') || '';
					var f = $item.data('field') || '';

					// If the list item is a value item remove it.
					if (v !== '' && f !== '') {
						methods.private.removeValue($smartSearch, v, f);
					}

				});

			},

			/**
			 * This function removes the last item from the values list.
			 * @return jQuery The jQuery selection object the plugin is operating on.
			 */
			removeMostRecentValue: function($smartSearch) {
				var $container = $smartSearch.data(stateKey).listContainer || $('<span>');

				$container.children('.wnt-ss-value:last').trigger('wntRemove');

			},

			showSuggestionsList: function($smartSearch) {
				var $container = $smartSearch.data(stateKey).suggestionContainer.show();
				var widestFieldLabel = methods.private.widestElementWidth($container.find('.field-label'));
				var remainingContainerWidth = $container.find('.wnt-suggestion:first').width() - widestFieldLabel;

				$container.find('.value-label').each(function(){
					var $label = $(this);

					if ($label.width() > remainingContainerWidth - 10) {
						$label.width(remainingContainerWidth - 10);
					}

				});

			},

			hideSuggestionsList: function($smartSearch) {
				$smartSearch.data(stateKey).suggestionContainer.hide();
			},

			resetSuggestionsList: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.suggestionContainer;
				var $suggestions = $list.children('.wnt-suggestion');

				methods.private.hideSuggestionsList($smartSearch);
				$suggestions.remove();

			},

			moveSuggestionSelectUp: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $suggestions = pluginData.suggestionContainer;
				var $selectedOption = $suggestions.find('.wnt-suggestion.wnt-hover');
				var $prev = $selectedOption.prev('.wnt-suggestion');

				if ($prev.length) {
					$prev.addClass('wnt-hover');
					$selectedOption.removeClass('wnt-hover');
				}

			},

			moveSuggestionSelectDown: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $suggestions = pluginData.suggestionContainer;
				var $selectedOption = $suggestions.find('.wnt-suggestion.wnt-hover');
				var $next = $selectedOption.next('.wnt-suggestion');

				if ($next.length) {
					$next.addClass('wnt-hover');
					$selectedOption.removeClass('wnt-hover');
				}

			},

			selectHighlightedSuggestion: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $suggestions = pluginData.suggestionContainer;
				var $selectedOption = $suggestions.find('.wnt-suggestion.wnt-hover');

				$selectedOption.trigger('wntSelect');

			},

			showSearchingMessage: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);

				pluginData.suggestionContainer.children('.wnt-message-searching').show();
				methods.private.showSuggestionsList($smartSearch);

			},

			hideSearchingMessage: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);

				pluginData.suggestionContainer.children('.wnt-message-searching').hide();

			},

			refreshExistingValues: function($smartSearch, e, isSilent) {
				isSilent = (typeof isSilent !== 'undefined') ? isSilent : false;

				var $form = $($smartSearch[0].form);
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var fieldValues = {};
				var valueButNoItem = [];
				var itemButNoValue = [];

				for (var i=0,l=pluginData.fields.length; i<l; i++) {
					var $field = $form.find('input[name="' + pluginData.fields[i] + '"]');

					if ($field.length !== 0) {
						var values = $.trim($field.val()).split(',');

						fieldValues[pluginData.fields[i]] = values;

						for (var ii=0,ll=values.length; ii<ll; ii++) {
							var field = pluginData.fields[i];
							var value = values[ii];

							if ($.trim(values[ii]) !== '') {
								valueButNoItem.push({field:field, value:value});
							}

						}

					}

				}

				$list.find('span.wnt-ss-value').each(function(){
					var $item = $(this);
					var vMap = {
						field : $item.data('field'),
						value : $item.data('value')
						};

					for (var i=0,a=[],l=valueButNoItem.length; i<l; i++) {
						var valueSet = valueButNoItem[i];
						if (valueSet.field!=vMap.field || valueSet.value!=vMap.value) {
							a.push(valueSet);
						}
					}
					valueButNoItem = a;

					if (!fieldValues[vMap.field] || $.inArray(vMap.value, fieldValues[vMap.field]) == -1) {
						itemButNoValue.push(vMap);
					}

				});

				/* Loop over values with no item and create items. */
				for (var i2=0,l2=valueButNoItem.length; i2<l2; i2++) {
					methods.private.addValue($smartSearch, valueButNoItem[i2].value, valueButNoItem[i2].field, isSilent);
				}

				/* Loop over items with no value and remove items. */
				for (var i3=0,l3=itemButNoValue.length; i3<l3; i3++) {
					methods.private.removeValue($smartSearch, itemButNoValue[i3].value, itemButNoValue[i3].field);
				}

			},

			resizeSuggestionsList: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $list = pluginData.listContainer;
				var $container = pluginData.suggestionContainer;
				var listPosition = $list.offset();

				// toggle input placeholder (prior to container positioning calculations)
				methods.private.toggleInputPlaceholder($smartSearch);

				var top = $list.innerHeight() + 1; //accounting for border

				var left = 0;

				var width = $list.outerWidth() +
					parseInt($list.css('margin-left')) +
					parseInt($list.css('margin-right'));

				/* Update the CSS for the suggestion container in case things have changed. */
				$container.css({top:top,left:left,width:width});

			},

			toggleInputPlaceholder: function($smartSearch) {
				var pluginData = $smartSearch.data(stateKey);
				var $searchInput = pluginData.searchInput;
				var $list = pluginData.listContainer;
				var numcriteria = $list.children('.wnt-ss-value').children().length;

				// if no smartsearch criteria selected, show placeholder
				if (numcriteria === 0){
					$(".wnt-smart-search-input").css ('width', '100%');
					$searchInput
						.attr('placeholder', $smartSearch.attr('placeholder'))
						.css ('width', '100%');
				// otherwise remove placeholder
				} else {
					$(".wnt-smart-search-input").css ('width', 0);
					$searchInput
						.attr('placeholder', '')
						.css ('width', 'auto');
				}

			},



			eventHandler: {

				searchInputKeydown: function(event) {
					var $searchInput = $(this);
					var $smartSearch = event.data.$smartSearch;

					switch (event.keyCode) {

						case 9:
						case 16:
						case 17:
						case 18:
						case 19:
						case 20:
						case 33:
						case 34:
						case 35:
						case 36:
						case 37:
						case 39:
						case 45:
						case 91:
						case 92:
						case 93:
						case 112:
						case 113:
						case 114:
						case 115:
						case 116:
						case 117:
						case 118:
						case 119:
						case 120:
						case 121:
						case 122:
						case 123:
						case 144:
						case 145:
						case 186:
						case 187:
						case 188:
						case 189:
						case 192:
							return true;

						case 13: // Enter
							if ($searchInput.val() !== '' && methods.private.suggestionsVisibleWithResults($smartSearch)) {
								event.preventDefault();
								methods.private.selectHighlightedSuggestion($smartSearch);
								return false;
							}
							break;

						case 8: // Shift
							if ($searchInput.val() === '') {
								methods.private.removeMostRecentValue($smartSearch);
							}
							break;

						case 40: // Down Arrow
						case 38: // Up Arrow
							event.preventDefault();
							return false;

					}

				},

				searchInputKeyup: function(event) {
					var $searchInput = $(this);
					var $smartSearch = event.data.$smartSearch;
					var $container = $searchInput.parent().parent();
					var $form = $($smartSearch[0].form);
					var newWidth = ($searchInput.val().length + 1) * 8;
					var maxWidth = $container.innerWidth() - 30;

					// If the original input has name "q" (primary open text search) perform the same
					// event on it.
					if ($smartSearch.attr('name') == 'q') {
						$smartSearch.trigger(event);
					}


					// As the user is typing update the size of the input to optimize how it fits
					// within the smart search container.
					if (newWidth < maxWidth) {
						$searchInput.width(newWidth);
					}
					else {
						$searchInput.width(maxWidth);
					}

					switch (event.keyCode) {

						case 9:
						case 16:
						case 17:
						case 18:
						case 19:
						case 20:
						case 33:
						case 34:
						case 35:
						case 36:
						case 37:
						case 39:
						case 45:
						case 91:
						case 92:
						case 93:
						case 112:
						case 113:
						case 114:
						case 115:
						case 116:
						case 117:
						case 118:
						case 119:
						case 120:
						case 121:
						case 122:
						case 123:
						case 144:
						case 145:
						case 186:
						case 187:
						case 188:
						case 189:
						case 192:
							return true;


						case 27: // Escape
							methods.private.hideSuggestionsList($smartSearch);
							return false;
							// break;

						case 13: // Enter
							if (methods.private.suggestionsVisibleWithResults($smartSearch)) {
								event.preventDefault();
								return false;
							} else {
								$form.submit();
								return true;
							}
							break;

						case 40: // Down Arrow
							event.preventDefault();
							methods.private.moveSuggestionSelectDown($smartSearch);
							return false;

						case 38: // Up Arrow
							event.preventDefault();
							methods.private.moveSuggestionSelectUp($smartSearch);
							return false;

						case 8: // Backspace
							methods.private.input($smartSearch, $searchInput.val());
							// toggle smartsearch input placeholder on if all input data removed
							methods.private.toggleInputPlaceholder($smartSearch);
							return true;

						default:
							methods.private.input($smartSearch, $searchInput.val());
							break;

					}

				},

				searchInputBlur: function(event) {
					var $smartSearch = event.data.$smartSearch;
					var pluginData = $smartSearch.data(stateKey);
					var $list = pluginData.listContainer;

					$list.removeClass('focus');

					if (!pluginData.overSuggestions) {
						methods.private.hideSuggestionsList($smartSearch);
						// $searchInput.val('');
					}

				},

				searchInputFocus: function(event){
					var $smartSearch = event.data.$smartSearch;
					var pluginData = $smartSearch.data(stateKey);
					var $list = pluginData.listContainer;

					$list.addClass('focus');
					methods.private.resetSuggestionsList($smartSearch);

				},

				suggestionContainerMouseover: function(event) {
					var $smartSearch = event.data.$smartSearch;
					var pluginData = $smartSearch.data(stateKey);

					pluginData.overSuggestions = true;
					$smartSearch.data(stateKey, pluginData);

				},

				suggestionContainerMouseout: function(event) {
					var $smartSearch = event.data.$smartSearch;
					var pluginData = $smartSearch.data(stateKey);
					var $searchInput = pluginData.searchInput;

					pluginData.overSuggestions = false;
					$smartSearch.data(stateKey, pluginData);

					if (!$searchInput.is(':focus')) {
						methods.private.hideSuggestionsList($smartSearch);
					}

				},

				suggestionClick: function(event) {
					var $suggestion = $(this);

					$suggestion.trigger('wntSelect');

				},

				suggestionMouseover: function(event) {
					var $suggestion = $(this);
					var $smartSearch = event.data.$smartSearch;
					var hoverClass = $smartSearch.data(stateKey).suggestionHoverClass;

					// Add the hover class to the suggestion that the mouse if over.
					$suggestion.addClass(hoverClass);

					// Remove the hover class from any other suggestions that have it.
					$suggestion.siblings('.' + hoverClass).removeClass(hoverClass);

				},

				suggestionOnWntSearch: function(event) {
					var $suggestion = $(this);
					var $smartSearch = event.data.$smartSearch;
					var pluginData = $smartSearch.data(stateKey);
					var $searchInput = pluginData.searchInput;
					var v = $suggestion.data('value');
					var f = $suggestion.data('field');

					$searchInput.val('');

					methods.private.resetSuggestionsList($smartSearch);
					methods.private.addValue($smartSearch, v, f);

					$smartSearch.trigger('wntFocus');

				},

				fieldChange: function(event) {
					var $smartSearch = event.data.$smartSearch;

					/* If there was a change and "I" didn't make it take action. */
					if (event.relatedTarget != $smartSearch[0]) {
						methods.private.refreshExistingValues($smartSearch, event);
					}

				}

			}

		}

	};

	$.fn[wntPlugin] = function(method)
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
