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

		$( document ).ready( function () {

			/* simple browser detection */
			if ( navigator.appName == 'Microsoft Internet Explorer' ) {
				$( 'html' ).addClass( 'ie' );
			}

		} );

		$.fn.wolfnetFeaturedListingsControls = function ( options )
		{

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			var showAutoFields = function ( $autoFields )
			{
				$autoFields.disabled = false;
				$autoFields.show();
				$autoFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
			}

			var hideAutoFields = function ( $autoFields )
			{
				$autoFields.val( '' );
				$autoFields.disabled = true;
				$autoFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
					$autoFields.hide();
				} );
			}

			return this.each( function () {

				var $widgetCtrls = $( this );

				var $playMode   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayField:first' );
				var $autoFields = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayOptions:first' ).hide();

				$playMode.change( function () {

					/* Automatic */
					if ( $( this ).val() == 'true' ) {
						showAutoFields( $autoFields );
					}
					/* Manual */
					else {
						hideAutoFields( $autoFields );
					}

				} );

				$playMode.trigger( 'change' );

			} );

		}

		$.fn.wolfnetValidateProductKey = function ()
		{

			var validClass      = 'valid';
			var invalidClass    = 'invalid';
			var wrapperClass    = 'wolfnetProductKeyValidationWrapper';
			var validEvent      = 'validProductKey';
			var invalidEvent    = 'invalidProductKey';
			var validationEvent = 'validateProductKey';
			//var apiUri          = 'http://services.mlsfinder.com/validateKey/';
			var apiUri          = 'http://aj.cfdevel.wnt/com/mlsfinder/services/index.cfm/validateKey/';

			/* Validate that the key has the appropriate prefix. */
			var validatePrefix = function ( key )
			{
				if ( key.substring( 0, 3 ).toLowerCase() == 'wp_' ) {
					return true;
				}
				else {
					return false;
				}

			}

			/* Validate that the key is of an appropriate length. */
			var validateLength = function ( key )
			{
				if ( key.length == 35 ) {
					return true;
				}
				else {
					return false;
				}

			}

			/* Send the key to the API and validate that the key is active in mlsfinder.com */
			var validateViaApi = function ( input )
			{
				var $this    = $( input );
				var key      = $this.val();

				$.ajax( {
					url: apiUri + key + '.jsonp',
					dataType: 'jsonp',
					cache: false,
					success: function ( data ) {
						/* If no errors are returned the key is valid. */
						if ( !data.error.status ) {
							$this.trigger( validEvent );
						}
						/* If the validation failed because the key is disabled notify the user */
						else if ( data.error.status === true && data.error.message == 'Product Key is Disabled' ) {
							$this.trigger( invalidEvent );
							alert( 'The product key you have entered is currently disabled. Please contact customer services for more information.' );
						}
						else {
							$this.trigger( invalidEvent );
						}
					},
					error: function () {
						/* If the Ajax request failed notify the user that validation of the key was not possible. */
						$this.trigger( invalidEvent );
						alert( 'Unable to validate the product key at this time.' );
					}
				} );
			}

			/* This callback function is called whenever the validation event is trigger and takes
			 * any neccessary action to notify the user if the key is valid or not. */
			var onValidateEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				var key      = $this.val();

				if ( key != '' ) {
					if ( validatePrefix( key ) === true && validateLength( key ) === true ) {
						$this.trigger( validEvent );
						validateViaApi( this );
					}
					else {
						$this.trigger( invalidEvent );
					}
				}
				else {
					$wrapper.removeClass( validClass );
					$wrapper.removeClass( invalidClass );
				}
			}

			var onValidEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				$wrapper.addClass( validClass );
				$wrapper.removeClass( invalidClass );
			}

			var onInvalidEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				$wrapper.addClass( invalidClass );
				$wrapper.removeClass( validClass );
			}

			return this.each( function () {

				var $this = $( this );

				/* Ensure the plugin is only applied to input elements. */
				if ( this.nodeName.toLowerCase() != 'input' ) {
					throw "wolfnetValidateProductKey jQuery plugin can only be applied to an input element!"
				}
				else {

					/* Create an element to wrap the input field with. ( this will make styling easier ) */
					var $wrapper = $( '<span/>' );
					$wrapper.addClass( wrapperClass );

					/* Add the wrapper element to the DOM immediately after the input field. Then
					 * move the input field inside of the wrapper. */
					$this.after( $wrapper );
					$this.appendTo( $wrapper );

					/* Bind the some custom events to callback */
					$this.bind( validationEvent, onValidateEvent );
					$this.bind( validEvent, onValidEvent );
					$this.bind( invalidEvent, onInvalidEvent );

					/* Trigger the validation even every time a key is pressed in input field. */
					$this.keyup( function () {
						$this.trigger( validationEvent );
					} );

					/* Trigger the validation event when the document is ready. */
					$( document ).ready( function () {
						$this.trigger( validationEvent );
					} );

				}

			} );

		}

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */