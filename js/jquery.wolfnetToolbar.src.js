
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

		//number of listings in a result set cannot be >250 (requirement)
		var listingLimit = 250;
		var previewLimitCount;
		var datakey = 'wolfnetToolbarData';	

		$.fn.wolfnetToolbar = function ( options ) {

			var options = $.extend( {usesPagination	: 	options.usesPagination,
									 page 			: 	1,
									 startrow		: 	1,
									 numrows        : 	options.numrows,
								 	 sort 			: 	'',
								 	 ownerType		: 	options.ownerType,
								 	 total_rows     :   options.total_rows,
								 	 max_results    :   listingLimit,
								 	 criteria 		:   {}
								 	}
								    ,options );	

			return this.each( function () {			

				$( this ).data( datakey , options );
				
				if (options.total_rows > listingLimit) {
					previewLimitCount = listingLimit;
				}
				else {
					previewLimitCount = options.total_rows;
				}

				$( this ).data('state' 
					         ,{ page 		: 	options.page,
					            startrow	: 	options.startrow,
							    numrows	    : 	options.numrows,
							    sort 		: 	options.sort,
							    ownerType	:   options.ownerType,
							    total_rows  :   options.total_rows,
							    max_results :   previewLimitCount					   
							});	

				var listingContainer = $( this );

				//Sort dropdown - build and insert to interface before & after listings
				var sortDropdown = renderSortDropdown.call( this );
				$( this ).find('h2.widget-title').after( sortDropdown.clone(true) );
				$( this ).append( sortDropdown.clone(true) );	

				//Pagination controls - build and insert to interface before & after listings
				if ( options.usesPagination == true && options.total_rows > options.numrows ) {
					var pagination = renderPaginationTools.call( this );
					$( this ).find('h2.widget-title').after( pagination.clone(true) );
					$( this ).append( pagination.clone(true) );
				}

				//logic to scroll to component header when bottom toolbar is used
				var scrollHandler = function () {
					$('html,body').scrollTop(listingContainer.offset().top);
				}

				var toolbars = $( this ).find('.wntToolbar');

				if ( options.usesPagination == true && options.total_rows > options.numrows ) {
					//adding scroll to bottom sort dropdown
					var toolbar = $(toolbars[2]);
					toolbar.find('select').change( scrollHandler);

					//adding scroll to bottom pagination toolbar
					toolbar = $(toolbars[3]);					
					toolbar.find('a').click( scrollHandler );
					toolbar.find('select').change( scrollHandler);
				} 
				else {			
					//adding scroll to bottom sort dropdown
					var toolbar = $(toolbars[1]);
					toolbar.find('select').change( scrollHandler );
				}

			});

		} /*END: function $.fn.wolfnetToolbar*/


		// Method to build out results toolbar
		var renderSortDropdown = function ( ) {
 
 			var container = $( this );
 			var options = container.data(datakey);
 			var state = container.data('state');
		
			var resultTools = $('<div>')
				.css( {'width':'100%','clear':'both'} )
				.addClass( 'wntSorting wntToolbar' );

			// Horizontal cells within toolbar div
			var cells = [];
			cells[0] = $('<div>').appendTo(resultTools)
								 .css( {'width':'99%','clear':'both','text-align':'left'} );

			//Build Sort By dropdown and append to first cell
			var sortByDropdown = $('<select>').addClass( 'wntSortoptions' )
				.change(function(event){
					state.sort = $(this).val();
					state.page = 1;
					state.startrow = 1;
					updateResultSetEventHandler.call(container, event);

				});

			$.ajax({ 
				url: '?pagename=wolfnet-get-sortOptions-dropdown',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.wntSorting' ).find( 'select.wntSortoptions' );
					select.empty();
					for ( var i=0; i<data.length; i++ ) {					
						$('<option>', {value:data[i]['value'],text:data[i]['label']}).appendTo( select );
					}
				}
			});
			$(sortByDropdown).appendTo(cells[0]);

			return resultTools;
		} //end method renderSortDropdown


		// Method to build out results toolbar
		var renderPaginationTools = function ( ) {

 			var container = $( this );
 			var options = container.data(datakey);
 			var state = container.data('state');

			var paginationToolbar = $('<div>').addClass('wntPagination wntToolbar')
											  .css( {'width':'100%','clear':'both'} );;

			//Build show # of listings dropdown and append to third cell
			var showDropdown = $('<select>').addClass( 'wntShowlistings' )
				.change(function(event){
					state.numrows = $(this).val();
					state.page = 1;
					state.startrow = 1;
					updateResultSetEventHandler.call(container, event);
				});

			$.ajax({ 
				url: '?pagename=wolfnet-get-showNumberOfListings-dropdown',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.wntPagination' ).find( 'select.wntShowlistings' );
					select.empty();

					$('<option>',{value:state.numrows,text:state.numrows})
						.addClass('showNum_'+state.numrows)
					    .appendTo(select)
						.attr('selected','selected');		

					for ( var key in data ) {
						if ( data[key] != state.numrows ) {
							$('<option>',{value:data[key],text:data[key]})
									    .addClass('showNum_'+data[key])
									    .appendTo( select );
						}
					}
				}
			});				
			var showPerPage = $(showDropdown).before('Show').after('per page');

			// Horizontal cells within pagination toolbar
			var cells = [];
			cells[0] = $('<div>').appendTo(paginationToolbar)
								 .css( {'width':'33%','float':'left','test-align':'left'} );
			cells[1] = $('<div>').appendTo(paginationToolbar)
							     .css( {'width':'33%','float':'left','text-align':'center'} );
			cells[2] = $('<div>').appendTo(paginationToolbar)
								 .css( {'width':'33%','float':'right','text-align':'right'} ); 

			//new horizontal cell to store Show # dropdown
			cells[3] = $('<div>').appendTo(paginationToolbar)
							     .css( {'width':'99%','clear':'both','text-align':'center'} ); 

			//Build results preview string dom which will be dynamically updated later by span class
			var resultsDisplay = $('<span>');
			var start = $('<span>').addClass('startrowSpan')
					               .before('Results ')
					               .text(state.startrow);
			resultsDisplay.append(start);
 
			var rowcount = $('<span>').addClass('numrowSpan')
									  .before('-')
									  .text(state.numrows);
			resultsDisplay.append(rowcount);

			var totalresults = $('<span>').addClass('totalrecordsSpan')
										  .before(' of ')
										  .text(state.max_results);
			resultsDisplay.append(totalresults);		
			resultsDisplay.appendTo(cells[1]);

			showPerPage.appendTo(cells[3]);

			$('<a>').appendTo(cells[0])
				    .addClass('previousPageLink')
				    .text('Previous')
				    .attr('href','javascript:;')
				.click(function ( event ) {
					state.page = Number(state.page) - 1;
					state.startrow = Number(state.startrow) - Number(state.numrows);

					if (state.page < 1) {
						//reset state vars to carousel to last page
						state.page = getLastPageNum(state.numrows,state.total_rows);
						state.startrow = ( Number(state.numrows) * (Number(state.page) - 1 ) ) + 1;
					}

					updateResultSetEventHandler.call(container, event);
				});

			$('<a>').appendTo(cells[2])
					.addClass('nextPageLink')
					.text('Next')
					.attr('href','javascript:;')
				.click(function ( event ) {
					state.page = Number(state.page) + 1;
					state.startrow = Number(state.startrow) + Number(state.numrows);

					if (state.page > getLastPageNum(state.numrows,state.total_rows)) {
						//reset state vars to carousel to 1st page
						state.page = 1;
						state.startrow = 1;
					}

					updateResultSetEventHandler.call(container, event);
				});

			return paginationToolbar;
		} //end method renderPaginationTools


		//Method that updates the state of the ajax call to get the new listings
		var updateResultSetEventHandler = function ( event ) {

			var container = this;
			var options = container.data(datakey);
			var state = container.data('state');

			var data = $.extend(state,options.criteria);
			data.ownerType = options.ownerType;

			$.ajax({ 
				url: '?pagename=wolfnet-listings-get',
				dataType: 'json',
				data: data,
				success: function ( data ) {
					//event handler logic
					updateResultSetRenderPage( data
						                      ,container
						                      ,options.usesPagination
						                      ,state.startrow
						                      ,state.numrows
						                      ,state.page
						                      ,state.total_rows);
				} 
			});	
		}


		var updateResultSetRenderPage = function ( data, container, paginationEnabled, startrow, numrows, page, totalRows ) {
	
			//clear listings from widget
			container.find('.wolfnet_listing').remove();
			container.find('.wolfnet_clearfix').remove();

			//rebuild list or grid component html doms
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
					container.find('.grid-listings-widget').append(listingEntity);
									
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

						var imageSpan = $('<span>').addClass('wolfnet_brokerLogo');
						var image = $('<img>').attr('src',brokerLogo)
											  .appendTo(imageSpan);
						imageSpan.appendTo(branding);
						$('<span>').addClass('wolfnet_brandingMessage')	
								   .text(brokerName)
								   .appendTo(branding);
						location.after(branding);
					}
				}//END: loop to rebuild listing grid dom

				container.wolfnetListingGrid('reload');
			}
			else if ( container.hasClass('wolfnet_propertyList') ) {

				//START:  rebuild property list dom (propertyList uses listingBrief.php)
				//loop listings in data object and build new listing entity to append to dom
				for (var i=0; i<data.length; i++) {

					var listingEntity = $('<div>').addClass('wolfnet_listing')
							  					  .attr('id','wolfnet_listing_'+data[i].property_id);
					container.find('.list-listings-widget').append(listingEntity);

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

			//update results count display
			var rowcountDisplay = (Number(startrow) - 1) + Number(numrows);
			if (rowcountDisplay > previewLimitCount) {
				rowcountDisplay = previewLimitCount;
			}
			container.find('.startrowSpan').html(startrow);
			container.find('.numrowSpan').html(rowcountDisplay);

			//clear show # select's options' selected attributes and update
			container.find('select.wntShowlistings option').removeAttr('selected');
			container.find('select.wntShowlistings option[value=\''+numrows+'\']').attr('selected','');

		}//end: updateResultSetRenderPage


		var priceFormatter = function ( number ) {
    		var number = number.toString(), 
    		dollars = number.split('.')[0], 
    		dollars = dollars.split('').reverse().join('')
    		    .replace(/(\d{3}(?!$))/g, '$1,')
    		    .split('').reverse().join('');
    		return '$' + dollars;
		}


		var getLastPageNum = function ( numrows, rowcount ) {
			var maxrows;
			if (rowcount < previewLimitCount) {
				maxrows = rowcount;
			}
			else {
				maxrows = previewLimitCount;
			}
			return Math.ceil( Number(maxrows) / Number(numrows) );
		}			

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */


