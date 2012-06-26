/**
 * This jQuery plugin can be applied to a Quick Search form with appropriate fields.
 * 
 * @title         jquery.wolfnetQuickSearch.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * 
 */

 /* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	}
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following 
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $ 
 * variable.
 */
if ( jQuery ) {
	
	( function ( $ ) {
		
		$.fn.wolfnetQuickSearch = function ( options ) {
			
			/* Define the default options for the plugin. */
			var defaultOptions = {
			        defaultSearchType : '_opentxt',
			        searchTypes : {
			            _opentxt : {
			                hint : 'House #, Street, City, State, or Zip',
			                name : 'open_text'
			                },
			            mlsnum  : {
			                hint : 'MLS Listing #',
			                name : 'property_id'
			                }
			            }
			    }
			
			/* If the options passed to the plugin contain 'searchTypes' merge them with the default
			 * search types so that we do not lose them. */
			if ( options && 'searchTypes' in options ) {
				options.searchTypes = $.extend( defaultOptions.searchTypes, options.searchTypes );
			}
			
			/* Merge the options passed into the plugin instance with the default options. */
			var option = $.extend( defaultOptions, options );
			
			/* This function establishes the hint text in the search text field based on the search 
			 * type that has been selected/clicked. */
			var performTypeChange = function ( $searchTypeLink, $input )
			{
				var searchTypeAttr = 'wnt:search_type';
				var searchType     = $searchTypeLink.attr( searchTypeAttr );
				$input.get(0).changeSearchType( searchType );
				
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
				if ( $input.val().trim() == '' || $input.val() == hint ) {
					
					$input.val( hint );
					$input.addClass( 'hintText' ); /* Style the field as as hint text. */
					
				}
				
			}
			
			/* For each of the elements to which the plugin is being applied do the following. */
			return this.each( function () {
				
				var $quickSearch       = $( this );
				var $quickSearchForm   = $quickSearch.find( '.wolfnet_quickSearch_form:first' );
				var $searchInput       = $quickSearch.find( '.wolfnet_quickSearch_searchText:first' );
				var $searchTypeLinks   = $quickSearch.find( 'ul.wolfnet_searchType li a' );
				var $defaultSearchLink = $searchTypeLinks.first();
				
				/* Establish the new properties of the search text field for managing the hint text.
				 * this is done this way so that the 'changeSearchType' function is exposed as part 
				 * of the elements DOM object and can then be manipulated outside of the plugin. */
				$searchInput.get(0).hint = '';
				$searchInput.get(0).searchTypes = option.searchTypes;
				$searchInput.get(0).defaultSearchType = option.defaultSearchType;
				$searchInput.get(0).changeSearchType = function ( searchType )
				{
					
					/* Make sure the searchType is defined and has the correct properties. */
					if ( !( searchType in this.searchTypes ) 
						|| (
							!( 'hint' in this.searchTypes[searchType] ) 
							|| !( 'name' in this.searchTypes[searchType] ) 
						) 
					) {
						searchType = this.defaultSearchType;
					}
					
					
					
					/* Update the hint text. */
					this.hint = this.searchTypes[searchType].hint;
					this.value = this.hint;
					
					/* Update the text field name so that string is passed to the correct parameter in 
					 * the search solution. */
					this.name = this.searchTypes[searchType].name;
					
				};
				
				/* Apply the follwing logic to the click event of any 'search type links' within the 
				 * current quick search instance. This will cause the form to update base on which 
				 * search type is being used. */
				$searchTypeLinks.click( function () {
					
					performTypeChange( $( this ), $searchInput );
					
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
				performTypeChange( $defaultSearchLink, $searchInput );
				
				/* Imediately perform the blur logic to make sure the defaults are set for the form. */
				performBlur( $searchInput );
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: function $.fn.wolfnetQuickSearch */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */