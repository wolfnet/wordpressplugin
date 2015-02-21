/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which
 * will then be displayed in a list format.
 *
 * @title         jquery.wolfnetPropertyList.src.js
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
					var $addr = $item.find( '.wolfnet_full_address:first' );
					var cont  = $addr.html();
					var len   = cont.length;
					var trim  = 1;

					while ( $link.width() > listWidth - 5 ) {

						$addr.html( cont.substring( 0, ( len - trim ) ).trim() + '... ' );
						trim++;

					}

				} );

				$this.css( {
					'position' : 'static'
				} );

				$this.find( '.wolfnet_listing' ).each( function () {

					$( this ).find( 'a' ).each( function () {

						$( this ).append( '<div class="wolfnet_clearfix"></div>' );

						$( this ).find( 'span.wolfnet_full_address' ).css( {
							'float' : 'left'
						} );

						$this.find( 'span.wolfnet_price' ).css( {
							'float' : 'right'
						} );

					} );

				} )

			} ); /* END: for each loop of elements the plugin has been applied to. */

		}; /* END: function $.fn.wolfnetPropertyList */

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
