/**
 * This jQuery plugin can be applied to a Quick Search form with appropriate fields.
 * 
 * @title         jquery.wolfnetQuickSearch.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * 
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following 
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $ 
 * variable.
 */
if ( jQuery ) {
	
	( function ( $ ) {
		
		$.fn.wolfnetQuickSearch = function ( options ) {
			
			var option = $.extend( {}, options );
			
			/* This function establishes the hint text in the search text field based on the search 
			 * type that has been selected. */
			var changeSearchType = function ( $searchTypeLink, $input )
			{
				var searchTypeAttr  = 'wnt:search_type';
				var hintAttrPrefix  = 'wnt:hint_';
				var nameAttrPrefix  = 'wnt:name_';
				var searchType      = $searchTypeLink.attr( searchTypeAttr );
				
				/* Update the hint text. */
				$input.prop( 'hint', $input.attr( hintAttrPrefix + searchType ) );
				$input.val( $input.prop( 'hint' ) );
				
				/* Update the text field name so that string is passed to the correct parameter in 
				 * the search solution. */
				$input.attr( 'name', $input.attr( nameAttrPrefix + searchType ) );
				
			}
			
			/* This function is a callback for the onFocus event for the search text field. */
			var performFocus = function ( $input )
			{
				var hint = $input.prop( 'hint' );
				
				/* If the input field is currently populated with a hint replace it with empty string. */
				if ( $input.val() == hint ) {
					
					$input.val( '' );
					
				}
				
				/* Make sure the field is not styled as hint text. */
				$input.removeClass( 'hintText' );
				
			}
			
			/* This function is a callback for the onBlur event for the search text field. */
			var performBlur = function ( $input )
			{
				var hint = $input.prop( 'hint' );
				
				/* If the input field is empty we still want to show the user the hint. */
				if ( $input.val().trim() == '' ) {
					
					$input.val( hint );
					$input.addClass( 'hintText' ); /* Style the field as as hint text. */
					
				}
				
			}
			
			/* For each of the elements to which the plugin is being applied do the following. */
			return this.each( function () {
				
				var $quickSearch       = $( this );
				var $quickSearchForm   = $quickSearch.find( '.wolfnet_quickSearch_form' );
				var $searchInput       = $quickSearch.find( '.wolfnet_quickSearch_searchText' );
				var $searchTypeLinks   = $quickSearch.find( 'ul.wolfnet_searchType li a' );
				var $defaultSearchLink = $searchTypeLinks.first();
				
				/* Establish the 'hint' property so that we do not get any errors. */
				$searchInput.prop( 'hint', '' );
				
				/* Apply the follwing logic to the click event of any 'search type links' within the 
				 * current quick search instance. This will cause the form to update base on which 
				 * search type is being used. */
				$searchTypeLinks.click( function () {
					
					changeSearchType( $( this ), $searchInput );
					
				} );
				
				/* Apply the follwing logic to the focus event of search text input within the 
				 * current quick search instance. */
				$searchInput.focus( function () {
					
					performFocus( $( this ) );
					
				} );
				
				/* Apply the follwing logic to the blur event of search text input within the 
				 * current quick search instance. */
				$searchInput.blur( function () {
					
					performBlur( $( this ) );
					
				} );
				
				/* Apply the following logic to the submit event of the search form. */
				$quickSearchForm.submit( function () {
					
					performFocus( $searchInput );
					
				} );
				
				/* Imediately perform the change search type logic to make sure the defaults are set 
				 * for the form. */
				changeSearchType( $defaultSearchLink, $searchInput );
				
				/* Imediately perform the blur logic to make sure the defaults are set for the form. */
				performBlur( $searchInput );
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: function $.fn.wolfnetQuickSearch */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */