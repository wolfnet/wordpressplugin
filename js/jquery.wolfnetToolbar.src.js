
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
				var $nextBtn = $('<a>')
					.addClass('wolfnet_page_nav wolfnet_page_nav_prev')
					.html('<span>Previous</span>')
					.attr( { title:'Previous Page', href:'javascript:;' } )
					.click( function ( event ) {
						$listingContainer.trigger('wolfnet.prevPage', [this]);
					} );

				controls.push($nextBtn);

				// Create the page info section of the toolbar. item range and number of items per page.
				var $pageInfo = $('<span>')
					.addClass('wolfnet_page_info')
					.append( function () {
						return $('<span>')
							.addClass('wolfnet_page_items')
							.append( $('<span>').addClass('wolfnet_page_start').html(state.startrow) )
							.append( '-' )
							.append( $('<span>').addClass('wolfnet_page_end').html(state.numrows) )
							.append( ' of ' )
							.append( $('<span>').addClass('wolfnet_page_total').html(state.max_results) )
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

				controls.push($pageInfo);

				// Create the "Next" button/link.
				var $prevBtn = $('<a>')
					.addClass('wolfnet_page_nav wolfnet_page_nav_next')
					.html('<span>Next</span>')
					.attr( { title:'Next Page', href:'javascript:;'} )
					.click( function () {
						$listingContainer.trigger('wolfnet.nextPage', [this]);
					} );

				controls.push($prevBtn);

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

					var $select = $(this).find('.wolfnet_toolbar .wolfnet_page_items_select select');
					var state   = $(this).data('state');

					$.ajax( {
						url      : '?pagename=wolfnet-get-showNumberOfListings-dropdown',
						dataType : 'json'
					} )
					.done( function ( data ) {

							// Clear out any existing options.
							$select.children().remove();

							// An array to hold our option elements.
							var options = [];

							for ( var key in data ) {
								var $option = $('<option>', {value:data[key],text:data[key]} );
								if ( data[key] == state.numrows ) {
									$option.attr( 'selected', 'selected' );
								}
								options.push( $option );
							}

							$select.append(options);

					} );

				} );

				return '';

			}


			var loadSortOptions = function ()
			{
				// This method could easily be refactored to pull the data in statically rather than with Ajax.

				// Wait until the toolbars have been added to the DOM.
				$(this).on("wolfnet.toolbarsRendered", function () {

					var $select = $(this).find('.wolfnet_toolbar .wolfnet_sortoptions select');
					var state   = $(this).data('state');

					$.ajax( {
						url      : '?pagename=wolfnet-get-sortOptions-dropdown',
						dataType : 'json'
					} )
					.done( function ( data ) {

							// Clear out any existing options.
							$select.children().remove();

							// An array to hold our option elements.
							var options = [];

							for ( var key in data ) {
								options.push( $('<option>', {value:data[key].value,text:data[key].label} ) );
							}

							$select.append(options);

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
				var container = $(this);
				var state     = container.data('state');
				var startrow  = state.startrow;
				var numrows   = state.numrows;
				var page      = state.page;
				var totalRows = state.total_rows;
				var sortBy    = state.sort;

				// Clear pre-existing items
				container.find('.wolfnet_listing').remove();
				container.find('.wolfnet_clearfix').remove();

				// Render Listing Grid Items
				if ( container.hasClass('wolfnet_listingGrid') ) {

					//START:  loop to rebuild listing grid dom (listingGrid uses listingSimple.php template)
					for (var i=0; i<data.length; i++) {

						//check for branding
						var brokerLogo= data[i].branding.brokerLogo  || null;
						var brokerName= data[i].branding.content || null;

						if ( brokerLogo == null && brokerName == null ) {
							var hasBranding = false;
							var listingEntityClass = 'wolfnet_listing';
						}
						else {
							var hasBranding = true;
							var listingEntityClass = 'wolfnet_listing wolfnet_branded';
						}

						var listingEntity = $('<div>').addClass(listingEntityClass)
								  					  .attr('id','wolfnet_listing_'+data[i].property_id);
						container.find('.wolfnet_listings').append(listingEntity);

						var link = $('<a>').attr('href',data[i].property_url);
						listingEntity.append(link);

						var listingImageSpan = $('<span>').addClass('wolfnet_listingImage');
						var listingImgSrc = $('<img>').attr('src',data[i].thumbnail_url).appendTo(listingImageSpan);
						link.append(listingImageSpan);

						var price = $('<span>').addClass('wolfnet_price')
										       .attr('itemprop','price')
											   .text(priceFormatter(data[i].listing_price));
						listingImageSpan.after(price);

						var bedbath = $('<span>').addClass('wolfnet_bed_bath')
												 .attr('title',data[i].bedrooms+' Bedrooms & '+data[i].bathroom+' Bathrooms')
												 .text(data[i].bedrooms+'bd/'+data[i].bathroom+'ba');
						price.after(bedbath);

						var citystate = data[i].city + ', ' + data[i].state;
						var fullAdress = data[i].display_address + ', ' + citystate;
						var location = $('<span>').attr('title',fullAdress);
						$('<span>').addClass('wolfnet_location')
								   .attr('itemprop','locality')
								   .text(citystate)
								   .appendTo(location);
						$('<span>').addClass('wolfnet_address')
								   .text(data[i].display_address)
								   .appendTo(location);
						$('<span>').addClass('wolfnet_full_address')
								   .attr('itemprop','street_address')
								   .css('display','none')
								   .text(fullAdress)
								   .appendTo(location);
						bedbath.after(location);

						if (hasBranding) {
							var branding = $('<span>').addClass('wolfnet_branding');

							if ( brokerLogo != null ) {
								var imageSpan = $('<span>')
									.addClass('wolfnet_brokerLogo');
								var image = $('<img>')
									.attr('src',brokerLogo)
									.appendTo(imageSpan);
								imageSpan.appendTo(branding);
							}

							if ( brokerName != null ) {
								$('<span>').addClass('wolfnet_brandingMessage')
										   .text(brokerName)
										   .appendTo(branding);
							}

							location.after(branding);
						}
					}//END: loop to rebuild listing grid dom

					container.wolfnetListingGrid('reload');

				}
				// Render Property List Items
				else if ( container.hasClass('wolfnet_propertyList') ) {

					//START:  rebuild property list dom (propertyList uses listingBrief.php)
					//loop listings in data object and build new listing entity to append to dom
					for (var i=0; i<data.length; i++) {

						var listingEntity = $('<div>').addClass('wolfnet_listing')
								  					  .attr('id','wolfnet_listing_'+data[i].property_id);
						container.find('.wolfnet_listings').append(listingEntity);

						var citystate = data[i].city + ', ' + data[i].state;
						var fullAdress = data[i].display_address + ', ' + citystate;
						var link = $('<a>').attr({'href':data[i].property_url,'title':fullAdress});
						listingEntity.append(link);

						var location = $('<span>').addClass('wolfnet_full_address')
												 .text(fullAdress);
						link.append(location);

						var price = $('<span>').addClass('wolfnet_price')
										       .attr('itemprop','price')
											   .text(priceFormatter(data[i].listing_price));
						location.after(price);

						var streetAddress = $('<span>').attr('itemprop','street-address')
													   .css('display','none')
													   .text(fullAdress);
						price.after(streetAddress);

					}//END: rebuild property list dom

					container.wolfnetPropertyList();

				}

				// Update results count display
				var rowcountDisplay = (Number(startrow) - 1) + Number(numrows);
				if (rowcountDisplay > previewLimitCount) {
					rowcountDisplay = previewLimitCount;
				}

				// Update page information
				container.find('.wolfnet_page_start').html(startrow);
				container.find('.wolfnet_page_end').html(rowcountDisplay);

				// clear show # select's options' selected attributes and update
				container.find('.wolfnet_page_items_select select').val( numrows );

				// clear the sort option's selected attributes and update
				container.find('.wolfnet_sortoptions select').val( sortBy );

				$(this).trigger('wolfnet.listingsRendered',[target]);

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
