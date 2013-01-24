
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

			var toolbar = renderSortDropdown( usesPagination,resultsPerPage );
			$( this ).prepend( toolbar.clone() ).append( toolbar.clone() );

			if (usesPagination == 'true') {				
				var pagination = renderSortByDropdown( resultsPerPage );
				$( this ).prepend( pagination.clone() ).append( pagination.clone() );
			}

			$( this ).prepend( title.clone() );

		} /*END: function $.fn.wolfnetToolbar*/


		var getTitleHeader = function ( titleString ) {
			var header = $('<h2>').text(titleString);
			return header;
		}


		// Method to build out results toolbar
		var renderSortDropdown = function ( usesPagination
											, resultsPerPage ) {

			var resultTools = $('<div>').addClass('sort_div').css( {'width':'100%','clear':'both'} );

			// Horizontal cells within toolbar div
			var cells = [];
			cells[0] = $('<div>').appendTo(resultTools).css( 
				{'width':'99%','clear':'both','text-align':'left'} );

			//Build Sort By dropdown and append to first cell
			var sortByDropdown = $('<select>').addClass( 'sortoptions' );
			$.ajax({ 
				url: '?pagename=wolfnet-listing-sortoptions',
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
		}


		// Method to build out results toolbar
		var renderSortByDropdown = function ( resultsPerPage ) {

			var paginationToolbar = $('<div>').addClass('pagination_div').css( {'width':'100%','clear':'both'} );;

			//Build show # of listings dropdown and append to third cell
			var showDropdown = $('<select>').addClass( 'showlistings' );
			$.ajax({ 
				url: '?pagename=wolfnet-listing-showlistings',
				dataType: 'json',
				success: function ( data ) {
					var select = $( '.pagination_div' ).find( 'select.showlistings' );
					select.empty();
					for ( var key in data ) {
						$("<option>", {value:data[key],text:data[key]}).appendTo( select );
					}
					$("<option>", {value:resultsPerPage,text:resultsPerPage}).appendTo(select).attr("selected","selected");;
				}
			});			
			var showPerPage = $(showDropdown).before('Show').after('per page');

			// Horizontal cells within toolbar div
			var cells = [];
			cells[0] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'left','test-align':'left'} );
			cells[1] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'left','text-align':'center'} );
			cells[2] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'33%','float':'right','text-align':'right'} ); 

			//cell to store Show # dropdown
			cells[3] = $('<div>').appendTo(paginationToolbar).css( 
				{'width':'99%','clear':'both','text-align':'center'} ); 

			//Build results preview string and append to second cell
			var resultsCount = getResultsCountString(resultsPerPage);
			$(cells[1]).text(resultsCount);

			$(showPerPage).appendTo(cells[3]);

			cells[0].text('<<Previous');
			cells[2].text('Next>>');

/*
			$("<a>").appendTo(cells[0]).addClass("previousPage").text("<<Previous").attr("href","?pagename=wolfnet-get-previous-results");
			$("<a>").appendTo(cells[2]).addClass("nextPage").text("Next>>").attr("href","?pagename=wolfnet-get-next-results");;

			$('.previousPage').click(function(e){
				e.preventDefault();
				var valueToPass = $(this).text();
				var url = "?pagename=wolfnet-get-previous-results";
				$.post(url, { data: valueToPass }, function( data ){

				} );
			});

			$('.nextPage').click(function(e){
				e.preventDefault();
				var valueToPass = $(this).text();
				var url = "?pagename=wolfnet-get-next-results";
				$.post(url, { data: valueToPass }, function( data ){

				} );
			});
*/
			return paginationToolbar;
		}


		// Method to calculate and return results preview string
		var getResultsCountString = function ( resultsPerPage ) {
			var preview = 'Results 1-' + resultsPerPage + ' of XXX';
			return preview;
		}


	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */

