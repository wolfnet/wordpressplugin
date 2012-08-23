/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which 
 * will then be displayed in a list format.
 * 
 * @title         jquery.wolfnetPropertyList.src.js
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
if ( typeof jQuery != 'undefined' ) {
	
	( function ( $ ) {
		
		$.fn.wolfnetPropertyList = function ( options ) {
			
			var option = $.extend( {}, options );
			
			return this.each( function () {
				
				var $this      = $( this );
				var $listWidth = $this.find( 'ul:first' ).width();
				
				$this.find( '.wolfnet_listing li' ).css( 'white-space', 'nowrap' );
				$this.find( '.wolfnet_listing div' ).css( 'white-space', 'nowrap' );
				$this.find( '.wolfnet_listing a' ).css( 'white-space', 'nowrap' );
				$this.find( '.wolfnet_listing span' ).css( 'white-space', 'nowrap' );
				$this.find( '.wolfnet_listing span' ).css( 'float', 'none' );
				
				var $listItemWidth = $this.find( 'li a' ).width();
				
				if ( $listItemWidth > $listWidth ) {
					$this.find( 'li span.full_address' ).each( function () {
						
						var $this = $( this );
						var $a = $this.parent( 'a' );
						var content = $this.html();
						var contentLen = content.length;
						var trimNum = 1;
						
						while ( $a.width() > $listWidth ) {
							
							$this.html( content.substring( 0, ( contentLen - trimNum ) ).trim() + '... ' );
							
							trimNum++;
							
						}
						
					} );
				}
				
				$this.find( '.wolfnet_listing span.price' ).css( 'float', 'right' );
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: function $.fn.wolfnetPropertyList */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */