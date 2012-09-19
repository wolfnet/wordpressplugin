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

		var name = 'wolfnetListingGrid';

		var defaultOptions = {
			listingCls : '.wolfnet_listing'
		}

		/* This method calculates the ideal margins to use for grid items given the width of the
		 * container and the items within it. */
		var calculateIdealMargin = function ( containerWidth, itemWidth, minMargin, modifier )
		{
			var numColumns    = Math.floor( containerWidth / itemWidth ) + modifier;
			var leftOverSpace = containerWidth - ( itemWidth * numColumns );
			var marginPerItem = leftOverSpace / numColumns;

			/* Does work in <=IE8, but avoids single columns */
			var idealMargin   = marginPerItem / 2;

			/* Works in every browser but has single columns */
			//var idealMargin   = Math.ceil( marginPerItem / 2 );

			if ( idealMargin == -1 ) {
				idealMargin = 0;
			}

			var itemsWithMargins = ( ( idealMargin * 2 ) + itemWidth ) * numColumns;

			var validMargins = ( idealMargin < minMargin || itemsWithMargins > containerWidth );

			if ( validMargins && numColumns > 1 ) {
				idealMargin = calculateIdealMargin( containerWidth, itemWidth, minMargin, modifier - 1 );
			}

			return idealMargin;

		}

		/* This function loops over all images in a container and triggers an even on the container
		 * when all images have completed loading. */
		var monitorImages = function ( container )
		{
			var $container = $( container );
			var $images    = $container.find( 'img' )
			var imageCount = $images.length;
			var loadedImgs = 0;

			$images.each( function () {

				var $this = $( this );

				if ( $this.prop( 'complete' ) === true ) {
					loadedImgs++;
				}

			} );

			if ( loadedImgs >= imageCount ) {
				$container.trigger( 'allImagesLoaded.' + name );
			}
			else {
				setTimeout( function () {
					monitorImages( container );
				}, 100 );
			}

		}

		/* Methods available to the plugin. */
		var methods = {

			/* Initialize the plugin for all elements that have been selected. */
			init : function ( options )
			{
				var plugin = this;

				this.data( name, {
					option : $.extend( defaultOptions, options )
				} );

				var option = this.data( name ).option;

				return this.each( function () {

					var $this = $( this );

					$this.append( '<div class="clearfix" />' );

					//methods._calculateItemSize.apply( plugin );

					/* When the window is resized calculate the appropriate margins for the grid items to
					 * ensure that the grid and its items are centered. */
					$( window ).bind( 'resize.' + name, function () {
						methods.resizeWidth.apply( plugin );
					} );
					//methods.resize.apply( plugin );

					$this.bind( 'allImagesLoaded.' + name, function () {
						methods.resizeHeight.apply( plugin );
					} );

					monitorImages( this );

				} ); /* END: for each loop of elements the plugin has been applied to. */

			},

			/* This method provides a safe way for the plugin to be removed from elements on the page. */
			destroy : function ()
			{
				var data = this.data( name );
				data[name].remove();
				this.removeData( name );

			},

			/* TODO: determine if this function is still neccessary. */
			_calculateItemSize : function ()
			{
				var option = this.data( name ).option;

				return this.each( function () {

					var $this     = $( this );
					var maxHeight = -1;
					var maxWidth  = -1;

					$this.height( $this.height() );
					$this.width(  $this.width() );

					$this.find( option.listingCls ).each( function () {
						var $this = $( this );
						maxHeight = maxHeight > $this.height() ? maxHeight : $this.height();
						maxWidth  = maxWidth  > $this.width()  ? maxWidth  : $this.width();
					} );

					$this.find( option.listingCls ).each( function () {
						var $this = $( this );
						$this.height( maxHeight );
						$this.width(  maxWidth );
					} );

				} );

			},

			/* This function uses the calculateIdealMargin function to resize the width of every
			 * item within the grid to provide a symetrical output. */
			resizeWidth : function ()
			{
				var option = this.data( name ).option;

				return this.each( function () {

					var $this          = $( this );
					var containerWidth = $this.width();
					var firstItemWidth = $this.find( option.listingCls + ':first' ).width();
					var marginWidth    = calculateIdealMargin( containerWidth, firstItemWidth, 2, 0 );
					var items          = $this.find( option.listingCls );

					items.each( function () {

						var $this = $( this );

						$this.css( {
							'margin-right' : marginWidth,
							'margin-left'  : marginWidth
						} );

					} );

				} );

			},

			/* This function calculates the maximum height required for an item in the grid and
			 * applies that height to all items within the grid. */
			resizeHeight : function ()
			{
				var option = this.data( name ).option;

				return this.each( function () {

					var $this          = $( this );
					var items          = $this.find( option.listingCls );
					var originalHeight = items.first().height();
					var maxHeight      = 0;

					items.css( {
						height : 'auto',
						'float' : 'none'
					} );

					items.each( function () {

						var $this = $( this );

						if ( $this.height() > maxHeight ) {
							maxHeight = $this.height();
						}

					} );

					items.height( maxHeight + 25 );
					items.css( {
						'float' : 'left'
					} );

				} );

			}

		}

		$.fn[name] = function ( method ) {

			if ( methods[method] ) {
				return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
			}
			else if ( typeof method === 'object' || ! method ) {
				return methods.init.apply( this, arguments );
			}
			else {
				$.error( 'Method ' + method + ' does not exist on jQuery.' + name );
			}

		}; /* END: function $.fn.wolfnetListingGrid */

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */