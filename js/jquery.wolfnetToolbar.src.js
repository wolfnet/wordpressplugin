
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
		$.fn.wolfnetToolbar = function ( usesPagination
									   , resultsPerPage 
									   , titleString ) {

			var title = getTitleHeader(titleString);

			var toolbar = renderResultsToolbar( usesPagination,resultsPerPage );
			$( this ).prepend( toolbar.clone() ).append( toolbar.clone() );

			if (usesPagination == 'true') {				
				var pagination = renderPaginationToolbar( usesPagination,resultsPerPage );
				$( this ).prepend( pagination.clone() ).append( pagination.clone() );
			}

			$( this ).prepend( title.clone() );

		} /*END: function $.fn.wolfnetToolbar*/

		var getTitleHeader = function ( titleString ) {
			var header = $('<h2>').text(titleString);
			return header;
		}

		// Method to build out results toolbar
		var renderResultsToolbar = function ( usesPagination
											, resultsPerPage ) {

			var resultTools = $('<div>').addClass('toolbar_div').css( {'width':'100%','clear':'both'} );

			// Horizontal cells within toolbar div
			var cells = [];
			cells[1] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%','float':'left','test-align':'left'} );
			cells[2] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%','float':'left','text-align':'center'} );
			cells[3] = $('<div>').appendTo(resultTools).css( 
				{'width':'33%','float':'right','text-align':'right'} ); 

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
			var resultsCount = getResultsCountString(usesPagination,resultsPerPage);
			$(cells[2]).text(resultsCount);

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
			var showPerPage = $(showDropdown).before('Show').after('per page');
			$(showPerPage).appendTo(cells[3]);

			return resultTools;
		}


		// Method to build out results toolbar
		var renderPaginationToolbar = function ( usesPagination
											   , resultsPerPage ) {

			var paginationToolbar = $('<div>').addClass('pagination_div').css( {'width':'100%','clear':'both'} );;

			var cells = [];
			cells[1] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'50%','float':'left','test-align':'left'} );
			cells[2] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'50%','float':'right','text-align':'right'} );  

			cells[1].text('<<Previous');
			cells[2].text('Next>>');

			return paginationToolbar;
		}

		// Method to calculate and return results preview string
		var getResultsCountString = function ( usesPagination
											 , resultsPerPage ) {
			if (usesPagination == 'false')
			var preview = 'Results 1-' + resultsPerPage + ' of XXX';
			return preview;
		}


	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */

