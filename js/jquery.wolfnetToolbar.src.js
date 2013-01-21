
/**
 * Code for results toolbar which contains:
 * 		-Sort dropdown
 *  	-Results count display (i.e. "Results 1-XX of XXXX")
 *		-Show XX per page dropdown
 *
 *		-also, pagination Previous/Next if enabled via admin (setting: paginated)
 */
if ( typeof jQuery != 'undefined' ) {
	( function ( $ ) {
		$.fn.wolfnetToolbar = function () {

			renderResultsToolbar();

			//if pagination is enabled
			//renderPaginationToolbar();

		} /* END: function $.fn.wolfnetToolbar */

		var renderResultsToolbar = function () {
			alert("hello wordpress");

/*
			//table for plugin
			var pluginTable = $('table').addClass('pluginTbl');

			//append row; results toolbar plugin will be a single table row
			var row = $('tr');
			$(pluginTable).append(row);

			//build Sort By dropdown and append to row 
			var sortByDropdown = $('select');

			//append Results count display to row
			$(row).append('<td>Results x-X of XXX</td>')

			//build Show # Per Page dropdown and append to row
			var showDropdown = $('select');
*/
		}

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */
