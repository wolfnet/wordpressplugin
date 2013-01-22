
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
		$.fn.wolfnetToolbar = function ( usesPagination ) {

			var toolbar = renderResultsToolbar();
			$( this ).prepend( toolbar.clone() ).append( toolbar.clone() );

			if (usesPagination == 'true') {				
				var pagination = renderPaginationToolbar();
			}

		} /* END: function $.fn.wolfnetToolbar */


		// Method to build out results toolbar
		var renderResultsToolbar = function () {

			var resultTools = $('<div>').addClass('toolbar_div');
			var cells = [];

			for (var i=1; i<=3; i++) {
				cells[i] = $('<div>').appendTo(resultTools);
				//$(cells[i]).css = ("display","table-cell");
			}

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
			var resultsPreview = "Results x-XX of XXX";
			$(cells[2]).text(resultsPreview).addClass();


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
			var paginationControls = $('<div>').addClass('pagination_div');

			return paginationControls;
		}

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */
