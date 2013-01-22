
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
			//alert("hello wordpress!!");

			var resultTools = $('<div></div>').addClass('results_tools');

			for (var i=1; i<=3; i++) {
				$('<div id="cell_' + i + '">rippy' + i + '</div>')
					.addClass('wolfnet_tools_div')
					.appendTo(resultTools);
			}

			//Build Sort By dropdown and append to cell_1
			var sortByDropdown = $('<select></select>');

			//Build results preview string and append to cell_2
			var resultsPreview = "Results x-X of XXX";

			//Build Show X Per Page dropdown & append to cell_3
			var showDropdown = $('<select></select>');
			//$(resultTools).appendTo(document.body);

		}

	} )( jQuery ); /* END: jQuery IIFE */
} /* END: If jQuery Exists */
