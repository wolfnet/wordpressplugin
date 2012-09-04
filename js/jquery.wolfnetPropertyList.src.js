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
				var listWidth  = $this.width();
				var $items     = $this.find( 'div.wolfnet_listing' );

				$this.css( {
					'position' : 'absolute'
				} );

				$items.find( '*:visible' ).css( {
					'white-space' : 'nowrap',
					'display'     : 'inline',
					'float'       : 'none',
					'overflow'    : 'visible'
				} );

				$items.each( function () {

					var $item = $( this );
					var $link = $item.find( 'a:first' );
					var $addr = $item.find( '.full_address:first' );
					var cont  = $addr.html();
					var len   = cont.length;
					var trim  = 1;

					while ( $link.width() > listWidth ) {

						$addr.html( cont.substring( 0, ( len - trim ) ).trim() + '... ' );
						trim++;

					}

				} );

				$this.css( {
					'position' : 'static'
				} );

				$this.find( '.wolfnet_listing span.price' ).css( {
					'float' : 'right'
				} );

			} ); /* END: for each loop of elements the plugin has been applied to. */

		}; /* END: function $.fn.wolfnetPropertyList */

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */