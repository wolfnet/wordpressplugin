/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which 
 * will then be displayed in a grid format.
 * 
 * @title         jquery.wolfnetListingGrid.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * 
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following 
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $ 
 * variable.
 */
if ( typeof jQuery != 'undefined' ) {
	
	( function ( $ ) {
		
		$.fn.wolfnetListingGrid = function ( options ) {
			
			var option = $.extend( { hideBranding:true }, options );
			
			return this.each( function () {
				
				var  grid      = this;
				var $grid      = $( this );
				var listingCls = '.wolfnet_listing';
				
				var maxHeight = -1;
				var maxWidth  = -1;
				
				$grid.append( '<div class="clearfix" />' );
				
				var calculateItemSize = function () {
					
					maxHeight = -1;
					maxWidth  = -1;
					
					$( this ).height( $( this ).height() );
					$( this ).width(  $( this ).width() );
					
					$grid.find( listingCls ).each( function () {
						maxHeight = maxHeight > $( this ).height() ? maxHeight : $( this ).height();
						maxWidth  = maxWidth  > $( this ).width()  ? maxWidth  : $( this ).width();
					} );
					
					$grid.find( listingCls ).each( function () {
						$( this ).height( maxHeight );
						$( this ).width(  maxWidth );
					} );
					
				}
				
				calculateItemSize();
				
				/* When the window is resized calculate the appropriate margins for the grid items to 
				 * ensure that the grid and its items are centered. */
				var onResize = function ()
				{
					
					var itemWidth   = $grid.find( listingCls + ':first' ).width();
					var gridWidth   = $grid.width();
					var numColumns  = Math.floor( gridWidth / itemWidth );
					var marginWidth = Math.floor( ( ( ( gridWidth % itemWidth ) - 1 ) / numColumns ) / 2 );
					
					$grid.find( listingCls ).css( 'margin-right', marginWidth );
					$grid.find( listingCls ).css( 'margin-left',  marginWidth );
					
				};
				
				$( window ).resize( onResize );
				
				$( window ).trigger( 'resize' );
					
				if ( option.hideBranding ) {
					
					var $branding = $grid.find( '.branding' );
					
					$branding.hide();
					$branding.each( function () {
						var $branding = $( this );
						$( $branding ).parent().tooltip( {
							showURL: false,
							bodyHandler: function() {
								return $( $branding.html() ).show();
							}
						} );
					} );
					
					
				}
				else {
					
					$grid.find( '.branding' ).imagesLoaded( function () {
						
						calculateItemSize();
						$( window ).trigger( 'resize' );
						
					} );
					
				}
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: function $.fn.wolfnetListingGrid */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */