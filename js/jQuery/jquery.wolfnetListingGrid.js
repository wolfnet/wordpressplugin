/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which 
 * will then be displayed in a grid format.
 * 
 * @title         jquery.wolfnetListingGrid.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * 
 */

( function ( $ ) {
	
	$.fn.wolfnetListingGrid = function ( options ) {
		
		var option = $.extend( {}, options );
		
		return this.each( function () {
			
			var grid  = this;
			var $grid = $( this );
			
			/* When the window is resized calculate the appropriate margins for the grid items to 
			 * ensure that the grid and its items are centered. */
			var onResize = function () {
				var itemWidth   = $grid.find( '.wolfnet_listing:first' ).width();
				var gridWidth   = $grid.width();
				var numColumns  = Math.floor( gridWidth / itemWidth );
				var marginWidth = Math.floor( ( ( ( gridWidth % itemWidth ) - 1 ) / numColumns ) / 2 );
				
				$grid.find( '.wolfnet_listing' ).css( 'margin-right', marginWidth );
				$grid.find( '.wolfnet_listing' ).css( 'margin-left',  marginWidth );
				
			};
			
			$( window ).resize( onResize );
			
			onResize();
			
		} );
		
	};
	
} )( jQuery );