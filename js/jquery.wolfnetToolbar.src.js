
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

		var datakey = 'wolfnetToolbarData';

		$.fn.wolfnetToolbar = function ( options ) {

			var options = $.extend( {usesPagination	: 	options.usesPagination,
									 page 			: 	1,
									 startrow		: 	1,
									 numrows        : 	options.numrows,
								 	 sort 			: 	'',
								 	 ownerType		: 	options.ownerType,
								 	 total_rows     :   options.total_rows,
								 	 criteria 		:   {}
								 	}
								    ,options );

			return this.each( function () {			

				$( this ).data( datakey , options );

				$( this ).data('state' 
					         ,{ page 		: 	options.page,
					            startrow	: 	options.startrow,
							    numrows	    : 	options.numrows,
							    sort 		: 	options.sort,
							    ownerType	:   options.ownerType,
							    total_rows  :   options.total_rows,
							    criteria 	: 	options.criteria							   
							});				

				//Sort dropdown - build and insert to interface before & after listings
				var sortDropdown = renderSortDropdown.call( this );
				$( this ).find('h2.widget-title').after( sortDropdown.clone(true) );
				$( this ).append( sortDropdown.clone(true) );

				//Pagination controls - build and insert to interface before & after listings, if enabled
				if (options.usesPagination == true) {
					var pagination = renderPaginationTools.call( this );
					$( this ).find('h2.widget-title').after( pagination.clone(true) );
					$( this ).append( pagination.clone(true) );
				}

			});

		} /*END: function $.fn.wolfnetToolbar*/


		// Method to build out results toolbar
		var renderSortDropdown = function ( ) {
 
 			var container = $( this );
 			var options = container.data(datakey);
 			var state = container.data('state');
		
			var resultTools = $('<div>')
				.addClass('sort_div')
				.css( {'width':'100%','clear':'both'} );

			// Horizontal cells within toolbar div
			var cells = [];
			cells[0] = $('<div>').appendTo(resultTools)
								 .css( {'width':'99%','clear':'both','text-align':'left'} );

			//Build Sort By dropdown and append to first cell
			var sortByDropdown = $('<select>').addClass( 'sortoptions' )
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
					var select = $( '.sort_div' ).find( 'select.sortoptions' );
					select.empty();
					for ( var key in data ) {
						$('<option>', {value:data[key][0],text:data[key][1]}).appendTo( select );
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

			var paginationToolbar = $('<div>').addClass('pagination_div')
											  .css( {'width':'100%','clear':'both'} );;

			//Build show # of listings dropdown and append to third cell
			var showDropdown = $('<select>').addClass( 'showlistings' )
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
					var select = $( '.pagination_div' ).find( 'select.showlistings' );
					select.empty();
					for ( var key in data ) {

						if (data[key] != state.numrows) {
							$('<option>',{value:data[key],text:data[key]})
									    .addClass('showNum_'+data[key])
									    .appendTo( select );
						}

						if (state.numrows >= data[key]) {
							$('<option>',{value:state.numrows,text:state.numrows})
								.addClass('showNum_'+state.numrows)
							    .appendTo(select)
								.attr('selected','selected');							
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
										  .text(options.total_rows);
			resultsDisplay.append(totalresults);		
			resultsDisplay.appendTo(cells[1]);

			showPerPage.appendTo(cells[3]);

			$('<a>').appendTo(cells[0])
				    .addClass('previousPage')
				    .html('<span class="previousPage">Previous</span>')
				    .attr('href','javascript:;')
				.click(function ( event ) {
					state.page = Number(state.page) - 1;
					state.startrow = Number(state.startrow) - Number(state.numrows);
					updateResultSetEventHandler.call(container, event);
				});

			$("<a>").appendTo(cells[2])
					.addClass('nextPage')
					.html('<span>Next</span>')
					.attr('href','javascript:;')
				.click(function ( event ) {
					state.page = Number(state.page) + 1;
					state.startrow = Number(state.startrow) + Number(state.numrows);
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
					updateResultSetRenderPage( data
						                      ,container
						                      ,options.usesPagination
						                      ,state.startrow
						                      ,state.numrows);
				} 
			});	
		}


		var updateResultSetRenderPage = function ( data, container, paginationEnabled, startrow, numrows ) {

			//clear listings from widget
			container.find('.wolfnet_listing').remove();

			//rebuild list or grid component html doms
			if ( container.hasClass('wolfnet_listingGrid') ) {				

				//START:  loop to rebuild listing grid dom (listingGrid uses listingSimple.php template)
				for (var i=0; i<data.length; i++) {

					//check for branding
					var brokerLogo= data[i].branding['brokerLogo'];
					var brokerName= data[i].branding['content'];
					if ( brokerLogo != '' || brokerName != '' ) {
						var hasBranding = true;
						var listingEntityClass = 'wolfnet_listing wolfnet_branded';
					}
					else {
						var hasBranding = false;
						var listingEntityClass = 'wolfnet_listing';	
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
							   .attr({'itemprop':'street_address','style':'display:none;'})
							   .text(fullAdress)
							   .appendTo(location);
					bedbath.after(location);

//if (i==0) { console.log( data[i].property_id ); }
//if (i==0) { console.log( data[i].branding ); }
//if (i==0) { console.log( 'logo ' + brokerLogo ); }
//if (i==0) { console.log( 'name ' + brokerName ); }

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

					var streetAddress = $('<span>').attr({'itemprop':'street-address','style':'display:none;'})
												   .text(fullAdress);
					price.after(streetAddress);
				}//END: rebuild property list dom
			}

			//update results count display
			var rowcountDisplay = (Number(startrow) - 1) + Number(numrows);
			container.find('.startrowSpan').html(startrow);
			container.find('.numrowSpan').html(rowcountDisplay);


		}//end: updateResultSetRenderPage


		var priceFormatter = function ( number ) {
    		var number = number.toString(), 
    		dollars = number.split('.')[0], 
    		dollars = dollars.split('').reverse().join('')
    		    .replace(/(\d{3}(?!$))/g, '$1,')
    		    .split('').reverse().join('');
    		return '$' + dollars;
		}

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */

