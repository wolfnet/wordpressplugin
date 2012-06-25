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
			
			var opt = $.extend( {}, options );
			
			return this.each( function () {
				
				var  quickSearch = this;
				var $quickSearch = $( this );
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: function $.fn.wolfnetQuickSearch */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */