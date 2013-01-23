
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
		$.fn.wolfnetToolbar = function ( usesPagination,
										 resultsPerPage ) {

			var toolbar = renderResultsToolbar();
			$( this ).prepend( toolbar.clone() ).append( toolbar.clone() );
			
			if (usesPagination == 'true') {				
				var pagination = renderPaginationToolbar();
				$( this ).prepend( pagination.clone() ).append( pagination.clone() );
			}

		} /* END: function $.fn.wolfnetToolbar */


		// Method to build out results toolbar
		var renderResultsToolbar = function () {

			var resultTools = $('<div>').addClass('toolbar_div').css( {'width':'100%'} );

			// Horizontal cells within toolbar div
			var cells = [];
			cells[1] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%',
				 'display':'table-cell',
				 'float':'left'} );
			cells[2] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%',
			 	 'display':'table-cell',
			 	 'text-align':'center'} ); 
			cells[3] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%',
				 'display':'table-cell',
				 'float':'right'} ); 

			//Build Sort By dropdown and append to first cell
			var sortByDropdown = $('<select>').addClass( 'sortoptions' );
			$.ajax({ 
				url: '?pagename=wolfnet-listing-sortoptions',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.toolbar_div' ).find( 'select.sortoptions' );
					select.empty();
					for ( var key in data ) {
						$("<option>", {value:data[key][0],text:data[key][1]}).appendTo( select );

					}
				}
			});
			$(sortByDropdown).appendTo(cells[1]);

			//Build results preview string and append to second cell
			var resultsCount = getResultsCountString();
			$(cells[2]).text(resultsCount).addClass();

			//Build show # of listings dropdown and append to third cell
			var showDropdown = $('<select>').addClass( 'showlistings' );
			$.ajax({ 
				url: '?pagename=wolfnet-listing-showlistings',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.toolbar_div' ).find( 'select.showlistings' );
					select.empty();
					for ( var key in data ) {
						$("<option>", {value:data[key],text:data[key]}).appendTo( select );
					}
				}
			});			
			$(showDropdown).appendTo(cells[3]);

			return resultTools;
		}


		// Method to build out results toolbar
		var renderPaginationToolbar = function () {
			var paginationToolbar = $('<div>').addClass('pagination_div');

			var cells = [];
			cells[1] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'50%',
				 'display':'table-cell',
				 'float':'left'} );
			cells[2] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'50%',
			 	 'display':'table-cell',
			 	 'float':'right'} ); 

			cells[1].text('<<Previous');
			cells[2].text('Next>>');

			return paginationToolbar;
		}

		// Method to calculate and return results preview string
		var getResultsCountString = function () {
			var preview = "Results x-YY of XXX";
			return preview;
		}


	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */

