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
			
			var grid       = this;
			var $grid      = $( this );
			var listingCls = '.wolfnet_listing';
			
			var maxHeight = -1;
			var maxWidth  = -1;
				
			$( listingCls ).each( function () {
				maxHeight = maxHeight > $( this ).height() ? maxHeight : $( this ).height();
				maxWidth  = maxWidth  > $( this ).width()  ? maxWidth  : $( this ).width();
			} );
				
			$( listingCls ).each( function () {
				$( this ).height( maxHeight );
				$( this ).width(  maxWidth );
			} );
			
			/* When the window is resized calculate the appropriate margins for the grid items to 
			 * ensure that the grid and its items are centered. */
			var onResize = function () {
				var itemWidth   = $grid.find( listingCls + ':first' ).width();
				var gridWidth   = $grid.width();
				var numColumns  = Math.floor( gridWidth / itemWidth );
				var marginWidth = Math.floor( ( ( ( gridWidth % itemWidth ) - 1 ) / numColumns ) / 2 );
				
				$grid.find( listingCls ).css( 'margin-right', marginWidth );
				$grid.find( listingCls ).css( 'margin-left',  marginWidth );
				
			};
			
			$( window ).resize( onResize );
			
			onResize();
			
		} );
		
	};
	
} )( jQuery );