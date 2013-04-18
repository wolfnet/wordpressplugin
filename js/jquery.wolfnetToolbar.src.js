
/**
 * Plugin for pagination tools and results toolbar.
 * Results toolbar contains:
 * 		-Sort dropdown
 *  	-Results count display (i.e. "Results 1-XX of XXXX")
 *		-Show XX per page dropdown
 *
 * Pagination tools contains Previous/Next (only if enabled via admin)
 */

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {


		var priceFormatter = function ( number )
		{
			var number = number.toString();
			var dollars = number.split('.')[0];
			var dollars = dollars
				.split('')
				.reverse()
				.join('')
				.replace(/(\d{3}(?!$))/g, '$1,')
				.split('')
				.reverse()
				.join('');

			return '$' + dollars;

		} // end method priceFormatter


		$.fn.wolfnetToolbar = function ( options ) {

			var defaultOptions = {
				usesPagination  : false,
				page            : 1,
				startrow        : 1,
				numrows         : 20,
				sort            : '',
				total_rows      : 250,
				max_results     : 250,
				criteria        : {},
				showSortOptions : false
				};
			var options = $.extend( defaultOptions, options );
			var previewLimitCount;
			var datakey = 'wolfnetToolbarData';


			var getLastPageNum = function ( numrows, rowcount )
			{
				var maxrows;

				if (rowcount < previewLimitCount) {
					maxrows = rowcount;
				}
				else {
					maxrows = previewLimitCount;
				}

				return Math.ceil( Number(maxrows) / Number(numrows) );

			} // end method getLastPageNum


			var renderToolbar = function ()
			{

				var options = $(this).data( datakey );
				var state   = $(this).data('state');

				// Create the toolbar container.
				var $toolbar = $('<div>').addClass('wolfnet_toolbar');

				// If pagination is enabled include the controls in the toolbar.
				if ( options.usesPagination !== false && options.total_rows > options.numrows ) {
					$toolbar.append( renderPaginationControls.call( this ) );
					$toolbar.addClass('wolfnet_withPagination');
				}

				// if sorting options are enabled include the controls in the toolbar.
				if ( options.showSortOptions === true ) {

					// if there are are already pagination controls add this control to them instead of the toolbar.
					if ( $toolbar.find('.wolfnet_page_info').length > 0 ) {
						$toolbar.find('.wolfnet_page_info').append( renderSortControls.call( this ) );
					}
					else {
						$toolbar.append( renderSortControls.call( this ) );
					}

					$toolbar.addClass('wolfnet_withSortOptions');

				}

				return $toolbar;

			}


			var renderPaginationControls = function ()
			{

				var $listingContainer = $(this);
				var state   = $(this).data('state');

				// An array to hold the controls.
				var controls = [];

				// Create the "Previous" button/link.
				var $nextBtn = $('<a title="Previous Page" href="javascript:;">')
					.addClass('wolfnet_page_nav wolfnet_page_nav_prev')
					.html('<span>Previous</span>')
					.click( function ( event ) {
						$listingContainer.trigger('wolfnet.prevPage', [this]);
					} );

				controls.push($nextBtn[0]);

				// Create the page info section of the toolbar. item range and number of items per page.
				var $pageInfo = $('<span>')
					.addClass('wolfnet_page_info')
					.append( function () {
						return $('<span>')
							.addClass('wolfnet_page_items')
							.append( $('<span>').addClass('wolfnet_page_start').text(state.startrow) )
							.append( '-' )
							.append( $('<span>').addClass('wolfnet_page_end').text(state.numrows) )
							.append( ' of ' )
							.append( $('<span>').addClass('wolfnet_page_total').text(state.max_results) )
					} )
					.append( function () {
						return $('<span>')
							.addClass('wolfnet_page_items_select')
							.append( function () {
								var $select = $('<select>');
								$select.change( function () {
									$listingContainer.trigger('wolfnet.itemsPerPage', [$(this).val(), this] );
								} );
								$select.append( loadPageOptions.call( $listingContainer ) );
								return $select;
							} )
							.append('per page');
					} );

				controls.push($pageInfo[0]);

				// Create the "Next" button/link.
				var $prevBtn = $('<a title="Next Page" href="javascript:;">')
					.addClass('wolfnet_page_nav wolfnet_page_nav_next')
					.html('<span>Next</span>')
					.click( function () {
						$listingContainer.trigger('wolfnet.nextPage', [this]);
					} );

				controls.push($prevBtn[0]);

				return controls;

			}


			var renderSortControls = function ()
			{

				var $listingContainer = $(this);

				return $('<span>')
					.addClass('wolfnet_sortoptions')
					.append( function () {
						var $select = $('<select>')
						$select.change( function ( event ) {
							$listingContainer.trigger('wolfnet.sortChange', [$(this).val(), this] );
						} );
						$select.append( loadSortOptions.call( $listingContainer ) );
						return $select;
					} );

			}


			var loadPageOptions = function ()
			{
				// This method could easily be refactored to pull the data in statically rather than with Ajax.

				// Wait until the toolbars have been added to the DOM.
				$(this).on("wolfnet.toolbarsRendered", function () {

					var $select = $(this).find('.wolfnet_page_items_select select');
					var state   = $(this).data('state');

					$.ajax( {
						url      : '?pagename=wolfnet-get-showNumberOfListings-dropdown',
						dataType : 'json'
					} )
					.done( function ( data ) {

							// Clear out any existing options.
							$select.children().remove();

							// If the 'default' value is not in the data set we need to add it.
							if ( $.inArray( state.numrows ) == -1 ) {
								var newData = [];
								var defaultUsed = false;
								for ( var i=0; i<data.length; i++ ) {
									if ( !defaultUsed && data[i] > state.numrows ) {
										newData[newData.length] = state.numrows;
										defaultUsed = true;
									}
									newData[newData.length] = data[i];
								}
								data = newData;
							}

							// Add an option to the select element for each item in the array.
							for ( var key=0; key<data.length; key++ ) {
								var $option = $('<option>', {value:data[key],text:data[key]} );
								if ( data[key] == state.numrows ) {
									$option.attr( 'selected', 'selected' );
								}
								$select.append( $option );
							}

					} );

				} );

				return '';

			}


			var loadSortOptions = function ()
			{
				// This method could easily be refactored to pull the data in statically rather than with Ajax.

				// Wait until the toolbars have been added to the DOM.
				$(this).on("wolfnet.toolbarsRendered", function () {

					var $select = $(this).find('.wolfnet_sortoptions select');
					var state   = $(this).data('state');

					$.ajax( {
						url      : '?pagename=wolfnet-get-sortOptions-dropdown',
						dataType : 'json'
					} )
					.done( function ( data ) {

							// Clear out any existing options.
							$select.children().remove();

							for ( var key=0; key<data.length; key++ ) {
								$select.append(
									$('<option>', {value:data[key].value,text:data[key].label} )
								);
							}

					} );

				});

				return '';

			}


			var loadDataEventHandler = function ( event, target )
			{
				var $container = $( this );
				var state      = $container.data('state');

				// If data is not already be refreshed attempt to do so.
				if ( !state.refreshing ) {
					var options = $container.data(datakey);
					var data    = $.extend( state, options.criteria );

					state.startrow = ( Number(state.numrows) * (Number(state.page) - 1 ) ) + 1;

					data.ownerType = options.ownerType;

					// Make Ajax call to retrieve data.
					$.ajax( {
						url      : '?pagename=wolfnet-listings-get',
						dataType : 'json',
						data     : data,
						beforeSend : function () {
							state.refreshing = true;
							$container.find('.wolfnet_listings').addClass('wolfnet_refreshing');
						}
					} )
					.done( function ( data ) {
						// Notify the container that the data has been loaded and pass the data to any handlers.
						$container.trigger( 'wolfnet.dataLoaded', [data,target] );
					} )
					.always( function () {
						state.refreshing = false;
						$container.find('.wolfnet_listings').removeClass('wolfnet_refreshing');
					} );

				}

			}


			var prevPageEventHandler = function ( event, target )
			{
				var state = $(this).data('state');
				var prevPage = state.page - 1;

				// if there is a prev page update state data
				if ( !state.refreshing && prevPage > 0 ) {
					state.page = prevPage;
					$(this).data( 'state', state );

					// trigger a data refresh.
					$(this).trigger('wolfnet.refreshData', target);
				}

			}


			var nextPageEventHandler = function ( event, target )
			{
				var state = $(this).data('state');
				var lastPage = getLastPageNum( state.numrows, state.total_rows );
				var nextPage = state.page + 1;

				// if there is a next page update state data
				if ( !state.refreshing && nextPage <= lastPage ) {
					state.page = nextPage;
					$(this).data( 'state', state );

					// trigger a data refresh.
					$(this).trigger('wolfnet.refreshData', [target] );
				}

			}


			var itemsPerPageEventHandler = function ( event, value, target )
			{
				var state = $(this).data('state');

				// if the value is acceptable update state data
				if ( !state.refreshing && value != state.total_rows ) {

					state.numrows = value;
					state.page = 1;
					state.startrow = 1;
					$(this).data( 'state', state );

					// update all related input controls
					$(this).find('.wolfnet_page_items_select select').val(value);

					// trigger a data refresh.
					$(this).trigger('wolfnet.refreshData', [target]);

				}

			}


			var sortChangeEventHandler = function ( event, value, target )
			{
				var state = $(this).data('state');

				// if the value is acceptable update state data
				if ( !state.refreshing && value != state.sort ) {

					state.sort = value;
					$(this).data( 'state', state );

					// update all related input controls
					$(this).find('.wolfnet_sortoptions select').val(value);

					// trigger a data refresh.
					$(this).trigger('wolfnet.refreshData', [target]);

				}

			}


			var dataLoadedEventHandler = function ( event, data, target )
			{
				var $container = $(this);
				var state      = $container.data('state');
				var startrow   = state.startrow;
				var numrows    = state.numrows;
				var page       = state.page;
				var totalRows  = state.total_rows;
				var sortBy     = state.sort;

				// Clear pre-existing items
				$container.find('.wolfnet_listing').remove();
				$container.find('.wolfnet_clearfix').remove();

				// Render Listing Grid Items
				if ( $container.hasClass('wolfnet_listingGrid') ) {
					buildListingGrid.call( $container, data );
				}
				// Render Property List Items
				else if ( $container.hasClass('wolfnet_propertyList') ) {
					buildListingGrid.call( $container, data );
				}

				// Update results count display
				var rowcountDisplay = (Number(startrow) - 1) + Number(numrows);
				if (rowcountDisplay > previewLimitCount) {
					rowcountDisplay = previewLimitCount;
				}

				// Update page information
				$container.find('.wolfnet_page_start').text(startrow);
				$container.find('.wolfnet_page_end').text(rowcountDisplay);

				// clear show # select's options' selected attributes and update
				$container.find('.wolfnet_page_items_select select').val( numrows );

				// clear the sort option's selected attributes and update
				$container.find('.wolfnet_sortoptions select').val( sortBy );

				$(this).trigger('wolfnet.listingsRendered',[target]);

			}


			var buildListingGrid = function ( data )
			{
				var $container = $(this);
				var $listings  = $container.find('.wolfnet_listings:first').clone();
				var state      = $container.data('state');
				var startrow   = state.startrow;
				var numrows    = state.numrows;
				var page       = state.page;
				var totalRows  = state.total_rows;
				var sortBy     = state.sort;

				//START:  loop to rebuild listing grid dom (listingGrid uses listingSimple.php template)
				for (var i=0; i<data.length; i++) {

					var brokerLogo  = data[i].branding.brokerLogo  || null;
					var brokerName  = data[i].branding.content || null;
					var cityState   = data[i].city + ', ' + data[i].state;
					var fullAddress = data[i].display_address + ', ' + cityState;
					var hasBranding = ( brokerLogo == null && brokerName == null ) ? false : true ;

					var listingEntity = $('<div>')
						.addClass('wolfnet_listing')
						.addClass( (hasBranding) ? 'wolfnet_branded' : '' )
						.attr('id','wolfnet_listing_'+data[i].property_id)
						.appendTo($listings);

					var link = $('<a>')
						.attr('href',data[i].property_url)
						.appendTo(listingEntity);

					var listingImageSpan = $('<span>')
						.addClass('wolfnet_listingImage')
						.appendTo(link);

					var listingImgSrc = $('<img>')
						.attr('src',data[i].thumbnail_url)
						.appendTo(listingImageSpan);

					var price = $('<span>')
						.addClass('wolfnet_price')
						.attr('itemprop','price')
						.html( priceFormatter(data[i].listing_price) )
						.appendTo(link);

					var bedbath = $('<span>')
						.addClass('wolfnet_bed_bath')
						.attr('title',data[i].bedrooms+' Bedrooms & '+data[i].bathroom+' Bathrooms')
						.html( data[i].bedrooms + 'bd/' + data[i].bathroom + 'ba' )
						.appendTo(link);

					var location = $('<span>')
						.attr('title',fullAddress)
						.append(
							$('<span>').addClass('wolfnet_location')
								.attr('itemprop','locality')
								.html(cityState)
						)
						.append(
							$('<span>').addClass('wolfnet_address')
							.html(data[i].display_address)
						)
						.append(
							$('<span>').addClass('wolfnet_full_address')
								.attr('itemprop','street_address')
								.css('display','none')
								.html(fullAddress)
						)
						.appendTo(link);

					if (hasBranding) {

						var branding = $('<div>')
							.addClass('wolfnet_branding')
							.insertAfter(location);

						if ( brokerLogo != null ) {

							$('<span>').addClass('wolfnet_brokerLogo')
								.append( $('<img>').attr('src',brokerLogo) )
								.appendTo(branding);

						}

						if ( brokerName != null ) {

							$('<span>').addClass('wolfnet_brandingMessage')
								.html(brokerName)
								.appendTo(branding);

						}

					}

				}//END: loop to rebuild listing grid dom

				$container.find('.wolfnet_listings:first').replaceWith( $listings );

				$container.wolfnetListingGrid('reload');

			}


			var buildPropertyList = function ( data )
			{
				var $container = $(this);
				var $listings  = $container.find('.wolfnet_listings:first').clone();
				var state      = $container.data('state');
				var startrow   = state.startrow;
				var numrows    = state.numrows;
				var page       = state.page;
				var totalRows  = state.total_rows;
				var sortBy     = state.sort;

				//START:  rebuild property list dom (propertyList uses listingBrief.php)
				//loop listings in data object and build new listing entity to append to dom
				for ( var i=0; i<data.length; i++ ) {

					var cityState   = data[i].city + ', ' + data[i].state;
					var fullAddress = data[i].display_address + ', ' + cityState;

					var listingEntity = $('<div>')
						.addClass('wolfnet_listing')
						.attr('id','wolfnet_listing_' + data[i].property_id)
						.appendTo($listings);

					var link = $('<a>')
						.attr({'href':data[i].property_url,'title':fullAddress})
						.appendTo(listingEntity);

					var location = $('<span>')
						.addClass('wolfnet_full_address')
						.html(fullAddress)
						.appendTo(link);

					var price = $('<span>')
						.addClass('wolfnet_price')
						.attr('itemprop','price')
						.html( priceFormatter(data[i].listing_price) )
						.appendTo(location);

					var streetAddress = $('<span>')
						.attr('itemprop','street-address')
						.css('display','none')
						.html(fullAddress)
						.appendTo(price);

				}//END: rebuild property list DOM

				$container.find('.wolfnet_listings:first').replaceWith( $listings );

				$container.wolfnetPropertyList();

			}


			var listingsRenderedEventHandler = function ( event, target )
			{
				var $container = $(this);
				var state = $container.data('state');

				if ( state.page - 1 < 1 ) {
					$container.find('a.wolfnet_page_nav_prev').addClass('wolfnet_disabled');
				}
				else {
					$container.find('a.wolfnet_page_nav_prev').removeClass('wolfnet_disabled');
				}

				if ( state.page + 1 > getLastPageNum( state.numrows, state.total_rows ) ) {
					$container.find('a.wolfnet_page_nav_next').addClass('wolfnet_disabled');
				}
				else {
					$container.find('a.wolfnet_page_nav_next').removeClass('wolfnet_disabled');
				}

				// If the element that triggered the event was in the bottom toolbar scroll to the top of the page.
				if ( target != undefined && $(target).closest('.wolfnet_toolbar')[0] == $(this).find('.wolfnet_toolbarBottom')[0] ) {
					$('html,body').scrollTop( $(this).offset().top - 100 );
				}

			}


			return this.each( function () {

				var $listingContainer = $( this );

				$listingContainer.data( datakey, options );

				if ( options.total_rows > options.max_results ) {
					previewLimitCount = options.max_results;
				}
				else {
					previewLimitCount = options.total_rows;
				}

				var stateData = {
					page        : options.page,
					startrow    : options.startrow,
					numrows     : options.numrows,
					sort        : options.sort,
					ownerType   : options.ownerType,
					total_rows  : options.total_rows,
					max_results : previewLimitCount,
					refreshing  : false
					};

				$listingContainer.data( 'state', stateData );

				//only display toolbars if there are listings
				if ( options.total_rows > 0 ) {

					// Create events on the container.
					$listingContainer.bind( 'wolfnet.nextPage', nextPageEventHandler );
					$listingContainer.bind( 'wolfnet.prevPage', prevPageEventHandler );
					$listingContainer.bind( 'wolfnet.itemsPerPage', itemsPerPageEventHandler );
					$listingContainer.bind( 'wolfnet.sortChange', sortChangeEventHandler );
					$listingContainer.bind( 'wolfnet.refreshData', loadDataEventHandler );
					$listingContainer.bind( 'wolfnet.dataLoaded', dataLoadedEventHandler );
					$listingContainer.bind( 'wolfnet.listingsRendered', listingsRenderedEventHandler );

					// If appropriate render and add the toolbar.
					if ( options.usesPagination !== false || options.showSortOptions === true ) {

						var $toolbar = renderToolbar.call(this);

						// Add a copy of the toolbar to the top of the container.
						$listingContainer
							.find('.wolfnet_listings:first')
							.before( $toolbar.clone(true).addClass('wolfnet_toolbarTop') );

						// Add a copy of the toolbar to the bottom of the container.
						$listingContainer
							.find('.wolfnet_listings:first')
							.after( $toolbar.clone(true).addClass('wolfnet_toolbarBottom') );

						// The toolbars are loaded so trigger some events.
						$listingContainer.trigger('wolfnet.toolbarsRendered');
						$listingContainer.trigger('wolfnet.listingsRendered');

					}

				}

			} );


		} /*END: function $.fn.wolfnetToolbar*/

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */
