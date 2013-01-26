
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
								 	}
								    ,options );

			return this.each( function () {			

				$( this ).data( datakey , options );
				$( this ).data( 'state' 
					          , { 	page 			: 	options.page,
					          	    startrow		: 	options.startrow,
							   		numrows	        : 	options.numrows,
							   		sort 			: 	options.sort,
							   		ownerType		:   options.ownerType
							   	});				


				//Sort dropdown - build and insert to interface before & after listings
				var sortDropdown = renderSortDropdown.call( this );
				$( this ).append( sortDropdown.clone(true) );
				$( this ).find('h2.widget-title').after( sortDropdown.clone(true) );

				//Pagination controls - build and insert to interface before & after listings, if enabled
				if (options.usesPagination == true) {
					var pagination = renderPaginationTools.call( this, options.numrows );
					$( this ).append( pagination.clone(true) );
					$( this ).find('h2.widget-title').after( pagination.clone(true) );
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
			cells[0] = $('<div>')
				.appendTo(resultTools)
				.css( {'width':'99%','clear':'both','text-align':'left'} );

			//Build Sort By dropdown and append to first cell
			var sortByDropdown = $('<select>')
				.addClass( 'sortoptions' )
				.change(function(event){

					//console.log('sort',$( this).val());
					state.sort = $(this).val();
					state.page = 1;
					state.startrow = 1;
					updateResultSet.call(container, event);

				});;

			$.ajax({ 
				url: '?pagename=wolfnet-get-sortOptions-dropdown',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.sort_div' ).find( 'select.sortoptions' );
					select.empty();
					for ( var key in data ) {
						$("<option>", {value:data[key][0],text:data[key][1]}).appendTo( select );
					}
				}
			});
			$(sortByDropdown).appendTo(cells[0]);

			return resultTools;
		} //end method renderSortDropdown


		// Method to build out results toolbar
		var renderPaginationTools = function ( numrows ) {

 			var container = $( this );
 			var options = container.data(datakey);
 			var state = container.data('state');

			var paginationToolbar = $('<div>')
				.addClass('pagination_div')
				.css( {'width':'100%','clear':'both'} );;

			//Build show # of listings dropdown and append to third cell
			var showDropdown = $('<select>')
				.addClass( 'showlistings' )
				.change(function(event){

					//console.log('show #',$( this).val());
					state.numrows = $(this).val();
					updateResultSet.call(container, event);

				});

			$.ajax({ 
				url: '?pagename=wolfnet-get-showNumberOfListings-dropdown',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.pagination_div' ).find( 'select.showlistings' );
					select.empty();
					for ( var key in data ) {
						$("<option>", {value:data[key],text:data[key]}).appendTo( select );
					}
					$("<option>", {value:numrows,text:numrows}).appendTo(select).attr("selected","selected");;
				}
			});			
			var showPerPage = $(showDropdown).before('Show').after('per page');

			// Horizontal cells within pagination toolbar
			var cells = [];
			cells[0] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'left','test-align':'left'} );
			cells[1] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'left','text-align':'center'} );
			cells[2] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'right','text-align':'right'} ); 

			//new horizontal cell to store Show # dropdown
			cells[3] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'99%','clear':'both','text-align':'center'} ); 

			//Build results preview string and append to second cell
			var resultsCount = getResultsCountString(numrows);
			$(cells[1]).html(resultsCount);

			$(showPerPage).appendTo(cells[3]);

			$("<a>").appendTo(cells[0]).addClass("previousPage").html("<span>Previous</span>").attr("href","javascript:;")
				.click(function ( event ) {
					//console.log("previous");
					state.page = options.page - 1;
					state.startrow = state.startrow - state.numrows;
					updateResultSet.call(container, event);
				});

			$("<a>").appendTo(cells[2]).addClass("nextPage").html("<span>Next</span>").attr("href","javascript:;")
				.click(function ( event ) {
					//console.log("next");
					state.page = options.page + 1;
					state.startrow = state.startrow + state.numrows;					
					updateResultSet.call(container, event);
				});

			return paginationToolbar;
		} //end method renderPaginationTools


		//Method that updates the state of the ajax call to get the new listings
		var updateResultSet = function ( event ) {
			//console.log('result update');

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
					//console.log("ajax success");
					//call function to rewrite data coming back to render on page
					//var buildData.call( container, data );
				}
			});	
		}


		// Method to calculate and return results preview string
		// keep track of pagination increments to traverse results set forward/backward
		var getResultsCountString = function ( numrows ) {
			var preview = 'Results 1-' + numrows + ' of XXX';
			return preview;
		}


	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */

