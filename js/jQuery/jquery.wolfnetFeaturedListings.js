/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which 
 * will then scroll based on the options that are passes in.
 * 
 * @title         jquery.wolfnetFeaturedListings.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * 
 * @option  boolean  autoplay  Should the child elements start scrolling right away.
 * @option  int      speed     How fast should the child elements scroll.
 * @option  boolean  wait      Should scrolling pause after each child element moves out of view.
 * @option  int      waitlen   How long (ms) should scrolling pause during a wait period.
 * 
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following 
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $ 
 * variable.
 */
if ( jQuery ) {
	
	( function ( $ ) {
		
		$.fn.wolfnetFeaturedListings = function ( options ) {
			
			var classPrefix    = 'wolfnet_featuredListings_';
			
			var oContainerCls  = classPrefix + 'outterContainer';
			var iContainerCls  = classPrefix + 'innerContainer';
			var prevBtnCls     = classPrefix + 'prev';
			var nextBtnCls     = classPrefix + 'next';
			
			var defaultOptions = {
								 'autoplay':    true,
								 'speed':       5000,
								 'wait':        true,
								 'waitLen':     2000,
								 'scrollCount': 0,
								 'direction':   'right'
								 }
			
			var option = $.extend( defaultOptions, options );
			
			if ( !option.wait ) {
				option.waitLen     = 0;
				option.scrollCount = 1;
			}
			
			return this.each( function () {
				
				var featListings       = this;
				var $featListings      = $( this );
				
				var timer              = null;
				var paused             = false;
				var runningScrollCount = 0;
				
				/* This veriable holds a reference to a set of elements which will be duplicated 
				 * temporarily then removed. This is done to create a seemless scrolling loop. */
				var dupChildren = [];
				
				/* Add Extra Elements - Wrapping Containers and Manual Controls
				 * ------------------------------------------------------------
				 * This is done here rather than in the view file because this code is only neccesary
				 * because of this JavaScript.
				 * 
				 * The Outter Container is used to hold the shape and position of the scrolling featured 
				 * listings as well as providing the actual scrolling mechanism via and "overflow scroll".
				 * 
				 * The Inner Container holds the individual listings and is sized to ensure all listings 
				 * appear on the same line.
				 */
				$featListings.wrapInner( '<div class="' + iContainerCls + '">' );
				$featListings.wrapInner( '<div class="' + oContainerCls + '">' );
				$featListings.append( '<div class="' + prevBtnCls + '"><span>Prev</span></div>' 
									+ '<div class="' + nextBtnCls + '"><span>Next</span></div>' );
				
				/* Capture The Previous Button */
				var prevBtn  = $featListings.find( '.' + prevBtnCls + ':first' );
				var $prevBtn = $( prevBtn );
				
				/* Capture The Next Button */
				var nextBtn  = $featListings.find( '.' + nextBtnCls + ':first' );
				var $nextBtn = $( nextBtn );
				
				$prevBtn.click( function () {
					move( false, ( option.direction == 'up' || option.direction == 'down' ) ? 'up'   : 'left'  , 1, 1000 );
				} );
				
				$nextBtn.click( function () {
					move( false, ( option.direction == 'up' || option.direction == 'down' ) ? 'down' : 'right' , 1, 1000 );
				} );
				
				/* Capture the Outter Container - This container will be used for scrolling */
				var outterContainer  = $featListings.find( '.' + oContainerCls + ':first' );
				var $outterContainer = $( outterContainer );
				
				/* Capture The Inner Container - This container hold the individual listing items. */
				var innerContainer  = $featListings.find( '.' + iContainerCls + ':first' );
				var $innerContainer = $( innerContainer );
				
				/* Capture The First Container Child
				 * ---------------------------------
				 * This element is used to define defaults when the plugin first loads. This element 
				 * will also be moved to the end of the listing as scrolling occurs.
				 */
				var firstChild  = $innerContainer.children( 'div:first' );
				var $firstChild = $( firstChild );
				
				var itemWidth   = $firstChild.width();
				
				var onResize = function ()
				{
					var itemWidth      = $featListings.find( '.wolfnet_listing:first' ).width();
					var containerWidth = $featListings.width();
					var numColumns     = Math.floor( containerWidth / itemWidth );
					var marginWidth    = Math.floor( ( ( ( containerWidth % itemWidth ) - 1 ) / numColumns ) / 2 );
					
					$featListings.find( '.wolfnet_listing' ).css( 'margin-right', marginWidth );
					$featListings.find( '.wolfnet_listing' ).css( 'margin-left',  marginWidth );
					
				};
				
				$( window ).resize( onResize );
				
				onResize();
				
				var itemMarginLeft  = $firstChild.css( 'margin-left' );
				var itemMarginRight = $firstChild.css( 'margin-right' );
				
				
				/* Ensure that every item has the same dimensions */
				var $innerContainerChildren = $innerContainer.children( 'div.wolfnet_listing' ).each( function () {
					$( this ).width( itemWidth );
					$( this ).css( 'margin-left', itemMarginLeft );
					$( this ).css( 'margin-right', itemMarginRight );
				} );
				
				var itemFullWidth = itemWidth;
				
				/* Reset the width of the container to the sum of its children's widths */
				var innerContainerWidth = itemWidth * $innerContainerChildren.length;
				itemMarginLeft = Number( itemMarginLeft.replace( /[^-\d\.]/g, '') );
				if ( !isNaN( itemMarginLeft ) ) {
					innerContainerWidth = innerContainerWidth + ( itemMarginLeft * $innerContainerChildren.length );
					itemFullWidth  = itemFullWidth + itemMarginLeft;
				}
				itemMarginRight = Number( itemMarginRight.replace( /[^-\d\.]/g, '') );
				if ( !isNaN( itemMarginRight ) ) {
					innerContainerWidth = innerContainerWidth + ( itemMarginRight * $innerContainerChildren.length );
					itemFullWidth  = itemFullWidth + itemMarginRight;
				}
				
				$innerContainer.width( innerContainerWidth + ( itemFullWidth * option.scrollCount ) );
				
				/* Begin sliding */
				var play = function ( delay )
				{
					paused = false;
					if ( !delay || delay == 0 ) {
						delay = 0;
						move();
					}
					else {
						timer = setTimeout( move, delay );
					}
				}
				
				/* Pause Sliding */
				var pause = function ()
				{
					paused = true;
					$outterContainer.stop( true, true );
					clearTimeout( timer );
				}
				
				/* Perform Movement */
				var move = function ( andContinue, direction, count, speed )
				{
					
					if ( !andContinue ) {
						var andContinue = true;
					}
					
					if ( !direction ) {
						var direction = option.direction;
					}
					
					if ( !count ) {
						var count = option.scrollCount;
					}
					
					if ( !speed ) {
						var speed = option.speed;
					}
					
					
					switch ( direction ) {
						
						case 'left':
							$innerContainer.find( '.wolfnet_listing' ).css( 'float', 'left' );
							$outterContainer.scrollLeft( 0 );
							var startScroll = 0;
							var endScroll = itemFullWidth * count;
							var animation = { scrollLeft : endScroll };
							copyChildren( 'start', count );
							break;
						
						case 'right':
							$innerContainer.find( '.wolfnet_listing' ).css( 'float', 'right' );
							$outterContainer.scrollLeft( $outterContainer.prop( 'scrollWidth' ) );
							var startScroll = $outterContainer.scrollLeft();
							var endScroll = startScroll - ( itemFullWidth * count );
							var animation = { scrollLeft : endScroll };
							copyChildren( 'end', count );
							break;
						
						case 'up':
							$innerContainer.find( '.wolfnet_listing' ).css( 'float', 'left' );
							$outterContainer.scrollTop( 0 );
							var startScroll = 0;
							var endScroll = itemFullHeight * count;
							var animation = { scrollTop  : endScroll };
							copyChildren( 'start', count );
							break;
						
						case 'down':
							$innerContainer.find( '.wolfnet_listing' ).css( 'float', 'left' );
							$outterContainer.scrollTop( $outterContainer.prop( 'scrollHeight' ) );
							var startScroll = $outterContainer.scrollTop();
							var endScroll = startScroll - ( itemFullHeight * count );
							$outterContainer.scrollTop( startScroll );
							var animation = { scrollTop  : endScroll };
							copyChildren( 'end', count );
							break;
						
					}
					
					$outterContainer.animate(
						animation,
						{
							duration: speed,
							easing: ( option.wait ) ? 'swing' : 'linear',
							complete: function () {
								var delay = 0;
								dropChildren();
								$outterContainer.scrollLeft( startScroll );
								if ( andContinue && !paused ) {
									play( (option.wait) ? option.waitLen : 0 );
								}
							}
						}
					);
					
				}
				
				/* Move the first element to the last position and redefined the new first element. */
				var copyChildren = function ( from, num )
				{
					dupChildren = $innerContainer.find( '.wolfnet_listing' ).slice( 0, num );
					$innerContainer.append(  dupChildren.clone() );
				}
				
				var dropChildren = function ()
				{
					dupChildren.remove();
					dupChildren = [];
				}
				
				/* Pause Auto Scrolling on MouseOver */
				$featListings.mouseover( function () {
					$prevBtn.fadeTo( 500, .5 );
					$nextBtn.fadeTo( 500, .5 );
					if ( option.autoplay ) {
						pause();
					}
				} );
				
				/* Resume Auto Scrolling on MouseLeave */
				$featListings.mouseleave( function () {
					$prevBtn.fadeTo( 500, 0 );
					$nextBtn.fadeTo( 500, 0 );
					if ( option.autoplay ) {
						play();
					}
				} );
				
				/* AutoPlay
				 * --------
				 * If auto play is enabled initiate scrolling imediately after the plugin is 
				 * completely defined. This must be the last code defined in the plugin.
				 */
				if ( option.autoplay ) {
					
					play();
					
				}
				
			} ); /* END: for each loop of elements the plugin has been applied to. */
			
		}; /* END: Function $.fn.wolfnetFeaturedListings */
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */