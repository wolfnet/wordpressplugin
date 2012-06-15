/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which 
 * will then scroll based on the options that are passes in.
 * 
 * @title         jquery.wntFeaturedListings.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @option  boolean  autoplay  Should the child elements start scrolling right away.
 * @option  int      speed     How fast should the child elements scroll.
 * @option  boolean  wait      Should scrolling pause after each child element moves out of view.
 * @option  int      waitlen   How long (ms) should scrolling pause during a wait period.
 * 
 */

( function ( $ ) {
	
	$.fn.wntFeaturedListings = function ( options ) {
		
		var opt = $.extend( {
			'autoplay': true,
			'speed':    25,
			'wait':     true,
			'waitLen':  2000
		}, options );
		
		return this.each( function () {
			
			var self = $(this);
			var containerClass = 'mlsFinder_featuredListingsContainer';
			
			/* Add Extra Elements */
			self.wrapInner('<div class="' + containerClass + '">');
			
			/* Capture Container and set initial position */
			var cont  = self.find('.' + containerClass + ':first')[0];
			var $cont = $(cont);
			var p     = 0;
			var timer = null;
			
			var firstChild     = $cont.children('div:first');
			var $firstChild    = $(firstChild);
			var moveFirstChild = function () {
				$firstChild.appendTo( $cont );
				firstChild  = $cont.children('div:first');
				$firstChild = $(firstChild);
			}
			
			/* Reset the width of the container to the sum of its children's widths */
			$cont.width( 0 );
			$cont.children('div').each( function () {
				$cont.width( $cont.width() + $(this).width() );
			});
			
			/* Begin sliding */
			var play = function ( delay ) {
				if (!delay) {
					delay = 0;
				}
				timer = setTimeout( move, opt.speed + delay );
			}
			
			/* Pause Sliding */
			var pause = function () {
				clearTimeout( timer );
			}
			
			/* Perform Movement */
			var move = function () {
				var delay = false;
				if ( p >= $firstChild.width() ) {
					moveFirstChild();
					p = 0;
					if (opt.wait) {
						delay = true;
					}
				}
				p++;
				self.scrollLeft( p );
				if ( delay ) {
					play( opt.waitLen );
				}
				else {
					play();
				}
			}
			
			/* Pause on MouseOver */
			self.mouseover( function () {
				pause();
			});
			
			/* Play on MouseLeave */
			self.mouseleave( function () {
				play();
			});
			
			/* AutoPlay */
			if (opt.autoplay) {
				play();
			}
			
			
		});
		
	};
	
} )( jQuery );