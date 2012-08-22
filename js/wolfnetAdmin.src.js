/**
 * This script is a general container for JavaScript code sepcific to the WordPress admin interface.
 *
 * @title         wolfnetAdmin.js
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

		/* Make sure there is always a scrollbar on the window so there is no jumping of content
		 * when sections are expanded and collapsed. */
		$( 'html' ).css( 'overflow-y', 'scroll' );

		$.fn.wolfnetFeaturedListingsControls = function ( options ) {

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			return this.each( function () {

				var  widgetCtrls = this;
				var $widgetCtrls = $( this );

				var $playMode   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayField:first' );
				var $autoFields = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayOptions:first' ).hide();

				var showAutoFields = function ()
				{
					$autoFields.disabled = false;
					$autoFields.show();
					$autoFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
				}

				var hideAutoFields = function ()
				{
					$autoFields.val( '' );
					$autoFields.disabled = true;
					$autoFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
						$autoFields.hide();
					} );
				}

				$playMode.change( function () {

					/* Automatic */
					if ( $( this ).val() == 'true' ) {
						showAutoFields();
					}
					/* Manual */
					else {
						hideAutoFields();
					}

				} );

				$playMode.trigger( 'change' );

			} );

		}

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */