/**
 * This jQuery plugin can be applied to a Quick Search form with appropriate fields.
 * 
 * @title         jquery.wntQuickSearch.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @option  boolean  autoplay  Should the child elements start scrolling right away.
 * @option  int      speed     How fast should the child elements scroll.
 * @option  boolean  wait      Should scrolling pause after each child element moves out of view.
 * @option  int      waitlen   How long (ms) should scrolling pause during a wait period.
 * 
 */

( function ( $ ) {
	
	$.fn.wntQuickSearch = function ( options ) {
		
		var opt = $.extend( {}, options );
		
		return this.each( function () {
			
			var self = $(this);
			
		});
		
	};
	
} )( jQuery );